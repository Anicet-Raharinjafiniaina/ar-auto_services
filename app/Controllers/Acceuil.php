<?php

namespace App\Controllers;


class Acceuil extends BaseController
{
    public function index()
    {
        $session = session();
        $valide_session = $session->has('login') && $session->has('profil_id');
        if (!$valide_session) { // Session invalide
            return redirect()->to('/');
        }
        $arr['titre'] = "";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('acceuil', $arr);
            return;
        }
        return view('acceuil', $arr);
    }
}
