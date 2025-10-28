<?php

namespace App\Controllers;

use App\Controllers\Acces;
use App\Models\CrudModel;
use App\Libraries\LibExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class User extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
        $this->checkTheme();
        $this->checkMenu();
    }

    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(1);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $crud = new CrudModel(TBL_UTILISATEUR);
        $arr_join = [
            array(
                'table' => 'profil',
                'on' => 'profil.id = ' . TBL_UTILISATEUR . '.profil_id',
                'type' => 'left'
            )
        ];
        $arr['arr_data_user'] = $crud->getAllData(array(TBL_UTILISATEUR . '.flag_suppression' => 0), $arr_join, TBL_UTILISATEUR . '.id,login,nom,prenom,' . TBL_UTILISATEUR . '.actif,profil.libelle as profil');
        $arr['titre'] = "Paramètrage des utilisateurs";
        $arr['arr_profil'] = $this->getAllProfil();
        echo view('user/user_view', $arr);
    }

    public function getAllProfil()
    {
        $crud = new CrudModel(TBL_PROFIL);
        $arr = $crud->getAllData(array('flag_suppression' => 0), [], 'id, libelle as text, actif');
        return $arr;
    }

    public function insertUser()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(1);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_user = $this->request->getVar('data');
        if (!empty($arr_user)) {
            $arr_user['profil_id'] =  $arr_user['profil'];
            unset($arr_user['profil']);
            $crud = new CrudModel(TBL_UTILISATEUR);
            $is_exist = $crud->getNb(array("login" => $arr_user['login'], "flag_suppression" => 0));
            if ($is_exist > 0) {
                return json_encode(2); // login doublon
            } else {
                $result = $crud->create($arr_user, 1);
                return json_encode(intVal($result));
            }
        }
        return json_encode(0);
    }

    /**
     * Visualisation d'un détail
     */
    public function getUser()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(1);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $crud = new CrudModel(TBL_UTILISATEUR);

        $id = trim($this->request->getVar('id'));
        $action = trim($this->request->getVar('action'));
        $arrData = $crud->getDataById(array('id' => intval($id)));
        $arr["errors"] = array();
        $arr["action"] = $action;
        $arr["data"] = $arrData;
        $arr["arr_profil"] = $this->getAllProfil();
        $arr["disabled"] = ($action == "voir") ? "disabled=disabled" : "";
        $arr["display"] = ($action == "voir") ? 'style="display:none;"' : "";
        echo view('user/maj_user_view', $arr);
    }


    public function majUser()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(1);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $arr_data = $this->request->getVar('data');
        $crud = new CrudModel(TBL_UTILISATEUR);
        if (!empty($arr_data)) {
            $is_login_exist = $crud->getNb(array("login" => $arr_data['login'], "id != " . $arr_data['id'] => null, "flag_suppression" => 0));
            $is_data_exist = $crud->getNb($arr_data);
            if ($is_login_exist > 0) {
                return json_encode(2); // login doublon
            } else if ($is_data_exist > 0) {
                return json_encode(3); // aucune modification
            } else {
                $id = $arr_data['id'];
                unset($arr_data['id']);
                $result = $crud->maj(["id" => $id], $arr_data, 2);
                if ($id == session()->get('user_id')) {
                    $session = session();
                    $session->set('nom', $arr_data['nom'] . " " . $arr_data['prenom']);
                    $session->set('profil_id', $arr_data['profil_id']);
                }
                return json_encode($result);
            }
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(1);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $id = $this->request->getVar('id');
        if ($id != "" && $id != null) {
            $crud = new CrudModel(TBL_UTILISATEUR);
            $result = $crud->del(["id" => $id], ["flag_suppression" => 1], 3);
            return json_encode($result);
        }
        return json_encode(0);
    }

    public function doExport()
    {
        // if (!$this->access->is_ok(59)) {
        //     return $this->access->get_redirect();
        // }
        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $path = URL_FILE;
        if (!file_exists($path))
            mkdir($path, 0777, true);

        $sheet = $spreadsheet->getActiveSheet();

        $arr_columns_title = array("Login", "Nom", "Prénom", "Statut");
        $nb_header_column = count($arr_columns_title);

        $excel->setTitleOfExcel($sheet, $nb_header_column, 2, 'Liste des utilisateurs');

        /* Titre de l'onglet */
        $sheet->setTitle('utilisateur');

        /* Titre de la colonne */
        $excel->setColumHeader(4, $arr_columns_title, $sheet);

        /* Les données correspondantes à chaque colonne */
        $sql = "SELECT login,
                    nom,
                    prenom,
                    CASE WHEN actif = 1 THEN 'actif'
                        ELSE 'inactif'
                    END           
                FROM utilisateur
                WHERE flag_suppression = 0";

        $arr =  $this->db->query($sql)->getResultArray();
        $excel->fetchAllData($arr, $nb_header_column, 4, 1, 5, $sheet);

        /* file excel output */
        $fileName = 'utilisateurs.xlsx';
        $rep = $path . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save($rep);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        $file = file_get_contents($rep);
        unlink($rep);
        return $this->response->download($fileName, $file);
    }

    public function changeTheme()
    {
        $crud = new CrudModel(TBL_UTILISATEUR);
        $theme = $this->request->getVar('data');
        $session_login = session()->get('login');
        $result = $crud->changeThemeModel(["login" => $session_login], ["is_theme_dark" => $theme]);
        return json_encode($result);
    }

    public function checkTheme()
    {
        $session = session();
        if ($session->has('login')) {
            $crud = new CrudModel(TBL_UTILISATEUR);
            $arr = $crud->getDataById(array('login' => session()->get('login'), 'flag_suppression' => 0), [], 'is_theme_dark');
            $session->set('theme', $arr->is_theme_dark);
        } else {
            return redirect()->to('/');
        }
    }

    public function changeMenu()
    {
        $crud = new CrudModel(TBL_UTILISATEUR);
        $menu = $this->request->getVar('data');
        $session_login = session()->get('login');
        $result = $crud->changeThemeModel(["login" => $session_login], ["is_menu_vertical" => $menu]);
        return json_encode($result);
    }

    public function checkMenu()
    {
        $session = session();
        if ($session->has('login')) {
            $crud = new CrudModel(TBL_UTILISATEUR);
            $arr = $crud->getDataById(array('login' => session()->get('login'), 'flag_suppression' => 0), [], 'is_menu_vertical');
            $session->set('menu', $arr->is_menu_vertical);
        } else {
            return redirect()->to('/');
        }
    }

    public function changeMdp()
    {
        $arr_mdp = $this->request->getVar('data');
        $crud_user = new CrudModel(TBL_UTILISATEUR);
        $arr_user = $crud_user->getDataById(array('login' => session()->get('login'), "flag_suppression" => 0));

        if (!empty($arr_user)) {
            if (password_verify($arr_mdp['mdp'], $arr_user->mdp)) {

                $crud = new CrudModel(TBL_UTILISATEUR);
                $hash_mdp_new = password_hash($arr_mdp['new_mdp'], PASSWORD_DEFAULT);
                $result = $crud->maj(["id" => $arr_user->id], ["mdp" => $hash_mdp_new], 10);
                return json_encode($result);
            } else {
                return json_encode(2); // mdp actuel incorrect
            }
        }
    }
}
