<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Promotion extends BaseController
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
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud_article = new CrudModel(TBL_ARTICLE);
        $arr['titre'] = "Paramétrage promotion";
        $sql = "WITH articles_limited AS (
                SELECT
                    a.id,
                    a.libelle,
                    p.id AS promotion_id,
                    ROW_NUMBER() OVER (PARTITION BY p.id ORDER BY a.libelle) AS rn,
                    COUNT(*) OVER (PARTITION BY p.id) AS total_articles
                FROM promotion p
                JOIN article a ON a.id = ANY(p.list_article_id)
                WHERE p.flag_suppression = 0
            )
            SELECT
                p.id,
                p.libelle,
                p.pourcentage,
                CASE
                    WHEN MAX(al.total_articles) > 2 THEN
                        string_agg(al.libelle, ', ' ORDER BY al.rn) || ' ...'
                    ELSE
                        string_agg(al.libelle, ', ' ORDER BY al.rn)
                END AS article_libelle,
                p.date_debut,
                p.date_fin,
                p.commentaire,
                p.actif
            FROM promotion p
            JOIN articles_limited al ON al.promotion_id = p.id AND al.rn <= 2
            WHERE p.flag_suppression = 0
            GROUP BY
                p.id, p.libelle, p.pourcentage, p.date_debut, p.date_fin, p.commentaire, p.actif
            ORDER BY
                p.date_debut DESC";

        $arr_promotion =  $this->db->query($sql)->getResult();
        $arr['arr_promotion'] = $arr_promotion;
        $arr_article = $crud_article->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        echo view('promotion/promotion_view', $arr);
    }

    public function insert()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $arr = $this->request->getVar('data');
        $arr_data = array(
            "libelle" => $arr['libelle'],
            "pourcentage" => $arr['pourcentage'],
            "date_debut" => $tool->normalizeDate($arr['date_debut']),
            "date_fin" => $tool->normalizeDate($arr['date_fin']),
            "list_article_id" => $arr['list_article_id'],
            "commentaire" => $arr['commentaire'],
        );
        sort($arr_data['list_article_id']);
        $list_article_id = '{' . implode(',', $arr_data['list_article_id']) . '}';
        $arr_data['list_article_id'] = $list_article_id;
        if (!empty($arr_data)) {
            $crud = new CrudModel(TBL_PROMOTION);
            $arr_data_base = $crud->getAllData(array("pourcentage" => $arr_data['pourcentage'], "list_article_id" => $arr_data['list_article_id'], "date_debut" => $arr_data['date_debut'], "date_fin" => $arr_data['date_fin'], "flag_suppression" => 0), [], "id", "id", "", "", 1, 1);
            if (!empty($arr_data_base)) {
                return json_encode(2);
            }
            $result = $crud->create($arr_data, 23);
            return json_encode($result);
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getDetail()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_PROMOTION);
        $crud_article = new CrudModel(TBL_ARTICLE);
        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arr_data = $crud->getDataById(array("id" => $id), [], "*");
        $arr_article = $crud_article->getAllData(array("flag_suppression" => 0, "actif" => 1));
        $arr['arr_article'] = $arr_article;
        $arr["action"] = $action;
        if (!empty($arr_data)) {
            $arr_data->list_article_id = array_map('intval', explode(',', str_replace(['{', '}'], '',  $arr_data->list_article_id)));
        }
        $arr["data"] = $arr_data;
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('promotion/maj_promotion_view', $arr);
    }

    public function deletePromotion()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_PROMOTION);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 25);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function majPromotion()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $tool = new Tools();
        $arr = $this->request->getVar('data');
        $arr_data = array(
            "libelle" => $arr['libelle'],
            "pourcentage" => $arr['pourcentage'],
            "date_debut" => $tool->normalizeDate($arr['date_debut']),
            "date_fin" => $tool->normalizeDate($arr['date_fin']),
            "list_article_id" => $arr['list_article_id'],
            "commentaire" => $arr['commentaire'],
            "actif" => $arr['actif']
        );
        sort($arr_data['list_article_id']);
        $arr_data['list_article_id'] = '{' . implode(',', $arr_data['list_article_id']) . '}';
        $crud_base = new CrudModel(TBL_PROMOTION);
        $arr_base = $crud_base->getDataByIdArray(array("id" => $arr['id']));
        $tool = new Tools();
        if (!empty($arr_base)) {
            $is_same_array = $tool->isSameArray($arr_base, $arr_data);
            if ($is_same_array == 1) {
                return json_encode(2); //Aucune modification
            } else {
                $check_data_base = $crud_base->getNb($arr_data);
                if ($check_data_base > 0) {
                    return json_encode(3); // données qui existent déjà
                } else {
                    $crud = new CrudModel(TBL_PROMOTION);
                    $result = $crud->maj(["id" => $arr['id']], $arr_data, 24);
                    return json_encode($result);
                }
            }
        }
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(11);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("Promotion", "Pourcentage", "Date début", "Date fin", "ID article", "Réf article", "Article", "Statut", "Commentaire");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des promotions');

        /* Titre de l'onglet */
        $sheet->setTitle('Seuil');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT                
                    p.libelle AS promotion_libelle,
                    p.pourcentage,
                    TO_CHAR(p.date_debut, 'DD/MM/YYYY') ,
                    TO_CHAR(p.date_fin, 'DD/MM/YYYY') ,
                    CONCAT('A', LPAD(a.id::text, 4, '0'))  AS article_id,
                    a.reference,
                    a.libelle AS article_libelle,                
                   CASE 
                        WHEN p.actif = 1 THEN 'actif'
                        ELSE 'inactif'
                    END AS statut,
                    p.commentaire
                FROM promotion p
                JOIN article a ON a.id = ANY(p.list_article_id)
                WHERE p.flag_suppression = 0
                ORDER BY p.date_debut DESC, a.libelle";
        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'Promotion.xlsx';
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
