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


class DevalidationFacture extends BaseController
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
        $is_ok = $acces->is_ok(19);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Dévalidation facture";
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
            WHERE bc.statut_id = 3 AND bc.flag_suppression = 0
            ORDER BY numero_facture.id DESC
            ";
        $arr_bc =  $this->db->query($sql)->getResult();
        $arr['arr_bc'] = $arr_bc;
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('devalidation_facture/facturation_view', $arr);
            return;
        }
        echo view('devalidation_facture/facturation_view', $arr);
    }

    public function  getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(19);
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
        echo view('devalidation_facture/maj_facturation_view', $arr);
    }

    public function devaliderFacture()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(19);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        $tools = new Tools();
        $crud = new CrudModel(TBL_BC);
        $crud_bc_article = new CrudModel(TBL_BC_ARTICLE);
        $arr_bc_article = $crud_bc_article->getAllData(["bc_id" => $id, "flag_suppression" => 0]);
        if (!empty($arr_bc_article)) {
            foreach ($arr_bc_article as $key => $value) {
                $tools->majQuantiteStock($value->article_id, - ($value->quantite)); // pour remettre au stock la quantite 
            }
        }
        $result = $crud->maj(["id" => intval($id)], ["statut_id" => 1, "date_devalidation" => date('Y-m-d'), "date_validation" => null], 42);
        return json_encode($result);
    }
}
