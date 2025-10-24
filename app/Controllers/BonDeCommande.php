<?php

namespace App\Controllers;

use TCPDF;
use DateTime;
use App\Libraries\Nuts;
use App\Models\CrudModel;
use App\Controllers\Acces;

class BonDeCommande extends BaseController
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
        $is_ok = $acces->is_ok(13);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Liste des bons de commande";
        $arr['arr_article'] = $this->getAllArticleWithPrix();
        $sql = "SELECT 
                bc.id, 
                type_client, 
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
                statut_id
            FROM bc
            LEFT JOIN client_standard 
                ON client_standard.id = bc.client_id  
                AND client_standard.flag_suppression = 0 
                AND client_standard.actif = 1
            LEFT JOIN client_entreprise 
                ON client_entreprise.id = bc.client_id  
                AND client_entreprise.flag_suppression = 0 
                AND client_entreprise.actif = 1
            WHERE bc.flag_suppression = 0
            ORDER BY id DESC
            ";
        $arr_bc =  $this->db->query($sql)->getResult();
        $arr['arr_bc'] = $arr_bc;
        echo view('parc_auto/bc_view', $arr);
    }

    public function saveBc()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(13);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $date = $this->request->getPost('date');
        $date =  $tool->normalizeDate($date);
        $type_client = $this->request->getPost('type_client');
        $client_id = $this->request->getPost('client');
        $articles = $this->request->getPost('article');
        $autres = $this->request->getPost('autre');
        $total = str_replace(" ", "", $this->request->getPost('total'));
        $net_a_payer = str_replace(" ", "", $this->request->getPost('net_a_payer'));
        $remise = floatval($this->request->getPost('remise'));
        $nature_travaux = $this->request->getPost('nature_travaux');
        $type_vehicule = $this->request->getPost('type_vehicule');
        $immatriculation = $this->request->getPost('immatriculation');

        $arr_data['type_client'] =  $type_client;
        $arr_data['client_id'] =  $client_id;
        $arr_data['date'] =  $date;
        $arr_data['total'] =  $total;
        $arr_data['net_a_payer'] =  $net_a_payer;
        $arr_data['remise'] =  $remise;
        $arr_data['nature_travaux'] =  $nature_travaux;
        $arr_data['type_vehicule'] =  $type_vehicule;
        $arr_data['immatriculation'] =  $immatriculation;
        $res = $this->insert($arr_data, $articles, $autres);
        return json_encode($res);
    }

    public function  getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(13);
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
        $arr["action"] = $action;
        $arr['arr_article'] = $this->getAllArticleWithPrix();
        $arr["arr_bc"] = $arr_bc;
        $arr["arr_bc_article"] = $arr_bc_article;
        $arr["arr_bc_autre"] = $arr_bc_autre;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('parc_auto/maj_bc_view', $arr);
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

    public function insert($arr_data, $arr_article, $arr_autre)
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(13);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $msg = "";
        if (!empty($arr_data)) {
            $crud = new CrudModel(TBL_BC);
            $result = $crud->create($arr_data, 38);
            if (!empty($arr_article) && $result == 1) {
                $arr_bc = $crud->getAllData(array('flag_suppression' => 0), [], 'id', 'id', "", 'desc', 1, 1);
                foreach ($arr_article as $key => $article) {
                    $article['bc_id'] = $arr_bc[0]->id;
                    $article['prix_unitaire'] = str_replace(" ", "", $article['prix_unitaire']);
                    $article['total'] = ($article['prix_unitaire'] * $article['quantite']);
                    $crud_article = new CrudModel(TBL_BC_ARTICLE);
                    $result = $crud_article->create($article, 0);
                }
                $msg = $result;
            }
            if (!empty($arr_autre) && $result == 1) {
                $arr_bc = $crud->getAllData(array('flag_suppression' => 0), [], 'id', 'id', "", 'desc', 1, 1);
                foreach ($arr_autre as $key => $autre) {
                    $autre['bc_id'] = $arr_bc[0]->id;
                    $autre['montant'] = str_replace(" ", "", $autre['montant']);
                    $crud_autre = new CrudModel(TBL_BC_AUTRE);
                    $result = $crud_autre->create($autre, 0);
                }
            }
            $msg = $result;
        }
        return $msg;
    }

    public function majBc()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(13);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $id = $this->request->getPost('id_upd');
        $date = $this->request->getPost('date_upd');
        $date = $tool->normalizeDate($date);
        $type_client = $this->request->getPost('type_client_upd');
        $client_id = $this->request->getPost('client_id_upd');
        $articles = $this->request->getPost('article');
        $autres = $this->request->getPost('autre');
        $total = str_replace(" ", "", $this->request->getPost('total_upd'));
        $net_a_payer = str_replace(" ", "", $this->request->getPost('net_a_payer_upd'));
        $remise = floatval($this->request->getPost('remise_upd'));
        $nature_travaux = $this->request->getPost('nature_travaux_upd');
        $type_vehicule = $this->request->getPost('type_vehicule_upd');
        $immatriculation = $this->request->getPost('immatriculation_upd');

        $arr_data['id'] =  $id;
        $arr_data['type_client'] =  $type_client;
        $arr_data['client_id'] =  $client_id;
        $arr_data['date'] =  $date;
        $arr_data['total'] =  $total;
        $arr_data['net_a_payer'] =  $net_a_payer;
        $arr_data['remise'] =  $remise;
        $arr_data['nature_travaux'] =  $nature_travaux;
        $arr_data['type_vehicule'] =  $type_vehicule;
        $arr_data['immatriculation'] =  $immatriculation;

        $crud_bc = new CrudModel(TBL_BC);
        $arr_bc = $crud_bc->getDataByIdArray(["id" => $id]);
        $crud_article = new CrudModel(TBL_BC_ARTICLE);
        $arr_article = $crud_article->getAllDataArray(["bc_id" => $id, "flag_suppression" => 0], [], "id,bc_id,article_id,quantite,prix_unitaire");
        $crud_autre = new CrudModel(TBL_BC_AUTRE);
        $arr_autre = $crud_autre->getAllDataArray(["bc_id" => $id, "flag_suppression" => 0], [], "id,bc_id,description,montant");

        $arr_bc_modifie = $this->getModifiedRow($arr_bc, $arr_data);
        $arr_article_modifie = $this->compareFormWithDatabase($arr_article, $articles);
        $arr_autre_modifie = $this->compareFormWithDatabase($arr_autre, $autres);

        $result = "";
        if (empty($arr_bc_modifie) && empty($arr_article_modifie['inserts']) && empty($arr_article_modifie['updates']) && empty($arr_article_modifie['deletes']) && empty($arr_autre_modifie['inserts']) && empty($arr_autre_modifie['updates']) && empty($arr_autre_modifie['deletes'])) {
            return json_encode(2); // aucune modification
        } else {
            $arr_data['statut_id'] = 1;
            unset($arr_data['id']);
            $result = $crud_bc->maj(["id" => $id], $arr_data, 39);
            if ($result != 1) {
                return json_encode(0); // erreur
            }
            if (!empty($arr_article_modifie)) {
                if (!empty($arr_article_modifie['inserts'])) {
                    foreach ($arr_article_modifie['inserts'] as $key => $value) {
                        $value['total'] =  $value['quantite'] *  $value['prix_unitaire'];
                        $result = $crud_article->create($value, 0);
                        if ($result != 1) {
                            return json_encode(0); // erreur
                        }
                    }
                }
                if (!empty($arr_article_modifie['updates'])) {
                    foreach ($arr_article_modifie['updates'] as $key => $val) {
                        $id_to_upd =  $val['id'];
                        unset($val['id']);
                        $val['total'] =  $val['quantite'] *  $val['prix_unitaire'];
                        $result = $crud_article->maj(["id" => $id_to_upd], $val, 0);
                        if ($result != 1) {
                            return json_encode(0); // erreur
                        }
                    }
                }
                if (!empty($arr_article_modifie['deletes'])) {
                    foreach ($arr_article_modifie['deletes'] as $key => $v) {
                        $result = $crud_article->maj(["id" => $v['id']], ['flag_suppression' => 1], 0);
                        if ($result != 1) {
                            return json_encode(0); // erreur
                        }
                    }
                }
            }

            if (!empty($arr_autre_modifie)) {
                if (!empty($arr_autre_modifie['inserts'])) {
                    foreach ($arr_autre_modifie['inserts'] as $key => $value_autre) {
                        $result = $crud_autre->create($value_autre, 0);
                        if ($result != 1) {
                            return json_encode(0); // erreur
                        }
                    }
                }
                if (!empty($arr_autre_modifie['updates'])) {
                    foreach ($arr_autre_modifie['updates'] as $key => $val_autre) {
                        $id_to_upd =  $val_autre['id'];
                        unset($val_autre['id']);
                        $result = $crud_autre->maj(["id" => $id_to_upd], $val_autre, 0);
                        if ($result != 1) {
                            return json_encode(0); // erreur
                        }
                    }
                }
                if (!empty($arr_autre_modifie['deletes'])) {
                    foreach ($arr_autre_modifie['deletes'] as $key => $v_autre) {
                        $result = $crud_autre->maj(["id" => $v_autre['id']], ['flag_suppression' => 1], 0);
                        if ($result != 1) {
                            return json_encode(0); // erreur
                        }
                    }
                }
            }
        }
        return json_encode($result);
    }

    /**
     * tableau à une dimension
     */
    public function getModifiedRow($dbRow, $formRow)
    {
        $modified = [];

        foreach ($formRow as $key => $value) {
            $formValue = is_string($value) ? str_replace(' ', '', trim($value)) : $value;
            $dbValue   = $dbRow[$key] ?? null;
            if (is_string($dbValue)) {
                $dbValue = str_replace(' ', '', trim($dbValue));
            }

            if ($formValue != $dbValue) {
                // on marque la ligne comme modifiée
                $modified = $formRow;
                break; // une seule différence suffit pour retourner la ligne complète
            }
        }

        return $modified; // tableau vide si aucune modification
    }

    /** 
     * tableau à 2 dimension
     */
    public function compareFormWithDatabase($dbData, $formData)
    {

        if (is_array($formData) && !empty($formData)) {
            $updates = [];
            $inserts = [];
            $deletes = [];

            // Normaliser le formulaire : enlever _upd et supprimer espaces
            $normalizeForm = function ($row) {
                $clean = [];
                foreach ($row as $key => $value) {
                    $cleanKey = str_replace('_upd', '', $key);
                    if (is_string($value)) {
                        $cleanValue = str_replace(' ', '', trim($value)); // supprime espaces
                    } else {
                        $cleanValue = $value;
                    }
                    $clean[$cleanKey] = $cleanValue;
                }
                return $clean;
            };

            // Indexer DB par id
            $dbIndexed = [];
            foreach ($dbData as $row) {
                if (!isset($row['id'])) continue;
                $dbIndexed[$row['id']] = $row;
            }

            // Normaliser aussi tout le formData (évite de re-normaliser à chaque boucle)
            $normalizedFormData = [];
            foreach ($formData as $row) {
                $normalizedFormData[] = $normalizeForm($row);
            }

            // Traiter le formulaire
            foreach ($normalizedFormData as $row) {
                if (isset($row['id'])) {
                    $id = $row['id'];
                    if (isset($dbIndexed[$id])) {
                        // Vérifier bc_id
                        if (($row['bc_id'] ?? null) == ($dbIndexed[$id]['bc_id'] ?? null)) {
                            // Comparer toutes les colonnes
                            if (array_diff_assoc($row, $dbIndexed[$id])) {
                                $updates[$id] = $row;
                            }
                        }
                    }
                } else {
                    // Nouvelle ligne
                    $inserts[] = $row;
                }
            }

            // Détection des suppressions
            foreach ($dbIndexed as $id => $dbRow) {
                $existsInForm = false;
                foreach ($normalizedFormData as $fRow) {
                    if (($fRow['id'] ?? null) == $id && ($fRow['bc_id'] ?? null) == $dbRow['bc_id']) {
                        $existsInForm = true;
                        break;
                    }
                }
                if (!$existsInForm) {
                    $deletes[] = $dbRow;
                }
            }

            return [
                'updates' => $updates,
                'inserts' => $inserts,
                'deletes' => $deletes
            ];
        }
        return [];
    }

    public function deleteBc()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(13);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_BC);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 40);
            $crud_article = new CrudModel(TBL_BC_ARTICLE);
            $crud_article->del(["bc_id" => $id], ["flag_suppression" => 1], 0);
            $crud_autre = new CrudModel(TBL_BC_AUTRE);
            $crud_autre->del(["id" => $id], ["flag_suppression" => 1], 0);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function getPrixArticle()
    {
        $type_client = $this->request->getPost('type_client');
        $article_id = $this->request->getPost('article_id');
        $date = $this->request->getPost('date');
        $crud = new CrudModel(TBL_TARIFICATION);
        $arr = [];
        if ($type_client == 1) {
            $arr = $crud->getDataById(array("article_id" => $article_id, "flag_suppression" => 0, "actif" => 1), [], "prix_client_standard as prix");
        }
        if ($type_client == 2) {
            $arr = $crud->getDataById(array("article_id" => $article_id, "flag_suppression" => 0, "actif" => 1), [], "prix_client_entreprise as prix");
        }
        $pourcentage = $this->getPourcentagePromotion($article_id, $date);
        if (!empty($arr) && $pourcentage != 0) {
            return json_encode($arr->prix - ($arr->prix * abs($pourcentage) / 100));
        }
        return (!empty($arr->prix) ? json_encode($arr->prix) : json_encode(0));
    }

    public function getPourcentagePromotion($article_id, $date)
    {
        $arr = [];
        $tool = new Tools();
        $date = $tool->normalizeDate($date);
        $sql = "SELECT pourcentage
                FROM promotion
                WHERE $article_id = ANY(list_article_id) AND actif = 1 AND flag_suppression = 0
                AND DATE '$date' BETWEEN date_debut AND date_fin
                LIMIT 1;";
        $arr =  $this->db->query($sql)->getRow();
        return (!empty($arr) ? $arr->pourcentage : 0);
    }
}
