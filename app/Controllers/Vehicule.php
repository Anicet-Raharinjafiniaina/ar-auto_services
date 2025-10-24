<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Vehicule extends BaseController
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
        $is_ok = $acces->is_ok(17);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_VEHICULE);
        $arr['titre'] = "Liste des véhicules";
        $arr_vehicule = $crud->getAllData(array("flag_suppression" => 0), [], "*", "id", "", "asc");
        $arr['arr_vehicule'] = $arr_vehicule;
        echo view('parc_auto/vehicule_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(17);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $image = $this->request->getFile('image');

        $imageBase64 = "";
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageContent = file_get_contents($image->getTempName());
            $imageBase64 = base64_encode($imageContent);
        }
        $arr_data = [
            'libelle' => trim($this->request->getVar('libelle')),
        ];
        if ($imageBase64 != "") {
            $arr_data['photo'] = $imageBase64;
        }
        $crud = new CrudModel(TBL_VEHICULE);
        $arr_data_base_lib = $crud->getAllData(array(("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base_lib)) {
            return json_encode(2);
        }
        $result = $crud->create($arr_data, 34);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(17);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_VEHICULE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('parc_auto/maj_vehicule_view', $arr);
    }

    public function del()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(17);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_VEHICULE);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 36);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function maj()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(17);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $id = trim($this->request->getVar('id_upd'));
        $image = $this->request->getFile('image_upd');

        $imageBase64 = "";
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageContent = file_get_contents($image->getTempName());
            $imageBase64 = base64_encode($imageContent);
        }
        $arr_data = [
            'libelle' => trim($this->request->getVar('libelle_upd'))
        ];
        if ($imageBase64 != "") {
            $arr_data['photo'] = $imageBase64;
        }
        $crud = new CrudModel(TBL_VEHICULE);
        $arr_base = $crud->getDataByIdArray(array("id" => $id));
        $tool = new Tools();
        $arr_data_base_lib = $crud->getAllData(array("id != " . $id => null,  ("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base_lib)) {
            return json_encode(3);
        }
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $result = $crud->maj(["id" => $id], $arr_data, 35);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(17);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID", "Libellé");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des véhicules');

        /* Titre de l'onglet */
        $sheet->setTitle('Véhicule');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT 
                    id,
                    libelle
                FROM vehicule
                WHERE flag_suppression = 0
                ORDER BY id";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Véhicule.xlsx';
        $rep = $path . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save($rep);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        $file = file_get_contents($rep);
        unlink($rep);
        return $this->response->download($fileName, $file);
    }
}
