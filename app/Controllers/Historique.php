<?php

namespace App\Controllers;

use App\Models\CrudModel;
use App\Controllers\Acces;
use App\Controllers\Tools;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Historique extends BaseController
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
        $is_ok = $acces->is_ok(12);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_HISTORIQUE);
        $arr_join = [
            array(
                'table' => TBL_UTILISATEUR,
                'on' => TBL_UTILISATEUR . '.id = ' . TBL_HISTORIQUE . '.utilisateur_id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_CRUD_ACTION,
                'on' => TBL_CRUD_ACTION . '.id = ' . TBL_HISTORIQUE . '.action_id',
                'type' => 'left'
            )
        ];
        $arr['titre'] = "Historique d'intéractions";
        $arr_histo = $crud->getAllData([], $arr_join, TBL_HISTORIQUE . ".id," . TBL_CRUD_ACTION . ".libelle," . TBL_UTILISATEUR . ".login," . TBL_UTILISATEUR . ".nom," . TBL_HISTORIQUE . ".date_creation", TBL_HISTORIQUE . ".id", "", "desc");
        $arr['arr_histo'] = $arr_histo;
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('categorie/historique_view', $arr);
            return;
        }
        echo view('categorie/historique_view', $arr);
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

        $arr_columns_title = array("ID", "Référence", "Libellé", "Date de création", "Commentaire", "Statut");
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
