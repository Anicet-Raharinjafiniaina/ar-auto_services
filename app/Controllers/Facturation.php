<?php

namespace App\Controllers;

use App\Libraries\Nuts;
use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Devis;
use App\Libraries\LibExcel;
use App\Controllers\BonDeCommande;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Facturation extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
        $user = new User();
        $user->checkTheme();
        $user->checkMenu();
    }

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(14);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Liste des bons de commande à valider";
        $sql = "SELECT 
                bc.id, 
                client_id, 
                CASE 
                    WHEN type_client = 1 THEN client_standard.nom || ' ' || client_standard.prenom
                    WHEN type_client = 2 THEN client_entreprise.libelle
                    ELSE NULL
                END AS client,
                date,
                bc.total AS total_bc,
                remise,
                nature_travaux,
                type_vehicule,
                immatriculation,
                net_a_payer,
                statut_id,
                numero_facture.id as num_facture
            FROM bc
            LEFT JOIN client_standard 
                ON client_standard.id = bc.client_id  
                AND client_standard.flag_suppression = 0 
                AND client_standard.actif = 1
            LEFT JOIN client_entreprise 
                ON client_entreprise.id = bc.client_id  
                AND client_entreprise.flag_suppression = 0 
                AND client_entreprise.actif = 1
            LEFT JOIN numero_facture 
                ON numero_facture.bc_id = bc.id  
            WHERE bc.statut_id != 2 AND bc.flag_suppression = 0
            ORDER BY id DESC
            ";
        $arr_bc =  $this->db->query($sql)->getResult();
        $arr['arr_bc'] = $arr_bc;
        echo view('facture/facturation_view', $arr);
    }

    public function  getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(14);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        $action = $this->request->getVar('action');
        $crud_bc = new CrudModel(TBL_BC);
        $arr_bc = $crud_bc->getDataById(["id" => $id]);
        $crud_bc_article = new CrudModel(TBL_BC_ARTICLE);
        $arr_bc_article = $crud_bc_article->getAllData(["bc_id" => $id, "flag_suppression" => 0]);
        $crud_bc_autre = new CrudModel(TBL_BC_AUTRE);
        $arr_bc_autre = $crud_bc_autre->getAllData(["bc_id" => $id, "flag_suppression" => 0]);
        $bc = new BonDeCommande();
        $arr['arr_article'] = $bc->getAllArticleWithPrix();
        $arr["action"] = $action;
        $arr["arr_bc"] = $arr_bc;
        $arr["arr_bc_article"] = $arr_bc_article;
        $arr["arr_bc_autre"] = $arr_bc_autre;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('facture/maj_facturation_view', $arr);
    }

    public function validerFacture()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(14);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        $validation = $this->request->getVar('validation'); // statut de la validation
        $montant_paye = $this->request->getVar('montant'); // montant payé
        $crud = new CrudModel(TBL_BC);
        if ($validation == 3) {
            $res = $this->checkQauntiteParArticle($id);
            if (!empty($res)) {
                return json_encode($res);
            }
            $this->majQuantiteStockParArticle($id);
            $this->createNumFacture($id);
            $arr_bc = $crud->getDataById(["id" => $id]);
            $crud->maj(["id" => $id], ['montant_paye' => $montant_paye, "restant_du" => ($arr_bc->net_a_payer - $montant_paye)], 0);
        }
        $result = $crud->maj(["id" => intval($id)], ["statut_id" => intval($validation), "date_validation" => date('Y-m-d')], 41);
        return json_encode($result);
    }

    public function createNumFacture($bc_id)
    {
        $crud = new CrudModel(TBL_NUM_FACTURE);
        $arr = $crud->getDataById(["bc_id" => $bc_id]);
        if (empty($arr)) {
            return $crud->create(["bc_id" => $bc_id], 0);
        }
    }

    public function majQuantiteStockParArticle($id)
    {
        $crud_bc_article = new CrudModel(TBL_BC_ARTICLE);
        $tools = new Tools();
        $arr_bc_article = $crud_bc_article->getAllData(["bc_id" => $id, "flag_suppression" => 0]);
        if (!empty($arr_bc_article)) {
            foreach ($arr_bc_article as $key => $value) {
                $tools->majQuantiteStock($value->article_id, $value->quantite);
            }
        }
    }

    public function checkQauntiteParArticle($id)
    {
        $crud_bc_article = new CrudModel(TBL_BC_ARTICLE);
        $crud_stock = new CrudModel(TBL_STOCK);
        $arr_bc_article = $crud_bc_article->getAllData(["bc_id" => $id, "flag_suppression" => 0]);
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_STOCK . '.article_id',
                'type' => 'left'
            )
        ];
        $arr = [];
        if (!empty($arr_bc_article)) {
            foreach ($arr_bc_article as $key => $value_article) {
                $arr_stock = $crud_stock->getDataById(["article_id" => $value_article->article_id, TBL_STOCK . ".flag_suppression" => 0], $arr_join, "article_id,quantite,libelle,reference");
                if ($arr_stock->quantite < $value_article->quantite) {
                    $arr = [$value_article->article_id, $arr_stock->reference . " " . $arr_stock->libelle, $arr_stock->quantite];
                    return $arr;
                }
            }
        }
        return $arr;
    }

    public function  viewOrDownloadPDF($id = null, $action = null)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(14);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        $action = $this->request->getVar('action');
        $crud_bc = new CrudModel(TBL_BC);
        $arr_bc = $crud_bc->getDataByIdArray(["id" => $id]);
        $crud_bc_article = new CrudModel(TBL_BC_ARTICLE);
        $arr_bc_article = $crud_bc_article->getAllDataArray(["bc_id" => $id, "flag_suppression" => 0]);
        $crud_bc_autre = new CrudModel(TBL_BC_AUTRE);
        $arr_bc_autre = $crud_bc_autre->getAllDataArray(["bc_id" => $id, "flag_suppression" => 0]);
        if ($action == "view") {
            $this->generateBc($action, $arr_bc, $arr_bc_article, $arr_bc_autre, false, $arr_bc['statut_id']);
        } else {
            $this->generateBc($action, $arr_bc, $arr_bc_article, $arr_bc_autre, true, $arr_bc['statut_id']);
        }
    }

    public function generateBc($action = null,  $arr_data = [], $articles = [], $autres = [], $download = false, $statut_id_validation = null)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(14);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        if (!empty($arr_data)) {
            if ($action == "view") {
                $download = false;
                $bc_id = $arr_data['id'];
            } else {
                $download = true;
                $bc_id = $arr_data['id'];
            }
            $devis  = new Devis();
            $arr_client = $devis->getClientDetail($arr_data['type_client'], $arr_data['client_id']);
            $nom_client_entrepise =  (!empty($arr_client) ? (isset($arr_client->libelle) ? "-" . $arr_client->libelle : "") : "");

            $arr_societe = $devis->getInfosSociete();
            $img =  'data:image/png;base64,' . (!empty($arr_societe) ? (isset($arr_societe->logo) ? $arr_societe->logo : "") : "");
            $html = '<table border="0" cellspacing="0" cellpadding="5" style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="width: 50%;">';
            $html .= '
                <table style="width: 100%; height: 100%;" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height: 10px;"></td> <!-- Espace vide en haut -->
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <img src="' . $img . '" width="80px">
                        </td>
                    </tr>
                </table>';
            $html .= '</td>';

            $html .= '<td style="width: 50%; text-align: right;">';

            // Boîte "Informations du Facture"
            $html .= '<table cellpadding="4" cellspacing="0" style="border: 1px solid #000; font-size: 10px; width:100%;">';
            $html .= '<tr bgcolor="#007BFF">';
            $html .= '<th colspan="2" style="color:white;text-align: center; border: 1px solid #000;"><b>Informations du PROFORMA</b></th>';
            $html .= '</tr>';
            $crud_facture  = new CrudModel(TBL_NUM_FACTURE);
            $arr_num_facture = $crud_facture->getDataById(["bc_id" => $bc_id]);
            $num_facture =  (!empty($arr_num_facture) ? (isset($arr_num_facture->id) ? $arr_num_facture->id : "XXXX") : "XXXX");
            $html .= '<tr><td colspan="2" style="border: none;"><b>N° Facture :</b> FA-' . str_pad($num_facture, 4, '0', STR_PAD_LEFT) . $nom_client_entrepise . "-" . date('Y') . " " . $arr_data['type_vehicule'] . " " . $arr_data['immatriculation'] . '</td></tr>';
            $html .= '<tr><td colspan="2" style="border: none;"><b>N° BC :</b> BC-' . str_pad($bc_id, 4, '0', STR_PAD_LEFT) . '</td></tr>';
            $html .= '<tr><td colspan="2" style="border: none;"><b>Date :</b> ' . date('d/m/Y', strtotime($arr_data['date'])) . '</td></tr>';
            $html .= '</table>';

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<br>';

            $html .= '<table border="0" cellpadding="3" cellspacing="0" style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="width: 50%; text-align: center;">';
            // Adresse entreprise
            $html .= '<b>' . (!empty($arr_societe) ? (isset($arr_societe->libelle) ? $arr_societe->libelle : "") : "") . '</b><br>';
            ((!empty($arr_societe) && ($arr_societe->adresse != "" || $arr_societe->adresse != null)) ? $html .=  $arr_societe->adresse . '<br>' : "");
            ((!empty($arr_societe) && ($arr_societe->ville != "" || $arr_societe->ville != null)) ? $html .= $arr_societe->ville . '<br>' : "");
            ((!empty($arr_societe) && ($arr_societe->nif != "" || $arr_societe->nif != null)) ? $html .= 'NIF : ' . $arr_societe->nif . '<br>' : "");
            ((!empty($arr_societe) && ($arr_societe->stat != "" || $arr_societe->stat != null)) ? $html .= 'STAT : ' . $arr_societe->stat . '<br>' : "");
            ((!empty($arr_societe) && ($arr_societe->rcs != "" || $arr_societe->rcs != null)) ? $html .= 'RCS : ' . $arr_societe->rcs . '<br>' : "");
            ((!empty($arr_societe) && ($arr_societe->telephone != "" || $arr_societe->telephone != null)) ? $html .= 'Tél : ' . $arr_societe->telephone . '<br>' : "");
            ((!empty($arr_societe) && ($arr_societe->adresse_mail != "" || $arr_societe->adresse_mail != null)) ? $html .= 'Email : ' . $arr_societe->adresse_mail . '<br>' : "");
            $html .= '</td>';

            $html .= '<td style="width: 50%; text-align: right;">';
            // Boîte "Informations client"
            $html .= '<table cellpadding="4" cellspacing="0" style="border: 1px solid #000; font-size: 10px; width:100%;">';
            $html .= '<tr bgcolor="#007BFF">';
            $html .= '<th style="color:white; text-align: center; border: 1px solid #000;"><b>Informations du client</b></th>';
            $html .= '</tr>';
            (!empty($arr_client) ? (!isset($arr_client->libelle) ? $html .= '<tr><td style="border: none;"><b>' . $arr_client->nom . " " . $arr_client->prenom . '</b></td></tr>' : "") : "");
            (!empty($arr_client) ? (isset($arr_client->libelle) ? $html .= '<tr><td style="border: none;"><b>' . $arr_client->libelle . '</b></td></tr>' : "") : "");
            ((!empty($arr_client) && ($arr_client->adresse != "" || $arr_client->adresse != null)) ? $html .= '<tr><td style="border: none;">Adresse : ' . $arr_client->adresse . '</td></tr>' : "");
            ((!empty($arr_client) && ($arr_client->mail != "" || $arr_client->mail != null)) ? $html .= '<tr><td style="border: none;">Email : ' . $arr_client->mail . '</td></tr>' : "");
            ((!empty($arr_client) && ($arr_client->contact != "" || $arr_client->contact != null)) ? $html .= '<tr><td style="border: none;">Tél :' . $arr_client->contact . '</td></tr>' : "");
            ((!empty($arr_client) && (isset($arr_client->nif)) && ($arr_client->nif != "" || $arr_client->nif != null)) ? $html .= '<tr><td style="border: none;">NIF : ' . $arr_client->nif . '</td></tr>' : "");
            ((!empty($arr_client) && (isset($arr_client->stat)) && ($arr_client->stat != "" || $arr_client->stat != null)) ? $html .= '<tr><td style="border: none;">STAT : ' . $arr_client->stat . '</td></tr>' : "");
            $html .= '</table>';

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            if (trim($arr_data['type_vehicule']) != "" && trim($arr_data['immatriculation'] != "")) {
                $html .= '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">';
                $html .= '<tr>';
                $html .= '<td><u>Type de véhicule </u>: ' . $arr_data['type_vehicule'] . '</td>';
                $html .= '<td><u>Immatriculation </u>: ' . $arr_data['immatriculation'] . '</td>';
                $html .= '</tr>';
                $html .= '</table>';
                $html .= '<br><br>';
            }
            ($arr_data['nature_travaux'] != "" ? $html .= '<u>Nature des travaux </u>: ' . $arr_data['nature_travaux'] : "");

            $html .= '<br><br>';

            $html .= '<table cellpadding="4" cellspacing="0" style="width:100%; border: 1px solid black; border-collapse: collapse;">';

            // En-tête
            $html .= '<thead>';
            $html .= '<tr bgcolor="#007BFF">';
            $html .= '<th align="center" valign="middle" style="color:white; width: 40%; border-top:1px solid #000;border-bottom:1px solid #000;"><b>Description</b></th>';
            $html .= '<th align="center" valign="middle" style="color:white; width: 20%; border-top:1px solid #000;border-bottom:1px solid #000;"><b>Quantité</b></th>';
            $html .= '<th align="center" valign="middle" style="color:white; width: 20%; border-top:1px solid #000;border-bottom:1px solid #000;"><b>Prix unitaire</b></th>';
            $html .= '<th align="center" valign="middle" style="color:white; width: 20%; border-top:1px solid #000;border-bottom:1px solid #000;"><b>Montant</b></th>';
            $html .= '</tr>';
            $html .= '</thead>';

            // Corps
            $html .= '<tbody>';

            if (!empty($articles)) {
                foreach ($articles as $key => $article) {
                    $quantite = str_replace(' ', '', $article['quantite']);
                    $prix = str_replace(' ', '', $article['prix_unitaire']);
                    $total_article = $quantite * $prix;

                    $html .= '<tr>';
                    $html .= '<td style="border: none; width: 40%; text-align: center;">' . $devis->getRefDesignationArticle($article['article_id']) . '</td>';
                    $html .= '<td style="border: none; width: 20%; text-align: center;">' . $quantite . '</td>';
                    $html .= '<td style="border: none; width: 20%; text-align: center;">' . number_format($prix, 2, ',', ' ') . ' <i>Ar</i></td>';
                    $html .= '<td style="border: none; width: 20%; text-align: center;">' . number_format($total_article, 2, ',', ' ') . ' <i>Ar</i></td>';
                    $html .= '</tr>';
                }
            }
            if (!empty($autres)) {
                foreach ($autres as $key => $autre) {
                    $description = $autre['description'];
                    $montant = str_replace(' ', '', $autre['montant']);
                    $html .= '<tr>';
                    $html .= '<td style="border: none; width: 40%; text-align: center;">' . ucfirst($description) . '</td>';
                    $html .= '<td style="border: none; width: 20%; text-align: center;"></td>';
                    $html .= '<td style="border: none; width: 20%; text-align: center;"></td>';
                    $html .= '<td style="border: none; width: 20%; text-align: center;">' . number_format($montant, 2, ',', ' ') . ' <i>Ar</i></td>';
                    $html .= '</tr>';
                }
            }
            $html .= '</tbody>';
            $html .= '</table>';

            $html .= '<table cellspacing="0" cellpadding="2" style="width: 100%; margin-top:10px; border-collapse: collapse;">
                <tr>
                    <td colspan="3" style="border: none;"></td>
                    <td style="background-color:#007BFF; color:white; text-align:right; width: 20%; border-left: 1px solid #000; ">
                        Total :
                    </td>
                    <td style="text-align:right; width: 20%; border-right: 1px solid #000; border-top: 1px solid #000;">
                        ' . number_format($arr_data['total'], 2, ',', ' ') . ' <i>Ar</i>
                    </td>
                </tr>';
            if ($arr_data['remise'] != 0) {
                $html .= '  <tr>
                    <td colspan="3" style="border: none;"></td>
                    <td style="background-color:#007BFF; color:white; text-align:right; border-left: 1px solid #000; ">
                        Remise :
                    </td>
                    <td style="text-align:right; border-right: 1px solid #000;">
                        ' . number_format($arr_data['remise'], 2, ',', ' ') . ' <i>%</i>
                    </td>
                </tr>';
            }
            $html .= '   <tr>
                    <td colspan="3" style="border: none;"></td>
                    <td style="background-color:#007BFF; color:white; font-weight:bold; text-align:right; border-left: 1px solid #000; border-bottom: 1px solid #000; ">
                        Total à payer TTC:
                    </td>
                    <td style="text-align:right; font-weight:bold; border-right: 1px solid #000; border-bottom: 1px solid #000; ">
                        ' . number_format(str_replace(' ', '',  $arr_data['net_a_payer']), 2, ',', ' ') . ' <i>Ar</i>
                    </td>
                </tr>
            </table>';

            $net_abs = abs(str_replace(' ', '',  $arr_data['net_a_payer']));
            $net_a_payer_lettre = "";
            if (intval($net_abs) == $net_abs) {
                $obj = new Nuts((intval($net_abs) == $net_abs) ? intval($net_abs) : number_format($net_abs, 2, ',', ''), "virgule");
                $net_a_payer_lettre = $obj->convert("fr-FR");
                $net_a_payer_lettre = str_replace("virgule", "", $net_a_payer_lettre);
            } else {
                $obj = new Nuts((intval($net_abs) == $net_abs) ? intval($net_abs) : number_format($net_abs, 2, ',', ''), "virgule");
                $net_a_payer_lettre = $obj->convert("fr-FR");
            }

            $html .= '<br><br><br><br><br>';
            $html .= '<table cellspacing="0" cellpadding="2" style="width: 100%; margin-top:10px; border-collapse: collapse;">
                <tr> <td>
                   Arrêtée la présente facture à la somme de  : ' . ucfirst($net_a_payer_lettre) . ' Ariary.    </td></tr>';

            $restant_du = ($arr_data['net_a_payer']  - $arr_data['montant_paye']);
            if ($arr_data['net_a_payer'] != $arr_data['montant_paye'] && $arr_data['statut_id'] == 3) {
                $html .= ' <tr> <td>';
                $html .= '  Acompte : ' . (
                    fmod($arr_data['montant_paye'], 1) == 0
                    ? number_format($arr_data['montant_paye'], 0, ',', ' ') . ' Ar'
                    : number_format($arr_data['montant_paye'], 2, ',', ' ') . ' Ar'
                ) . '<br>' .
                    '  Restant dû : ' . (
                        fmod($restant_du, 1) == 0
                        ? number_format($restant_du, 0, ',', ' ') . ' Ar'
                        : number_format($restant_du, 2, ',', ' ') . ' Ar'
                    );
                $html .= '</td></tr>';
            }

            $html .= '<tr><td><br><b>  Règlement par chèque ou par virement sur le compte de ' . (!empty($arr_societe) ? (isset($arr_societe->libelle) ? str_ireplace("sarl", "", $arr_societe->libelle) : "") : "") . ' : ' .  ((!empty($arr_societe) && (isset($arr_societe->compte_bancaire)) && ($arr_societe->compte_bancaire != "" || $arr_societe->compte_bancaire != null)) ?  $arr_societe->compte_bancaire : "") . " " . ((!empty($arr_societe) && (isset($arr_societe->banque)) && ($arr_societe->banque != "" || $arr_societe->banque != null)) ?  '(' . $arr_societe->banque . ')' : "") . '</b>
                   </td></tr>
            </table>';
            $html .= '<br><br>
            <table cellspacing="0" cellpadding="2" style="width: 100%; margin-top:10px; border-collapse: collapse;">
                <tr>
                  <td style="width: 24%;"><b><u>Bon pour accord du client</u></b></td>
                  <td colspan="3"></td>                                                
                   <td><b><u>Le responsable</u></b></td>                  
                </tr>
            </table>';

            $devis->generatePDF($html,  'FA-' . str_pad($bc_id, 4, '0', STR_PAD_LEFT) . $nom_client_entrepise . "-" . date('Y') . " " . $arr_data['type_vehicule'] . " " . $arr_data['immatriculation'] . '.pdf',  $download, $statut_id_validation);
        } else {
            echo "<b>Erreur lors de l'affichage du bc, des données sont probablement corrompues.<b>";
        }
    }
}
