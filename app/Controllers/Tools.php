<?php

namespace App\Controllers;

use DateTime;
use Exception;
use App\Models\CrudModel;

class Tools extends BaseController
{
    /**
        $complet = ['nom' => 'Jean', 'age' => 30, 'ville' => 'Paris', 'pays' => 'France'];
        $partiel1 = ['nom' => 'Jean', 'age' => 30]; // ✅ true
        $partiel2 = ['nom' => 'Jean', 'age' => 25]; // ❌ false
        $partiel3 = ['ville' => 'Lyon'];            // ❌ false
        $partiel4 = ['pays' => 'France'];           // ✅ true
     */
    function isSameArray($arr_base, $arr_comparer)
    {
        foreach ($arr_comparer as $cle => $valeur) {
            if (!array_key_exists($cle, $arr_base) || $arr_base[$cle] != $valeur) {
                return 0;
            }
        }
        return 1;
    }

    /**
     * pour avoir toujour la forme YYYY-MM-DD
     */
    public function normalizeDate($date)
    {
        // Vérifie si la date contient des slashes → format jj/mm/aaaa
        if (strpos($date, '/') !== false) {
            $d = DateTime::createFromFormat('d/m/Y', $date);
        } else {
            // Sinon on suppose que c'est déjà yyyy-mm-dd
            $d = DateTime::createFromFormat('Y-m-d', $date);
        }
        if ($d === false) {
            // Gestion d'erreur si la date est invalide
            throw new Exception("Date invalide : $date");
        }
        return $d->format('Y-m-d'); // renvoie toujours "2025-08-25"
    }

    /** vérification quantité par article */
    public function getQuantite($id = null)
    {
        $article_id = "";
        if ($id == null) {
            $article_id = $this->request->getPost('article_id');
        } else {
            $article_id = $id;
        }
        $crud = new CrudModel(TBL_STOCK);
        $arr = $crud->getDataById(["article_id" => $article_id, "actif" => 1, "flag_suppression" => 0]);
        if (!empty($arr)) {
            return json_encode($arr->quantite);
        }
        return  json_encode(0);
    }



    public function majStock($article_id, $nb, $ancien_nb = null)
    {
        $crud = new CrudModel(TBL_STOCK);
        $count = $crud->getNb(["article_id" => $article_id]);
        $result = "";

        if ($count == 0) { // Cas : premier approvisionnement de l'article
            $result = $crud->create(["article_id" => $article_id, "quantite" => $nb], 0);
        } else { // Cas : déjà existant → mise à jour
            $this->calculStock($article_id, $nb, $ancien_nb);
        }
        return $result;
    }

    public function calculStock($article_id, $nb, $ancien_nb = null)
    {
        $crud = new CrudModel(TBL_STOCK);
        $arr = $crud->getDataById(["article_id" => $article_id, "actif" => 1, "flag_suppression" => 0]);
        if (!empty($arr)) {
            if ($ancien_nb === null) { // Ajout (nouvel approvisionnement)
                $new_quantity = $arr->quantite + $nb;
            } else { // Modification quantite stock        
                $new_quantity = $arr->quantite - $ancien_nb + $nb;
            }
            return $crud->maj(["article_id" => $article_id], ["quantite" => $new_quantity], 0);
        }
    }
    /** stock - nombre entré */
    public function majQuantiteStock($article_id, $nb)
    {
        $crud = new CrudModel(TBL_STOCK);
        $arr = $crud->getDataById(["article_id" => $article_id, "actif" => 1, "flag_suppression" => 0]);
        if (!empty($arr)) {
            $new_quantity = $arr->quantite - $nb;
            return $crud->maj(["article_id" => $article_id], ["quantite" => $new_quantity], 0);
        }
    }

    /** vérifier si le client a un en cours */
    public function checkPaiementClient($type, $client_id)
    {
        $crud = new CrudModel(TBL_BC);
        $arr = $crud->getDataById(array("statut_id" => 3, "flag_suppression" => 0, "restant_du > 0" => null, "type_client" => $type, "client_id" => $client_id));
        return (!empty($arr) ? 1 : 0);
    }
}
