<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Categorie extends BaseController
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
        $is_ok = $acces->is_ok(15);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_CATEGORIE);
        $arr['titre'] = "Catégorie";
        $arr_categorie = $crud->getAllData(array("flag_suppression" => 0));
        $arr['arr_categorie'] = $arr_categorie;
        echo view('categorie/categorie_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(15);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $arr_data = [
            'libelle' => trim($this->request->getVar('libelle')),
            'reference' => trim($this->request->getVar('reference')),
            'commentaire' => trim($this->request->getVar('commentaire')),
        ];

        $crud = new CrudModel(TBL_CATEGORIE);
        $arr_data_base_lib = $crud->getAllData(array(("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base_lib)) {
            return json_encode(2);
        }
        $arr_data_base_ref = $crud->getAllData(array(("LOWER(reference)") => strtolower($arr_data['reference']), "flag_suppression" => 0), [], "reference", "id", "", "", 1, 1);
        if (!empty($arr_data_base_ref)) {
            return json_encode(3);
        }
        $result = $crud->create($arr_data, 14);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(15);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_CATEGORIE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('categorie/maj_categorie_view', $arr);
    }

    public function deleteCategorie()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(15);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $arr_cat_used =  $this->checkCategorieForArticle($id);
            if (!empty($arr_cat_used)) {
                return json_encode(2);
            }
            $crud = new CrudModel(TBL_CATEGORIE);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 16);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function checkCategorieForArticle($cat_id)
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arr = $crud->getDataById(array("flag_suppression" => 0, "categorie_id" => $cat_id));
        return $arr;
    }

    public function majCategorie()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(15);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $id = trim($this->request->getVar('id_upd'));
        $crud_base = new CrudModel(TBL_CATEGORIE);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $id));

        $arr_data = [
            'reference' => trim($this->request->getVar('reference_upd')),
            'libelle' => trim($this->request->getVar('libelle_upd')),
            'commentaire' => trim($this->request->getVar('commentaire_upd')),
            'actif' => intval(trim($this->request->getVar('actif_upd'))),
        ];

        $tool = new Tools();
        $arr_data_base_lib = $crud_base->getAllData(array("id != " . $id => null,  ("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base_lib)) {
            return json_encode(3);
        }
        $arr_data_base_ref = $crud_base->getAllData(array("id != " . $id => null, ("LOWER(reference)") => strtolower($arr_data['reference']), "flag_suppression" => 0), [], "reference", "id", "", "", 1, 1);
        if (!empty($arr_data_base_ref)) {
            return json_encode(4);
        }
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $crud = new CrudModel(TBL_CATEGORIE);
                $result = $crud->maj(["id" => $id], $arr_data, 16);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(15);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID", "Référence", "Libellé", "Commentaire", "Statut");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des Catégories');

        /* Titre de l'onglet */
        $sheet->setTitle('Catégorie');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT 
                    CONCAT('A', LPAD(id::text, 4, '0'))  AS id,
                    reference,
                    libelle,
                    commentaire,
                    CASE 
                        WHEN actif = 1 THEN 'actif'
                        ELSE 'inactif'
                    END AS statut
                FROM Categorie
                WHERE flag_suppression = 0
                ORDER BY id";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Catégories.xlsx';
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
