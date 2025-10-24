<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Societe extends BaseController
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
        $is_ok = $acces->is_ok(18);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_SOCIETE);
        $arr['titre'] = "Information de la société";
        $arr_societe = $crud->getAllData(array("flag_suppression" => 0), [], "*", "id", "", "asc");
        $arr['arr_societe'] = $arr_societe;
        echo view('societe/societe_view', $arr);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(18);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_SOCIETE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('societe/maj_societe_view', $arr);
    }

    public function maj()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(18);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $id = trim($this->request->getVar('id_upd'));
        $image = $this->request->getFile('logo_upd');

        $imageBase64 = "";
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageContent = file_get_contents($image->getTempName());
            $imageBase64 = base64_encode($imageContent);
        }
        $arr_data = [
            'libelle' => trim($this->request->getVar('libelle_upd')),
            'adresse' => trim($this->request->getVar('adresse_upd')),
            'ville' => trim($this->request->getVar('ville_upd')),
            'nif' => trim($this->request->getVar('nif_upd')),
            'stat' => trim($this->request->getVar('stat_upd')),
            'rcs' => trim($this->request->getVar('rcs_upd')),
            'banque' => trim($this->request->getVar('banque_upd')),
            'compte_bancaire' => trim($this->request->getVar('compte_bancaire_upd')),
            'telephone' => trim($this->request->getVar('telephone_upd')),
            'adresse_mail' => trim($this->request->getVar('adresse_mail_upd'))

        ];
        if ($imageBase64 != "") {
            $arr_data['logo'] = $imageBase64;
        }
        $crud = new CrudModel(TBL_SOCIETE);
        $arr_base = $crud->getDataByIdArray(array("id" => $id));
        $tool = new Tools();
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $result = $crud->maj(["id" => $id], $arr_data, 37);
                return json_encode($result);
            }
        }
    }
}
