<?php

namespace App\Controllers;

use App\Models\CrudModel;

class Notification extends BaseController
{

    public function getArticle()
    {
        $crud_stock = new CrudModel(TBL_STOCK);
        $crud_seuil = new CrudModel(TBL_SEUIL_STOCK);
        $arr_seuil = $crud_seuil->getAllData(array("actif" => 1, "flag_suppression" => 0));
        $arr_id = [];
        foreach ($arr_seuil as $key => $value) {
            $arr_stock = $crud_stock->getDataById(array("article_id" => $value->article_id, "actif" => 1, "flag_suppression" => 0));
            if (!empty($arr_stock)) {
                if ($value->seuil_min > $arr_stock->quantite) {
                    array_push($arr_id, $arr_stock->article_id);
                }
            }
        }
        return $arr_id;
    }

    public function getDetailArticle()
    {
        $arr_id = $this->getArticle();
        $crud_article = new CrudModel(TBL_ARTICLE);
        $arr_join = [
            array(
                'table' => TBL_SEUIL_STOCK,
                'on' => TBL_SEUIL_STOCK . '.article_id = ' . TBL_ARTICLE . '.id',
                'type' => 'left'
            ),
            array(
                'table' => TBL_STOCK,
                'on' => TBL_STOCK . '.article_id = ' . TBL_ARTICLE . '.id',
                'type' => 'left'
            )
        ];
        $arr_article_detail = [];
        if (!empty($arr_id)) {
            foreach ($arr_id as $key => $value) {
                $arr_article = $crud_article->getDataById(array(TBL_ARTICLE . ".id" => $value, TBL_ARTICLE . ".actif" => 1, TBL_ARTICLE . ".flag_suppression" => 0, TBL_SEUIL_STOCK . ".actif" => 1, TBL_SEUIL_STOCK . ".flag_suppression" => 0), $arr_join, TBL_ARTICLE . ".id,reference,libelle,photo,seuil_min,quantite");
                if (!empty($arr_article)) {
                    array_push($arr_article_detail, $arr_article);
                }
            }
        }
        return $arr_article_detail;
    }

    public function getNotifPaiement()
    {
        $crud_bc = new CrudModel(TBL_BC);

        $arr_join = [
            ['table' => TBL_CLIENT_STANDARD, 'on' => TBL_CLIENT_STANDARD . '.id = ' . TBL_BC . '.client_id AND ' . TBL_BC . '.type_client = 1', 'type' => 'left'],
            ['table' => TBL_CLIENT_ENTREPRISE, 'on' => TBL_CLIENT_ENTREPRISE . '.id = ' . TBL_BC . '.client_id AND ' . TBL_BC . '.type_client = 2', 'type' => 'left'],
            ['table' => TBL_NUM_FACTURE, 'on' => TBL_NUM_FACTURE . '.bc_id = ' . TBL_BC . '.id', 'type' => 'left'],
            ['table' => TBL_PAIEMENT, 'on' => TBL_PAIEMENT . '.bc_id = ' . TBL_BC . '.id', 'type' => 'left'],
        ];

        $select = TBL_BC . ".id,
              CASE 
                  WHEN " . TBL_BC . ".type_client = 1 
                       THEN " . TBL_CLIENT_STANDARD . ".nom || ' ' || " . TBL_CLIENT_STANDARD . ".prenom
                  ELSE " . TBL_CLIENT_ENTREPRISE . ".libelle
              END as nom,
              " . TBL_NUM_FACTURE . ".id as num_facture,
              " . TBL_BC . ".net_a_payer,
              " . TBL_BC . ".montant_paye,
              " . TBL_BC . ".restant_du,
              COALESCE(SUM(" . TBL_PAIEMENT . ".montant), 0) as montant_paiement
              ";

        $groupBy = TBL_BC . ".id, "
            . TBL_CLIENT_STANDARD . ".nom, "
            . TBL_CLIENT_STANDARD . ".prenom, "
            . TBL_CLIENT_ENTREPRISE . ".libelle, "
            . TBL_NUM_FACTURE . ".id, "
            . TBL_BC . ".net_a_payer, "
            . TBL_BC . ".montant_paye";

        return $crud_bc->getAllData(
            [
                TBL_BC . ".restant_du > 0" => null,
                TBL_BC . ".statut_id" => 3,
                TBL_BC . ".flag_suppression" => 0
            ],
            $arr_join,
            $select,
            "",
            $groupBy
        );
    }

    public function notification()
    {
        $arr_article = $this->getDetailArticle();
        $arr_client = $this->getNotifPaiement();
        return json_encode([$arr_article, $arr_client]);
    }
}
