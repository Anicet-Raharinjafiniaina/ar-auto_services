<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MouvementStock extends BaseController
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
        $is_ok = $acces->is_ok(7);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Mouvement de stock";
        $sql = "SELECT
                    article.id,
                    article.reference,
                    article.libelle,
                    COALESCE(agg.quantite_in, 0)  AS quantite_in,
                    COALESCE(agg.quantite_out, 0) AS quantite_out,
                    COALESCE(agg.quantite_in, 0) - COALESCE(agg.quantite_out, 0) AS stock_theorique,
                    COALESCE(agg.stock_reel, 0) AS stock_reel
                FROM article
                INNER JOIN (   -- ✅ on utilise un INNER JOIN pour ne garder que les articles présents dans au moins une des tables
                    SELECT
                        a.article_id,
                        SUM(CASE WHEN src = 'in'  THEN quantite ELSE 0 END)  AS quantite_in,
                        SUM(CASE WHEN src = 'out' THEN quantite ELSE 0 END)  AS quantite_out,
                        SUM(CASE WHEN src = 'base' THEN quantite ELSE 0 END) AS stock_reel
                    FROM (
                        SELECT article_id, quantite, 'in' AS src FROM approvisionnement WHERE flag_suppression = 0
                        UNION ALL
                        SELECT article_id, quantite, 'out' AS src FROM bc_detail_article WHERE flag_suppression = 0
                        UNION ALL
                        SELECT article_id, quantite, 'base' AS src FROM stock WHERE flag_suppression = 0
                    ) AS a
                    GROUP BY a.article_id
                ) AS agg
                    ON article.id = agg.article_id
                WHERE article.flag_suppression = 0 AND article.actif = 1
                ORDER BY article.id";
        $arr['arr_mouvement'] =  $this->db->query($sql)->getResult();
        echo view('mouvement_stock/mouvement_view', $arr);
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(7);
        if (!$is_ok) {
            return redirect()->to('/');
        }

        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("ID Article", "Référence", "Dénomination", "entrée", "Sortie", "Stock théorique", "Stock réel");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Mouvement de stock');

        /* Titre de l'onglet */
        $sheet->setTitle('Mouvement de stock');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT
                    LPAD(article.id::text, 4, '0') AS id,
                    article.reference,
                    article.libelle,
                    COALESCE(agg.quantite_in, 0)  AS quantite_in,
                    COALESCE(agg.quantite_out, 0) AS quantite_out,
                    COALESCE(agg.quantite_in, 0) - COALESCE(agg.quantite_out, 0) AS stock_theorique,
                    COALESCE(agg.stock_reel, 0) AS stock_reel
                FROM article
                INNER JOIN (   -- ✅ on utilise un INNER JOIN pour ne garder que les articles présents dans au moins une des tables
                    SELECT
                        a.article_id,
                        SUM(CASE WHEN src = 'in'  THEN quantite ELSE 0 END)  AS quantite_in,
                        SUM(CASE WHEN src = 'out' THEN quantite ELSE 0 END)  AS quantite_out,
                        SUM(CASE WHEN src = 'base' THEN quantite ELSE 0 END) AS stock_reel
                    FROM (
                        SELECT article_id, quantite, 'in' AS src FROM approvisionnement WHERE flag_suppression = 0
                        UNION ALL
                        SELECT article_id, quantite, 'out' AS src FROM bc_detail_article WHERE flag_suppression = 0
                        UNION ALL
                        SELECT article_id, quantite, 'base' AS src FROM stock WHERE flag_suppression = 0
                    ) AS a
                    GROUP BY a.article_id
                ) AS agg
                    ON article.id = agg.article_id
                WHERE article.flag_suppression = 0 AND article.actif = 1
                ORDER BY article.id;";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);
        if (empty($arr)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        /* file excel output */
        $fileName = 'mouvement_stock.' . date('d_m_Y') . '.xlsx';
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
