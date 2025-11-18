<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SeuilStock extends BaseController
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
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_SEUIL_STOCK);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $arr['titre'] = "Paramétrage seuil minimum";
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_SEUIL_STOCK . '.article_id',
                'type' => 'left'
            )
        ];
        $arr_seuil = $crud->getAllData(array(TBL_SEUIL_STOCK . ".flag_suppression" => 0), $arr_join, TBL_SEUIL_STOCK . ".id," . TBL_SEUIL_STOCK . ".article_id," . TBL_ARTICLE . ".reference," . TBL_ARTICLE . ".libelle as article," . TBL_SEUIL_STOCK . ".actif," . TBL_SEUIL_STOCK . ".seuil_min");
        $arr['arr_seuil'] = $arr_seuil;
        $arr_article = $crud_article->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr_categorie = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $arr_categorie;
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('seuil_stock/seuil_view', $arr);
            return;
        }
        echo view('seuil_stock/seuil_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $arr_data = [
            'article_id' => trim($this->request->getVar('article')),
            'seuil_min' => trim($this->request->getVar('seuil_min')),
        ];

        $crud = new CrudModel(TBL_SEUIL_STOCK);
        $arr_data_base = $crud->getAllData(array("article_id" => $arr_data['article_id'], "flag_suppression" => 0), [], "article_id", "id", "", "", 1, 1);
        if (!empty($arr_data_base)) {
            return json_encode(2);
        }
        $result = $crud->create($arr_data, 17);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_SEUIL_STOCK);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        //    $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_SEUIL_STOCK . '.article_id',
                'type' => 'left'
            )
        ];
        $arr_data = $crud->getDataById(array(TBL_SEUIL_STOCK . ".id" => $id), $arr_join, TBL_ARTICLE . ".categorie_id," . TBL_SEUIL_STOCK . ".article_id," . TBL_SEUIL_STOCK . ".id," . TBL_ARTICLE . ".reference," . TBL_ARTICLE . ".libelle as article," . TBL_SEUIL_STOCK . ".actif," . TBL_SEUIL_STOCK . ".seuil_min");
        $arr_article = [];
        if (!empty($arr_data)) {
            $arr_article = $crud_article->getAllData(array("categorie_id" => $arr_data->categorie_id, "flag_suppression" => 0, "actif" => 1));
        }
        $arr_categorie = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $arr_categorie;
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('seuil_stock/maj_seuil_view', $arr);
    }

    public function deleteSeuil()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_SEUIL_STOCK);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 19);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function majSeuil()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $id = trim($this->request->getVar('id_upd'));
        $crud_base = new CrudModel(TBL_SEUIL_STOCK);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $id));

        $arr_data = [
            'article_id' => trim($this->request->getVar('article_upd')),
            'seuil_min' => trim($this->request->getVar('seuil_min_upd')),
            'actif' => intval(trim($this->request->getVar('actif_upd'))),
        ];

        $tool = new Tools();
        $arr_data_base_lib = $crud_base->getAllData(array("id != " . $id => null,  "article_id" => $arr_data['article_id'], "flag_suppression" => 0), [], "article_id", "id", "", "", 1, 1);
        if (!empty($arr_data_base_lib)) {
            return json_encode(3);
        }
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $crud = new CrudModel(TBL_SEUIL_STOCK);
                $result = $crud->maj(["id" => $id], $arr_data, 18);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(9);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID Article", "Référence", "Libellé", "Seuil minimum", "Statut");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des seuils');

        /* Titre de l'onglet */
        $sheet->setTitle('Seuil');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT 
                    CONCAT('A', LPAD(article.id::text, 4, '0'))  AS id,
                    article.reference,
                    article.libelle as  article,
                    seuil_stock.seuil_min,
                    CASE 
                        WHEN seuil_stock.actif = 1 THEN 'actif'
                        ELSE 'inactif'
                    END AS statut
                FROM seuil_stock
				LEFT JOIN article ON article.id = seuil_stock.article_id AND article.flag_suppression = 0
                WHERE seuil_stock.flag_suppression = 0
                ORDER BY id";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Seuil minimum.xlsx';
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
