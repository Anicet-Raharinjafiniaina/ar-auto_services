<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use App\Controllers\Notification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaiementCredit extends BaseController
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
        $is_ok = $acces->is_ok(20);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $notif = new Notification();
        $arr['arr_client'] = $notif->getNotifPaiement();

        $crud = new CrudModel(TBL_PAIEMENT);
        $arr_join = [
            array(
                'table' => TBL_BC,
                'on' => TBL_BC . '.id = ' . TBL_PAIEMENT . '.bc_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_NUM_FACTURE,
                'on' => TBL_NUM_FACTURE . '.bc_id = ' . TBL_PAIEMENT . '.bc_id',
                'type' => 'left'
            ),
        ];
        $select = TBL_PAIEMENT . '.id,' . TBL_NUM_FACTURE . ".id as num_facture," . TBL_BC . ".id as bc_id," . TBL_PAIEMENT . ".montant," . TBL_PAIEMENT . ".date_paiement," . TBL_BC . ".restant_du," . TBL_PAIEMENT . ".commentaire";
        $arr['arr_data'] = $crud->getAllData(array(TBL_BC . ".statut_id" => 3, TBL_BC . ".flag_suppression" => 0, TBL_PAIEMENT . ".flag_suppression" => 0), $arr_join, $select);
        $arr['titre'] = "Suivi des paiements à crédit";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('paiement_a_credit/list_view', $arr);
            return;
        }
        echo view('paiement_a_credit/list_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(20);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $arr = $this->request->getVar('data');
        $arr['date_paiement'] = $tool->normalizeDate($arr['date_paiement']);
        $arr['montant'] = str_replace(" ", "", $arr['montant']);
        $crud = new CrudModel(TBL_PAIEMENT);
        $result = $crud->create($arr, 43);
        $this->majRestantDu($arr['bc_id'], $arr['montant']);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(20);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_PAIEMENT);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $notif = new Notification();
        $arr['arr_client'] = $notif->getNotifPaiement();
        $arr_join = [
            array(
                'table' => TBL_NUM_FACTURE,
                'on' => TBL_NUM_FACTURE . '.bc_id = ' . TBL_PAIEMENT . '.bc_id',
                'type' => 'left'
            ),
        ];
        $select = TBL_PAIEMENT . ".id as id," . TBL_NUM_FACTURE . ".id as num_facture," . TBL_PAIEMENT . ".bc_id," . TBL_PAIEMENT . ".montant," . TBL_PAIEMENT . ".date_paiement," . TBL_PAIEMENT . ".commentaire";
        $arr_data = $crud->getDataById(array(TBL_PAIEMENT . ".id" => $id), $arr_join, $select);
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('paiement_a_credit/maj_view', $arr);
    }

    public function deletePaiement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(20);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_PAIEMENT);
            $arr = $crud->getDataById(array("id" => $id));
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 45);
            $this->majRestantDu($arr->bc_id, ($arr->montant * -1));
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function majPaiement()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(20);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $arr_data = $this->request->getVar('data');
        $arr_data['date_paiement'] = $tool->normalizeDate($arr_data['date_paiement']);
        $crud_base = new CrudModel(TBL_PAIEMENT);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $arr_data['id']));
        $montant = str_replace(" ", "", $arr_data['montant']);
        $arr_data['montant'] = $montant;
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $crud = new CrudModel(TBL_PAIEMENT);
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $arr_paiement = $crud->getDataById(["id" => $id], [], "montant");
                $result = $crud->maj(["id" => $id], $arr_data, 44);
                $this->majRestantDu($arr_data['bc_id'], $montant, $arr_paiement->montant);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(20);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("N° Facture", "Montant payé", "Restant dû", "Date", "Commentaire");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des paiements à crédit');

        /* Titre de l'onglet */
        $sheet->setTitle('Paiement à crédit');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $crud = new CrudModel(TBL_PAIEMENT);
        $arr_join = [
            array(
                'table' => TBL_BC,
                'on' => TBL_BC . '.id = ' . TBL_PAIEMENT . '.bc_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_NUM_FACTURE,
                'on' => TBL_NUM_FACTURE . '.bc_id = ' . TBL_PAIEMENT . '.bc_id',
                'type' => 'left'
            ),
        ];
        $select = "CONCAT('FA-', LPAD(" . TBL_NUM_FACTURE . ".id::text, 4, '0')) AS  num_facture," . TBL_PAIEMENT . ".montant," . TBL_BC . ".restant_du," . TBL_PAIEMENT . ".date_paiement,"  . TBL_PAIEMENT . ".commentaire";
        $arr = $crud->getAllDataArray(array(TBL_BC . ".statut_id" => 3, TBL_BC . ".flag_suppression" => 0, TBL_PAIEMENT . ".flag_suppression" => 0), $arr_join, $select);
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Paiement à crédit.xlsx';
        $rep = $path . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save($rep);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        $file = file_get_contents($rep);
        unlink($rep);
        return $this->response->download($fileName, $file);
    }

    public function majRestantDu($id, $montant, $ancien_montant = 0)
    {
        $crud = new CrudModel(TBL_BC);
        $arr_data = $crud->getDataById(["id" => $id], [], "restant_du");
        $new_sum_restant_du = floatval($arr_data->restant_du) + floatval($ancien_montant) - floatval($montant);    // Si ancien montant existe, on le réintègre avant de soustraire le nouveau
        return $crud->maj(["id" => $id], ["restant_du" => $new_sum_restant_du], 0);
    }
}
