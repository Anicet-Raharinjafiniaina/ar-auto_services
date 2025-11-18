<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Approvisionnement extends BaseController
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
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_APPROVISIONNEMENT);
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $crud_fournisseur = new CrudModel(TBL_FOURNISSEUR);
        $arr['titre'] = "Approvisionnement";
        $arr_join = [
            array(
                'table' => TBL_ARTICLE,
                'on' => TBL_ARTICLE . '.id = ' . TBL_APPROVISIONNEMENT . '.article_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_CATEGORIE,
                'on' => TBL_CATEGORIE . '.id = ' . TBL_APPROVISIONNEMENT . '.categorie_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_FOURNISSEUR,
                'on' => TBL_FOURNISSEUR . '.id = ' . TBL_APPROVISIONNEMENT . '.fournisseur_id',
                'type' => 'left'
            )
        ];
        $arr_appro = $crud->getAllData(array(TBL_APPROVISIONNEMENT . ".flag_suppression" => 0), $arr_join, TBL_APPROVISIONNEMENT . ".id," . TBL_APPROVISIONNEMENT . ".article_id," . TBL_ARTICLE . ".reference," . TBL_ARTICLE . ".libelle as article," . TBL_APPROVISIONNEMENT . ".actif," . TBL_APPROVISIONNEMENT . ".quantite," . TBL_APPROVISIONNEMENT . ".date_appro," . TBL_FOURNISSEUR . ".libelle as fournisseur");
        $arr['arr_appro'] = $arr_appro;
        $arr_article = $crud_article->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr_categorie = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr_fournisseur = $crud_fournisseur->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $arr_categorie;
        $arr['arr_fournisseur'] = $arr_fournisseur;
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('approvisionnement/appro_view', $arr);
            return;
        }
        echo view('approvisionnement/appro_view', $arr);
    }

    public function getAllArticle()
    {
        $categorie_id = trim($this->request->getVar('categorie'));
        if ($categorie_id != "") {
            $crud_article = new CrudModel(TBL_ARTICLE);
            $arr_article = $crud_article->getAllData(array("categorie_id" => $categorie_id, "flag_suppression" => 0, "actif" => 1), [], "id, reference || ' ' || libelle as text");
            return  json_encode($arr_article);
        }
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tools  = new Tools();
        $arr_appro = $this->request->getVar('data');
        $arr_appro['date_appro'] = $tools->normalizeDate($arr_appro['date_appro']);
        $crud = new CrudModel(TBL_APPROVISIONNEMENT);
        $result = $crud->create($arr_appro, 20);
        if ($result == 1) {
            $tools->majStock($arr_appro['article_id'], $arr_appro['quantite']);
        }
        return json_encode($result);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_APPROVISIONNEMENT);
        $crud_categorie = new CrudModel(TBL_CATEGORIE);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $crud_fournisseur = new CrudModel(TBL_FOURNISSEUR);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array("id" => $id), [], "id, fournisseur_id, categorie_id, article_id, quantite,date_appro,commentaire");
        $arr_article = $crud_article->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr_categorie = $crud_categorie->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr_fournisseur = $crud_fournisseur->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        $arr['arr_categorie'] = $arr_categorie;
        $arr['arr_fournisseur'] = $arr_fournisseur;
        $arr["action"] = $action;
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('approvisionnement/maj_appro_view', $arr);
    }

    public function deleteAppro()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_APPROVISIONNEMENT);
            $arr = $crud->getDataById(["id" => $id, "flag_suppression" => 0]);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 22);
            if ($result == 1 && !empty($arr)) {
                $tool = new Tools();
                $tool->majQuantiteStock($arr->article_id, $arr->quantite); // maj stock
            }
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function majAppro()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $arr_data = $this->request->getVar('data');
        $arr_data['date_appro'] = $tool->normalizeDate($arr_data['date_appro']);
        $id = $arr_data['id'];
        unset($arr_data['id']);
        $crud_base = new CrudModel(TBL_APPROVISIONNEMENT);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $id));
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $crud = new CrudModel(TBL_APPROVISIONNEMENT);
                $result = $crud->maj(["id" => $id], $arr_data, 21);
                if ($result == 1) {
                    $tool->majStock($arr_data['article_id'], $arr_data['quantite'], $arr_base['quantite']);
                }
                return json_encode($result);
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(8);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID Article", "Référence", "Libellé", "Quantité", "Date", "Fournisseur");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des approvisionnements');

        /* Titre de l'onglet */
        $sheet->setTitle('Appro');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT 
                    CONCAT('A', LPAD(approvisionnement.article_id::text, 4, '0'))  AS id   ,
                    article.reference,
                    article.libelle AS article,
                    approvisionnement.quantite,
                    TO_CHAR(approvisionnement.date_appro, 'DD/MM/YYYY') AS date_appro,
                    fournisseur.libelle AS fournisseur
                FROM approvisionnement
                LEFT JOIN article 
                    ON article.id = approvisionnement.article_id
                LEFT JOIN categorie 
                    ON categorie.id = approvisionnement.categorie_id
                LEFT JOIN fournisseur 
                    ON fournisseur.id = approvisionnement.fournisseur_id
                WHERE approvisionnement.flag_suppression = 0";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Approvisionnement.xlsx';
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
