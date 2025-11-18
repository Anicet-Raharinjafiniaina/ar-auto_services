<?php

namespace App\Controllers;

use TCPDF;
use DateTime;
use App\Libraries\Nuts;
use App\Models\CrudModel;
use App\Controllers\Acces;

class Devis extends BaseController
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
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Liste des devis";
        $arr['arr_article'] = $this->getAllArticleWithPrix();
        $sql = "SELECT 
                devis.id, 
                type_client, 
                client_id, 
                CASE 
                    WHEN type_client = 1 THEN client_standard.nom || ' ' || client_standard.prenom
                    WHEN type_client = 2 THEN client_entreprise.libelle
                    ELSE NULL
                END AS client,
                date,
                validite,
                date_fin,
                devis.total AS total_devis,
                remise,
                nature_travaux,
                type_vehicule,
                immatriculation,
                net_a_payer
            FROM devis
            LEFT JOIN client_standard 
                ON client_standard.id = devis.client_id  
                AND client_standard.flag_suppression = 0 
                AND client_standard.actif = 1
            LEFT JOIN client_entreprise 
                ON client_entreprise.id = devis.client_id  
                AND client_entreprise.flag_suppression = 0 
                AND client_entreprise.actif = 1
            WHERE devis.flag_suppression = 0
            ORDER BY id DESC
            ";
        $arr_devis =  $this->db->query($sql)->getResult();
        $arr['arr_devis'] = $arr_devis;
        $token = bin2hex(random_bytes(16));
        session()->set('form_token', $token);
        $arr['token'] = $token;
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('facture/devis_view', $arr);
            return;
        }
        echo view('facture/devis_view', $arr);
    }

    public function getVehicule()
    {
        $crud = new CrudModel(TBL_VEHICULE);
        $res = $crud->getAllData(['flag_suppression' => 0], [], "libelle", "libelle", "", "asc");
        $arr = [];
        foreach ($res as $k => $v) :
            array_push($arr, $v->libelle);
        endforeach;
        return json_encode($arr);
    }

    public function getAllArticleWithPrix()
    {
        $crud = new CrudModel(TBL_TARIFICATION);
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_TARIFICATION . '.article_id',
                'type' => 'left'
            )
        ];
        $arr = $crud->getAllData(array(TBL_ARTICLE . ".flag_suppression" => 0, TBL_ARTICLE . ".actif" => 1, TBL_TARIFICATION . ".flag_suppression" => 0, TBL_TARIFICATION . ".actif" => 1), $arr_join, TBL_ARTICLE . ".id,reference,libelle");
        return $arr;
    }

    public function getPrixArticle()
    {
        $type_client = $this->request->getPost('type_client');
        $article_id = $this->request->getPost('article_id');
        $crud = new CrudModel(TBL_TARIFICATION);
        $arr = [];
        if ($type_client == 1) {
            $arr = $crud->getDataById(array("article_id" => $article_id, "flag_suppression" => 0, "actif" => 1), [], "prix_client_standard as prix");
        }
        if ($type_client == 2) {
            $arr = $crud->getDataById(array("article_id" => $article_id, "flag_suppression" => 0, "actif" => 1), [], "prix_client_entreprise as prix");
        }
        return (!empty($arr->prix) ? json_encode($arr->prix) : json_encode(0));
    }


    function generatePDF($htmlContent, $filename = 'document.pdf', $download = false, $valide = null)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        if ($valide != 3) { // --- Filigrane en arrière-plan ---
            $pdf = new PDFWithWatermark();
            $pdf->setPrintHeader(true);   // important pour exécuter Header()
        }
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->writeHTML($htmlContent, true, false, true, false, '');
        // Nettoyer le tampon de sortie si besoin
        if (ob_get_length()) {
            ob_end_clean();
        }
        if (!$download) {
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
        }

        $pdf->Output($filename, $download ? 'D' : 'I');
        exit;
    }

    public function saveDevis()
    {
        $validite = $this->request->getPost('validite');
        $kilometrage_garantie = $this->request->getPost('garantie');
        $type_client = $this->request->getPost('type_client');
        $client_id = $this->request->getPost('client');
        $articles = $this->request->getPost('article');
        $autres = $this->request->getPost('autre');
        $total = str_replace(" ", "", $this->request->getPost('total'));
        $net_a_payer = str_replace(" ", "", $this->request->getPost('net_a_payer'));
        $remise = floatval($this->request->getPost('remise'));
        $nature_travaux = $this->request->getPost('nature_travaux');
        $duree_travaux = $this->request->getPost('duree_travaux');
        $type_vehicule = $this->request->getPost('type_vehicule');
        $immatriculation = $this->request->getPost('immatriculation');

        $arr_data['type_client'] =  $type_client;
        $arr_data['client_id'] =  $client_id;
        $arr_data['validite'] =  $validite;
        $arr_data['kilometrage_garantie'] =  $kilometrage_garantie;
        $arr_data['date'] =  date('Y-m-d');
        $arr_data['date_fin'] = $this->ajouterJours($validite, 1);
        $arr_data['total'] =  $total;
        $arr_data['net_a_payer'] =  $net_a_payer;
        $arr_data['remise'] =  $remise;
        $arr_data['nature_travaux'] =  $nature_travaux;
        $arr_data['duree_travaux'] =  $duree_travaux;
        $arr_data['type_vehicule'] =  $type_vehicule;
        $arr_data['immatriculation'] =  $immatriculation;

        $devis_id = $this->insert($arr_data, $articles, $autres);
        return json_encode($devis_id);
    }

    public function generateDevis($action = null,  $arr_data = [], $articles = [], $autres = [], $download = false)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        if (!empty($arr_data)) {
            if ($action == "view") {
                $download = false;
                $devis_id = $arr_data['id'];
            } else {
                $download = true;
                $devis_id = $arr_data['id'];
            }

            $arr_client = $this->getClientDetail($arr_data['type_client'], $arr_data['client_id']);
            $nom_client_entrepise =  (!empty($arr_client) ? (isset($arr_client->libelle) ? "-" . $arr_client->libelle : "") : "");

            $arr_societe = $this->getInfosSociete();
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

            // Boîte "Informations du DEVIS"
            $html .= '<table cellpadding="4" cellspacing="0" style="border: 1px solid #000; font-size: 10px; width:100%;">';
            $html .= '<tr bgcolor="#007BFF">';
            $html .= '<th colspan="2" style="color:white;text-align: center; border: 1px solid #000;"><b>Informations du PROFORMA</b></th>';
            $html .= '</tr>';
            $html .= '<tr><td colspan="2" style="border: none;"><b>N° :</b> PF-' . str_pad($devis_id, 4, '0', STR_PAD_LEFT) . $nom_client_entrepise . "-" . date('Y') . " " . $arr_data['type_vehicule'] . " " . $arr_data['immatriculation'] . '</td></tr>';
            $html .= '<tr><td colspan="2" style="border: none;"><b>Date d\'émission :</b> ' . date('d/m/Y') . '</td></tr>';
            $html .= '<tr><td colspan="2" style="border: none;"><b>Valide pendant :</b> ' . str_pad($arr_data['validite'], 2, '0', STR_PAD_LEFT)    . ' jours</td></tr>';
            $html .= '<tr><td colspan="2" style="border: none;"><b>Valable jusqu\'au :</b> ' . $this->ajouterJours($arr_data['validite'], 2) . '</td></tr>';
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
                    $html .= '<td style="border: none; width: 40%; text-align: center;">' . $this->getRefDesignationArticle($article['article_id']) . '</td>';
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
                <tr>
                   <td>
                   Arrêtée la présente facture à la somme de  : ' . ucfirst($net_a_payer_lettre) . ' Ariary.';
            ($arr_data['kilometrage_garantie'] != 0 ? ' <br><b>Garantie des travaux : ' . $arr_data['kilometrage_garantie'] . ' km</b>' : "");
            ($arr_data['duree_travaux'] != 0) ? $html .= ' <br><br> <b> Durée approximative des travaux : ' . str_pad($arr_data['duree_travaux'], 2, '0', STR_PAD_LEFT)   . ' jours ouvrables, après confirmation du présent devis</b>' : "";
            $html .= ' <br><br><b>  Règlement par chèque ou par virement sur le compte de ' . (!empty($arr_societe) ? (isset($arr_societe->libelle) ? str_ireplace("sarl", "", $arr_societe->libelle) : "") : "") . ' : ' .  ((!empty($arr_societe) && (isset($arr_societe->compte_bancaire)) && ($arr_societe->compte_bancaire != "" || $arr_societe->compte_bancaire != null)) ?  $arr_societe->compte_bancaire : "") . " " . ((!empty($arr_societe) && (isset($arr_societe->banque)) && ($arr_societe->banque != "" || $arr_societe->banque != null)) ?  '(' . $arr_societe->banque . ')' : "") . '</b>
                   </td>
                </tr>
            </table>';
            $html .= '<br><br>
            <table cellspacing="0" cellpadding="2" style="width: 100%; margin-top:10px; border-collapse: collapse;">
                <tr>
                  <td style="width: 24%;"><b><u>Bon pour accord du client</u></b></td>
                  <td colspan="3"></td>                                                
                   <td><b><u>Le responsable</u></b></td>                  
                </tr>
            </table>';

            $this->generatePDF($html,  'PF-' . str_pad($devis_id, 4, '0', STR_PAD_LEFT) . $nom_client_entrepise . "-" . date('Y') . " " . $arr_data['type_vehicule'] . " " . $arr_data['immatriculation'] . '.pdf',  $download, 0);
        } else {
            echo "<b>Erreur lors de l'affichage du devis, des données sont probablement corrompues.<b>";
        }
    }

    public function  viewOrDownloadPDF($id = null, $action = null)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        $action = $this->request->getVar('action');
        $crud_devis = new CrudModel(TBL_DEVIS);
        $arr_devis = $crud_devis->getDataByIdArray(["id" => $id]);
        $crud_devis_article = new CrudModel(TBL_DEVIS_ARTICLE);
        $arr_devis_article = $crud_devis_article->getAllDataArray(["devis_id" => $id, "flag_suppression" => 0]);
        $crud_devis_autre = new CrudModel(TBL_DEVIS_AUTRE);
        $arr_devis_autre = $crud_devis_autre->getAllDataArray(["devis_id" => $id, "flag_suppression" => 0]);
        if ($action == "view") {
            $this->generateDevis($action, $arr_devis, $arr_devis_article, $arr_devis_autre, false);
        } else {
            $this->generateDevis($action, $arr_devis, $arr_devis_article, $arr_devis_autre, true);
        }
    }

    public function modelpdf()
    {
        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $img = base_url('assets/images/logo/images.png');
        $img = "";
        $html = '<table border="0" cellspacing="0" cellpadding="5" style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td style="width: 50%;">';
        $html .= '
                <table style="width: 100%; height: 100px;" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height: 50px;"></td> <!-- Espace vide en haut -->
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <img src="' . $img . '" height="50">
                        </td>
                    </tr>
                </table>';
        $html .= '</td>';

        $html .= '<td style="width: 50%; text-align: right;">';

        // Boîte "Informations du DEVIS"
        $html .= '<table cellpadding="4" cellspacing="0" style="border: 1px solid #000; font-size: 10px; width:100%;">';
        $html .= '<tr bgcolor="#007BFF">';
        $html .= '<th colspan="2" style="color:white;text-align: center; border: 1px solid #000;"><b>Informations du DEVIS</b></th>';
        $html .= '</tr>';
        $html .= '<tr><td colspan="2" style="border: none;"><b>N° de devis :</b> D-2024-06-234</td></tr>';
        $html .= '<tr><td colspan="2" style="border: none;"><b>Date d\'émission :</b> 10/06/2024</td></tr>';
        $html .= '<tr><td colspan="2" style="border: none;"><b>Valide pendant :</b> 90 jours</td></tr>';
        $html .= '<tr><td colspan="2" style="border: none;"><b>Valable jusqu\'au :</b> 08/09/2024</td></tr>';
        $html .= '</table>';

        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br>';

        $html .= '<table border="0" cellpadding="3" cellspacing="0" style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td style="width: 50%; text-align: center;">';
        // Adresse entreprise
        $html .= '<b>Nom Commercial - Nom Prénom (EI)</b><br>';
        $html .= '1 rue de l\'exemple<br>';
        $html .= '00000 Exemple<br>';
        $html .= '01.23.45.67.89<br>';
        $html .= 'votre@mail.com';
        $html .= '</td>';
        $html .= '<td style="width: 50%; text-align: right;">';

        // Boîte "Informations client"
        $html .= '<table cellpadding="4" cellspacing="0" style="border: 1px solid #000; font-size: 10px; width:100%;">';
        $html .= '<tr bgcolor="#007BFF">';
        $html .= '<th style="color:white; text-align: center; border: 1px solid #000;"><b>Informations client</b></th>';
        $html .= '</tr>';
        $html .= '<tr><td style="border: none;">';
        $html .= 'M. Votre Client<br>';
        $html .= '1 rue de votre client<br>';
        $html .= '11111 Code Postal';
        $html .= '</td></tr>';
        $html .= '</table>';

        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br><br>';

        $html .= '<table border="1" cellpadding="4" cellspacing="0">';
        $html .= '<thead>';
        $html .= '<tr bgcolor="#007BFF">';
        $html .= '<th align="center" valign="middle" style="color:white; width: 40%;"><b>Description</b></th>';
        $html .= '<th align="center" valign="middle" style="color:white; width: 20%;"><b>Quantité</b></th>';
        $html .= '<th align="center" valign="middle" style="color:white; width: 20%;"><b>Prix unitaire</b></th>';
        $html .= '<th align="center" valign="middle" style="color:white; width: 20%;"><b>Total</b></th>';
        $html .= '</tr>';
        $html .= '</thead>';

        $html .= '<tbody>';

        $html .= '<tr style="text-align :center">';
        $html .= '<td style="width: 40%;">Ajoutez ici le titre de votre article : Et si vous le</td>';
        $html .= '<td style="width: 20%;" align="center">1</td>';
        $html .= '<td style="width: 20%;" align="center">15 000 000 000,55</td>';
        $html .= '<td style="width: 20%;" align="center">100,00</td>';
        $html .= '</tr>';

        $html .= '</tbody>';
        $html .= '</table>';

        $html .= '<table cellspacing="0" cellpadding="4" border="0" style="width: 100%; margin-top:10px;">
                    <tr>
                        <td colspan="3"></td>
                        <td style="background-color:#007BFF; color:white; text-align:right; width: 25%;">
                            Prix total :
                        </td>
                        <td style="border:1px solid #007BFF; text-align:right; width: 15%;">
                            150,00 €
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td style="background-color:#007BFF; color:white; text-align:right;">
                            Remise :
                        </td>
                        <td style="border:1px solid #007BFF; text-align:right;">
                            10,00 €
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td style="background-color:#007BFF; color:white; font-weight:bold; text-align:right;">
                            Prix total à payer :
                        </td>
                        <td style="border:1px solid #007BFF; text-align:right; font-weight:bold;">
                            140,00 €
                        </td>
                    </tr>
                </table>
                ';

        header('Content-Type: application/pdf');
        header('Cache-Control: max-age=0');
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('devis.pdf', 'I');
        exit;
    }

    public function getAllClient()
    {
        $type = trim($this->request->getVar('type_client'));
        $arr = [];
        if ($type == 1) {
            $crud = new CrudModel(TBL_CLIENT_STANDARD);
            $arr = $crud->getAllData(["actif" => 1, "flag_suppression" => 0], [],  "id,'C' || LPAD(id::text, 4, '0') || ' - ' || nom || ' ' || prenom AS text");
        }
        if ($type == 2) {
            $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
            $arr = $crud->getAllData(["actif" => 1, "flag_suppression" => 0], [],  "id,'C' || LPAD(id::text, 4, '0') || ' - ' || libelle as text");
        }
        return  json_encode($arr);
    }

    public function getClientDetail($type, $id)
    {
        $arr = [];
        if ($type == 1) {
            $crud = new CrudModel(TBL_CLIENT_STANDARD);
            $arr = $crud->getDataById(["id" => $id], [],  "id,nom,prenom,adresse,contact,mail");
        }
        if ($type == 2) {
            $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
            $arr = $crud->getDataById(["id" => $id], [],  "id, libelle,adresse,contact,mail,nif,stat");
        }
        return $arr;
    }

    public function getRefDesignationArticle($id)
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arr = $crud->getDataById(["id" => $id], [],  "id,reference,libelle");
        return (!empty($arr) ? $arr->reference . " - " . ucfirst($arr->libelle) : "");
    }

    public function getInfosSociete()
    {
        $crud = new CrudModel(TBL_SOCIETE);
        $arr = $crud->getDataById(["flag_suppression" => 0]);
        return $arr;
    }

    function ajouterJours($nbJours, $type)
    {
        $date = new DateTime(); // aujourd'hui
        $date->modify("+$nbJours days");
        if ($type == 1) {
            return $date->format('Y-m-d');
        }
        if ($type == 2) {
            return $date->format('d/m/Y');
        }
    }

    public function insert($arr_data, $arr_article, $arr_autre)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $msg = "";
        if (!empty($arr_data)) {
            $crud = new CrudModel(TBL_DEVIS);
            $result = $crud->create($arr_data, 31);
            if (!empty($arr_article) && $result == 1) {
                $arr_devis = $crud->getAllData(array('flag_suppression' => 0), [], 'id', 'id', "", 'desc', 1, 1);
                foreach ($arr_article as $key => $article) {
                    $article['devis_id'] = $arr_devis[0]->id;
                    $article['prix_unitaire'] = str_replace(" ", "", $article['prix_unitaire']);
                    $article['total'] = ($article['prix_unitaire'] * $article['quantite']);
                    $crud_article = new CrudModel(TBL_DEVIS_ARTICLE);
                    $result = $crud_article->create($article, 0);
                }
                $msg = $arr_devis[0]->id;
            }
            if (!empty($arr_autre) && $result == 1) {
                $arr_devis = $crud->getAllData(array('flag_suppression' => 0), [], 'id', 'id', "", 'desc', 1, 1);
                foreach ($arr_autre as $key => $autre) {
                    $autre['devis_id'] = $arr_devis[0]->id;
                    $autre['montant'] = str_replace(" ", "", $autre['montant']);
                    $crud_autre = new CrudModel(TBL_DEVIS_AUTRE);
                    $result = $crud_autre->create($autre, 0);
                }
            }
            $msg = $arr_devis[0]->id;
        }
        return $msg;
    }

    public function deleteDevis()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_DEVIS);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 33);
            if ($result == 1) {
                $crud_article = new CrudModel(TBL_DEVIS_ARTICLE);
                $crud_article->del(["devis_id" => $id], ["flag_suppression" => 1], 0);
                $crud_autre = new CrudModel(TBL_DEVIS_AUTRE);
                $crud_autre->del(["devis_id" => $id], ["flag_suppression" => 1], 0);
            }

            return json_encode($result);
        }
        return json_encode(0);
    }
}
class PDFWithWatermark extends TCPDF
{
    public function Header()
    {
        // Filigrane derrière tout le contenu
        $this->SetFont('Helvetica', 'B', 80);
        $this->SetTextColor(200, 200, 200);
        $this->SetAlpha(0.45); // transparence

        $this->StartTransform();

        // Coordonnées pour le centre + remontée
        $x = $this->GetPageWidth() / 2;
        $y = $this->GetPageHeight() / 2.5; // <-- plus petit = plus haut

        $this->Rotate(45, $x, $y);

        // Texte centré sur x, y
        $txt = 'NON VALIDÉ';
        $this->SetXY($x - ($this->GetStringWidth($txt) / 2), $y - 10);
        $this->Cell(0, 0, $txt, 0, 0, 'C', false, '', 0, false, 'T', 'M');

        $this->StopTransform();
        $this->SetAlpha(1);
    }
}
