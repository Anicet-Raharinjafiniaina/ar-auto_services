<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tarification extends BaseController
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
        $is_ok = $acces->is_ok(16);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_TARIFICATION);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $arr['titre'] = "Paramétrage tarification";
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_TARIFICATION . '.article_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_CATEGORIE,
                'on' => TBL_CATEGORIE . '.id = ' . TBL_ARTICLE . '.categorie_id',
                'type' => 'left'
            )
        ];
        $arr_tarif = $crud->getAllData(array(TBL_TARIFICATION . ".flag_suppression" => 0, TBL_ARTICLE . ".flag_suppression" => 0, TBL_CATEGORIE . ".flag_suppression" => 0, TBL_ARTICLE . ".actif" => 1, TBL_CATEGORIE . ".actif" => 1), $arr_join, TBL_TARIFICATION . ".id," . TBL_TARIFICATION . ".article_id," . TBL_ARTICLE . ".reference," . TBL_ARTICLE . ".libelle as article," . TBL_TARIFICATION . ".actif," . TBL_CATEGORIE . ".libelle as categorie, prix_client_standard,prix_client_entreprise");
        $arr['arr_tarif'] = $arr_tarif;
        $arr_article = $crud_article->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr_categorie = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $arr_categorie;
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('tarification/tarif_view', $arr);
            return;
        }
        echo view('tarification/tarif_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(16);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $arr = $this->request->getVar('data');
        $arr_data = array(
            "article_id" => $arr['article_id'],
            "prix_client_standard" => str_replace(" ", "", $arr['prix_client_standard']),
            "prix_client_entreprise" => str_replace(" ", "", $arr['prix_client_entreprise']),
        );
        $crud = new CrudModel(TBL_TARIFICATION);
        $arr_data_base = $crud->getAllData(array("article_id" => $arr_data['article_id'], "flag_suppression" => 0), [], "article_id", "id", "", "", 1, 1);
        if (!empty($arr_data_base)) {
            return json_encode(2);
        }
        $result = $crud->create($arr_data, 26);
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(16);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_TARIFICATION);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        //    $arr_data = $crud->getDataById(array('id' => intval($id)));
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_TARIFICATION . '.article_id',
                'type' => 'left'
            ),
        ];
        $arr_data = $crud->getDataById(array(TBL_TARIFICATION . ".id" => $id), $arr_join, TBL_TARIFICATION . ".id," . TBL_ARTICLE . ".categorie_id," . TBL_TARIFICATION . ".article_id, prix_client_standard, prix_client_entreprise");
        $arr_categorie = [];
        $arr_article = [];
        if (!empty($arr_data)) {
            $arr_article = $crud_article->getAllData(array("categorie_id" => $arr_data->categorie_id, "flag_suppression" => 0, "actif" => 1));
            $arr_categorie = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        }
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $arr_categorie;
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('tarification/maj_tarif_view', $arr);
    }

    public function delete()

    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(16);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_TARIFICATION);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 28);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function maj()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(16);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $arr_data['prix_client_standard'] = str_replace(" ", "", $arr_data['prix_client_standard']);
        $arr_data['prix_client_entreprise'] = str_replace(" ", "", $arr_data['prix_client_entreprise']);
        unset($arr_data['categorie']);

        $crud_base = new CrudModel(TBL_TARIFICATION);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $arr_data["id"]));
        $tool = new Tools();
        $arr_data_base_art = $crud_base->getAllData(array("id != " . $arr_data["id"] => null,  "article_id" => $arr_data['article_id'], "flag_suppression" => 0), [], "article_id", "id", "", "", 1, 1);
        if (!empty($arr_data_base_art)) {
            return json_encode(3);
        }
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $crud = new CrudModel(TBL_TARIFICATION);
                $id = $arr_data["id"];
                unset($arr_data["id"]);
                $result = $crud->maj(["id" => $id], $arr_data, 27);
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(16);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID Article", "Référence", "Libellé", "Prix (client standard)", "Prix (client entreprise)");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Tarif par article');

        /* Titre de l'onglet */
        $sheet->setTitle('Tarif');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT 
                    CONCAT('A', LPAD(article.id::text, 4, '0'))  AS id,
                    article.reference,
                    article.libelle as  article,
                    prix_client_standard,
                    prix_client_entreprise
                FROM tarification
				LEFT JOIN article ON article.id = tarification.article_id AND article.flag_suppression = 0
                WHERE tarification.flag_suppression = 0
                ORDER BY id";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Tarif par article.xlsx';
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
