<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ClientEntreprise extends BaseController
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
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
        $arr['titre'] = "Clients entreprises";
        $arr_client = $crud->getAllData(array("flag_suppression" => 0));
        $arr['arr_client_entreprise'] = $arr_client;
        echo view('client/client_entreprise_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
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
            'contact' => trim($this->request->getVar('contact')),
            'mail' => trim($this->request->getVar('mail')),
            'adresse' => trim($this->request->getVar('adresse')),
            'nif' => trim($this->request->getVar('nif')),
            'stat' => trim($this->request->getVar('stat'))
        ];
        if ($imageBase64 != "") {
            $arr_data['photo'] = $imageBase64;
        }
        $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
        $arr_data_base = $crud->getAllData(array(("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base)) {
            return json_encode(2);
        }
        $result = $crud->create($arr_data, 7);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('client/maj_client_entreprise_view', $arr);
    }

    public function deleteClient()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $tools  = new Tools();
            $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
            $can_delete = $tools->checkPaiementClient(2, $id);
            if ($can_delete != 1) {
                $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 8);
                return json_encode($result);
            } else {
                return json_encode(2);
            }
        }
        return json_encode(0);
    }

    public function majClient()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $id = trim($this->request->getVar('id_upd'));
        $crud_base = new CrudModel(TBL_CLIENT_ENTREPRISE);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $id));

        $image = $this->request->getFile('image_upd');

        $imageBase64 = "";
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageContent = file_get_contents($image->getTempName());
            $imageBase64 = base64_encode($imageContent);
        }
        $arr_data = [
            'libelle' => trim($this->request->getVar('libelle_upd')),
            'contact' => trim($this->request->getVar('contact_upd')),
            'mail' => trim($this->request->getVar('mail_upd')),
            'adresse' => trim($this->request->getVar('adresse_upd')),
            'actif' => intval(trim($this->request->getVar('actif_upd'))),
            'nif' => trim($this->request->getVar('nif_upd')),
            'stat' => trim($this->request->getVar('stat_upd')),
        ];
        if ($imageBase64 != "") {
            $arr_data['photo'] = $imageBase64;
        }
        $tool = new Tools();
        $arr_data_base = $crud_base->getAllData(array("id != " . $id => null, ("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base)) {
            return json_encode(3);
        }
        $can_delete = $tool->checkPaiementClient(2, $id);
        if ($can_delete == 1 && $arr_data['actif'] == 0 && $arr_base['actif'] == 1) {
            return json_encode(4); // il y a encore un montant impayé
        }
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $crud = new CrudModel(TBL_CLIENT_ENTREPRISE);
                $result = $crud->maj(["id" => $id], $arr_data, 9);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(4);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID", "Dénomination", "Contact", "Email", "Adresse", "Statut");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des clients entreprises');

        /* Titre de l'onglet */
        $sheet->setTitle('client');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT 
                    CONCAT('E', LPAD(id::text, 4, '0'))  AS id,
                    libelle,
                    contact,
                    mail,
                    adresse,
                    CASE 
                        WHEN actif = 1 THEN 'actif'
                        ELSE 'inactif'
                    END AS statut                  
                FROM client_entreprise
                WHERE flag_suppression = 0
                ORDER BY id";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'clients.xlsx';
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
