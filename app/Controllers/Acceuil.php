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
        return view('acceuil', $arr);
    }
}
