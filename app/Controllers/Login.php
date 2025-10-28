<?php

namespace App\Controllers;

use App\Models\CrudModel;


class Login extends BaseController
{
    public function logIn()
    {
        $login = $this->request->getVar('login');
        $mdp = $this->request->getVar('mdp');
        $crud_user = new CrudModel(TBL_UTILISATEUR);
        $arr_user = $crud_user->getDataById(array("login" => $login, "flag_suppression" => 0));
        $session = session();
        if (!empty($arr_user)) {
            if ($arr_user->actif == 0) {
                return json_encode(2); // user désactivé
            } else if (password_verify($mdp, $arr_user->mdp)) {
                $session->set('user_id', $arr_user->id);
                $session->set('login', $login);
                $session->set('nom', $arr_user->nom . " " . $arr_user->prenom);
                $session->set('profil_id', $arr_user->profil_id);
                $session->set('theme', $arr_user->is_theme_dark);
                $crud = new CrudModel(TBL_UTILISATEUR);
                $crud->majData(["id" => $arr_user->id], ["derniere_connection" => date("Y-m-d H:i:s")]);
                $crud_histo = new CrudModel();
                $crud_histo->logInAndOut(29);
                return json_encode(1);
            } else {
                $session->destroy();
                return json_encode(0);
            }
        }
        $session->destroy();
        return json_encode(0);
    }

    public function logout()
    {
        $session = session();
        $crud_histo = new CrudModel();
        $crud_histo->logInAndOut(30);
        $session->destroy();  // Détruire toute la session
        return redirect()->to('Home');  // Rediriger vers la page de login
    }
}
