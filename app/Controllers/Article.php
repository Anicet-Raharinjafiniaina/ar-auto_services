<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Article extends BaseController
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
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arr['titre'] = "Articles";
        $arr_join = [
            array(
                'table' => TBL_CATEGORIE,
                'on' => TBL_CATEGORIE . '.id = ' . TBL_ARTICLE . '.categorie_id',
                'type' => 'left'
            )
        ];
        $arr_article = $crud->getAllData(array(TBL_ARTICLE . ".flag_suppression" => 0), $arr_join, TBL_ARTICLE . ".id," . TBL_ARTICLE . ".reference," . TBL_ARTICLE . ".libelle as article," . TBL_ARTICLE . ".date_creation_article," . TBL_ARTICLE . ".photo," . TBL_ARTICLE . ".commentaire," . TBL_ARTICLE . ".actif," . TBL_CATEGORIE . ".libelle as categorie");
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $this->getAllCategorie();
        echo view('article/article_view', $arr);
    }

    public function getAllCategorie()
    {
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $arr_cat = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        return $arr_cat;
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
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
            'categorie_id' => trim($this->request->getVar('categorie')),
            'libelle' => trim($this->request->getVar('libelle')),
            'reference' => trim($this->request->getVar('reference')),
            'commentaire' => trim($this->request->getVar('commentaire')),
        ];
        if ($imageBase64 != "") {
            $arr_data['photo'] = $imageBase64;
        }
        $crud = new CrudModel(TBL_ARTICLE);
        $arr_data_base_lib = $crud->getAllData(array(("LOWER(libelle)") => strtolower($arr_data['libelle']), "flag_suppression" => 0), [], "libelle", "id", "", "", 1, 1);
        if (!empty($arr_data_base_lib)) {
            return json_encode(2);
        }
        $arr_data_base_ref = $crud->getAllData(array(("LOWER(reference)") => strtolower($arr_data['reference']), "flag_suppression" => 0), [], "reference", "id", "", "", 1, 1);
        if (!empty($arr_data_base_ref)) {
            return json_encode(3);
        }
        $result = $crud->create($arr_data, 11);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_ARTICLE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr['arr_categorie'] = $this->getAllCategorie();
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('article/maj_article_view', $arr);
    }

    public function deleteArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $is_used = $this->articleDejaUtilise(intval($id));
            if ($is_used == true) {
                return json_encode(2);
            }
            $crud = new CrudModel(TBL_ARTICLE);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 13);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function articleDejaUtilise($id)
    {
        $tables = [
            TBL_STOCK => ['article_id' => $id, "quantite !=0" => null, "flag_suppression" => 0],
            TBL_APPROVISIONNEMENT => ['article_id' => $id, "flag_suppression" => 0],
            TBL_SEUIL_STOCK => ['article_id' => $id, "flag_suppression" => 0],
            TBL_TARIFICATION => ['article_id' => $id, "flag_suppression" => 0],
            TBL_BC_ARTICLE => ['article_id' => $id, "flag_suppression" => 0],
        ];

        foreach ($tables as $table => $conditions) {
            $crud = new CrudModel($table);
            $result = $crud->getDataById($conditions);
            if (!empty($result)) { // Dès qu’on trouve une occurrence, on peut arrêter
                return true;
            }
        }
        return false;
    }


    public function majArticle()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $id = trim($this->request->getVar('id_upd'));
        $crud_base = new CrudModel(TBL_ARTICLE);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $id));

        $image = $this->request->getFile('image_upd');

        $imageBase64 = "";
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageContent = file_get_contents($image->getTempName());
            $imageBase64 = base64_encode($imageContent);
        }
        $arr_data = [
            'categorie_id' => trim($this->request->getVar('categorie_upd')),
            'reference' => trim($this->request->getVar('reference_upd')),
            'libelle' => trim($this->request->getVar('libelle_upd')),
            'commentaire' => trim($this->request->getVar('commentaire_upd')),
            'actif' => intval(trim($this->request->getVar('actif_upd'))),
        ];
        if ($imageBase64 != "") {
            $arr_data['photo'] = $imageBase64;
        }
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
                $crud = new CrudModel(TBL_ARTICLE);
                $result = $crud->maj(["id" => $id], $arr_data, 12);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(6);
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

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des Articles');

        /* Titre de l'onglet */
        $sheet->setTitle('Article');

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
                FROM article
                WHERE flag_suppression = 0
                ORDER BY id";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Articles.xlsx';
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
