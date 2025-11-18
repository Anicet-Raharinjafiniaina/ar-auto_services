<?php

namespace App\Controllers;

use App\Models\CrudModel;

class Dashboard extends BaseController
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
        $is_ok = $acces->is_ok(21);
        if (!$is_ok) {
            return redirect()->to('/');
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "";
        $arr['arr_all_recette'] = $this->getAllRecette();
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('dashboard_view', $arr);
            return;
        }
        echo view('dashboard_view', $arr);
    }

    public function getAllRecette()
    {
        $sql = "SELECT *
                FROM (
                    -- bc
                    SELECT 
                        nf.id AS numero_facture_id, 
                        bc.montant_paye AS montant, 
                        bc.date_validation AS date_real,
                        TO_CHAR(DATE(bc.date_validation), 'DD/MM/YYYY') AS date,
                        'facture' AS source
                    FROM bc
                    LEFT JOIN numero_facture nf ON nf.bc_id = bc.id
                    WHERE bc.statut_id = 3 AND bc.flag_suppression = 0

                    UNION ALL

                    -- paiement_credit
                    SELECT 
                        nf.id AS numero_facture_id,
                        pc.montant, 
                        pc.date_paiement AS date_real,
                        TO_CHAR(DATE(pc.date_paiement), 'DD/MM/YYYY') AS date,
                        'paiement à credit' AS source
                    FROM paiement_credit pc
                    LEFT JOIN bc ON pc.bc_id = bc.id
                    LEFT JOIN numero_facture nf ON nf.bc_id = bc.id
                    WHERE pc.flag_suppression = 0 AND  bc.statut_id = 3 AND bc.flag_suppression = 0
                ) AS t
                ORDER BY date_real DESC;
                ";
        return $this->db->query($sql)->getResult();
    }

    public function get_recette_chart_data()
    {
        $db = \Config\Database::connect();
        $periode = $this->request->getPost('periode'); // récupération via POST
        switch ($periode) {
            case 'quotidien':
                $sql = "SELECT * FROM (
                    SELECT 
                        label,
                        SUM(total) AS total
                    FROM (
                        -- Totaux venant de la table bc
                        SELECT 
                        TO_CHAR(DATE(bc.date_validation), 'DD/MM/YYYY') AS label,
                            SUM(COALESCE(bc.montant_paye, 0) + COALESCE(p.total_paye, 0)) AS total
                        FROM bc
                        LEFT JOIN (
                            SELECT 
                                bc_id,
                                DATE(date_paiement) AS date_paiement,
                                SUM(montant) AS total_paye
                            FROM paiement_credit
                            WHERE flag_suppression = 0
                            GROUP BY bc_id, DATE(date_paiement)
                        ) AS p 
                            ON p.bc_id = bc.id 
                            AND DATE(p.date_paiement) = DATE(bc.date_validation)
                        WHERE bc.statut_id = 3 
                          AND bc.flag_suppression = 0
                        GROUP BY DATE(bc.date_validation)

                        UNION ALL

                        -- Dates paiement sans bc ou bc non valide
                        SELECT 
                               TO_CHAR(DATE(pc.date_paiement), 'DD/MM/YYYY') AS label,
                            SUM(pc.montant) AS total
                        FROM paiement_credit pc
                        LEFT JOIN bc 
                            ON bc.id = pc.bc_id 
                            AND DATE(bc.date_validation) = DATE(pc.date_paiement)
                        WHERE pc.flag_suppression = 0
                          AND (bc.id IS NULL OR bc.statut_id != 3 OR bc.flag_suppression != 0)
                        GROUP BY DATE(pc.date_paiement)
                    ) AS result
                    GROUP BY label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            case 'hebdomadaire':
                $sql = "SELECT * FROM (
                    SELECT 
                        label,
                        SUM(total) AS total
                    FROM (
                        SELECT 
                            CONCAT('Semaine ', EXTRACT(WEEK FROM bc.date_validation)) AS label,
                            SUM(COALESCE(bc.montant_paye, 0) + COALESCE(p.total_paye, 0)) AS total
                        FROM bc
                        LEFT JOIN (
                            SELECT 
                                bc_id,
                                EXTRACT(WEEK FROM date_paiement) AS semaine_paiement,
                                SUM(montant) AS total_paye
                            FROM paiement_credit
                            WHERE flag_suppression = 0
                            GROUP BY bc_id, EXTRACT(WEEK FROM date_paiement)
                        ) AS p 
                            ON p.bc_id = bc.id 
                            AND EXTRACT(WEEK FROM bc.date_validation) = p.semaine_paiement
                        WHERE bc.statut_id = 3 
                          AND bc.flag_suppression = 0
                        GROUP BY EXTRACT(WEEK FROM bc.date_validation)

                        UNION ALL

                        SELECT 
                            CONCAT('Semaine ', EXTRACT(WEEK FROM pc.date_paiement)) AS label,
                            SUM(pc.montant) AS total
                        FROM paiement_credit pc
                        LEFT JOIN bc 
                            ON bc.id = pc.bc_id 
                            AND EXTRACT(WEEK FROM bc.date_validation) = EXTRACT(WEEK FROM pc.date_paiement)
                        WHERE pc.flag_suppression = 0
                          AND (bc.id IS NULL OR bc.statut_id != 3 OR bc.flag_suppression != 0)
                        GROUP BY EXTRACT(WEEK FROM pc.date_paiement)
                    ) AS result
                    GROUP BY label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            case 'mensuel':
                $sql = "SELECT * FROM (
                    SELECT 
                        label,
                        SUM(total) AS total
                    FROM (
                        SELECT 
                            TO_CHAR(bc.date_validation, 'MM/YYYY') AS label,
                            SUM(COALESCE(bc.montant_paye, 0) + COALESCE(p.total_paye, 0)) AS total
                        FROM bc
                        LEFT JOIN (
                            SELECT 
                                bc_id,
                                TO_CHAR(date_paiement, 'MM/YYYY') AS mois_paiement,
                                SUM(montant) AS total_paye
                            FROM paiement_credit
                            WHERE flag_suppression = 0
                            GROUP BY bc_id, TO_CHAR(date_paiement, 'MM/YYYY')
                        ) AS p 
                            ON p.bc_id = bc.id 
                            AND TO_CHAR(bc.date_validation, 'MM/YYYY') = p.mois_paiement
                        WHERE bc.statut_id = 3 
                          AND bc.flag_suppression = 0
                        GROUP BY TO_CHAR(bc.date_validation, 'MM/YYYY')

                        UNION ALL

                        SELECT 
                            TO_CHAR(pc.date_paiement, 'MM/YYYY') AS label,
                            SUM(pc.montant) AS total
                        FROM paiement_credit pc
                        LEFT JOIN bc 
                            ON bc.id = pc.bc_id 
                            AND TO_CHAR(bc.date_validation, 'MM/YYYY') = TO_CHAR(pc.date_paiement, 'MM/YYYY')
                        WHERE pc.flag_suppression = 0
                          AND (bc.id IS NULL OR bc.statut_id != 3 OR bc.flag_suppression != 0)
                        GROUP BY TO_CHAR(pc.date_paiement, 'MM/YYYY')
                    ) AS result
                    GROUP BY label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            case 'annuel':
                $sql = "SELECT * FROM (
                    SELECT 
                        label,
                        SUM(total) AS total
                    FROM (
                        SELECT 
                            EXTRACT(YEAR FROM bc.date_validation) AS label,
                            SUM(COALESCE(bc.montant_paye, 0) + COALESCE(p.total_paye, 0)) AS total
                        FROM bc
                        LEFT JOIN (
                            SELECT 
                                bc_id,
                                EXTRACT(YEAR FROM date_paiement) AS annee_paiement,
                                SUM(montant) AS total_paye
                            FROM paiement_credit
                            WHERE flag_suppression = 0
                            GROUP BY bc_id, EXTRACT(YEAR FROM date_paiement)
                        ) AS p 
                            ON p.bc_id = bc.id 
                            AND EXTRACT(YEAR FROM bc.date_validation) = p.annee_paiement
                        WHERE bc.statut_id = 3 
                          AND bc.flag_suppression = 0
                        GROUP BY EXTRACT(YEAR FROM bc.date_validation)

                        UNION ALL

                        SELECT 
                            EXTRACT(YEAR FROM pc.date_paiement) AS label,
                            SUM(pc.montant) AS total
                        FROM paiement_credit pc
                        LEFT JOIN bc 
                            ON bc.id = pc.bc_id 
                            AND EXTRACT(YEAR FROM bc.date_validation) = EXTRACT(YEAR FROM pc.date_paiement)
                        WHERE pc.flag_suppression = 0
                          AND (bc.id IS NULL OR bc.statut_id != 3 OR bc.flag_suppression != 0)
                        GROUP BY EXTRACT(YEAR FROM pc.date_paiement)
                    ) AS result
                    GROUP BY label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            default:
                return $this->response->setJSON([]);
        }


        $query = $db->query($sql);
        $results = $query->getResultArray(); // ✅ tableau associatif propre
        return $this->response->setJSON($results);
    }

    public function get_article_chart_data()
    {
        $db = \Config\Database::connect();
        $periode = $this->request->getPost('periode'); // récupération via POST
        switch ($periode) {
            case 'quotidien':
                $sql = "SELECT * FROM (
                    SELECT article_reference, label, SUM(total) AS total
                    FROM (
                        SELECT 
                            article.reference AS article_reference,
                            DATE(bc.date_validation) AS label,
                            SUM(bc_detail_article.quantite) AS total
                        FROM bc_detail_article
                        INNER JOIN article 
                            ON article.id = bc_detail_article.article_id
                            AND article.flag_suppression = 0
                        INNER JOIN bc 
                            ON bc.id = bc_detail_article.bc_id
                            AND bc.flag_suppression = 0
                        GROUP BY article.reference, DATE(bc.date_validation)
                    ) AS result
                    GROUP BY article_reference, label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            case 'hebdomadaire':
                $sql = "SELECT * FROM (
                    SELECT article_reference, label, SUM(total) AS total
                    FROM (
                        SELECT 
                            article.reference AS article_reference,
                            CONCAT('Semaine ', EXTRACT(WEEK FROM bc.date_validation)) AS label,
                            SUM(bc_detail_article.quantite) AS total
                        FROM bc_detail_article
                        INNER JOIN article 
                            ON article.id = bc_detail_article.article_id
                            AND article.flag_suppression = 0
                        INNER JOIN bc 
                            ON bc.id = bc_detail_article.bc_id
                            AND bc.flag_suppression = 0
                        GROUP BY article.reference, EXTRACT(WEEK FROM bc.date_validation)
                    ) AS result
                    GROUP BY article_reference, label
                    ORDER BY MIN(label) DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            case 'mensuel':
                $sql = "SELECT * FROM (
                    SELECT article_reference, label, SUM(total) AS total
                    FROM (
                        SELECT 
                            article.reference AS article_reference,
                            TO_CHAR(bc.date_validation, 'YYYY-MM') AS label,
                            SUM(bc_detail_article.quantite) AS total
                        FROM bc_detail_article
                        INNER JOIN article 
                            ON article.id = bc_detail_article.article_id
                            AND article.flag_suppression = 0
                        INNER JOIN bc 
                            ON bc.id = bc_detail_article.bc_id
                            AND bc.flag_suppression = 0
                        GROUP BY article.reference, TO_CHAR(bc.date_validation, 'YYYY-MM')
                    ) AS result
                    GROUP BY article_reference, label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            case 'annuel':
                $sql = "SELECT * FROM (
                    SELECT article_reference, label, SUM(total) AS total
                    FROM (
                        SELECT 
                            article.reference AS article_reference,
                            EXTRACT(YEAR FROM bc.date_validation) AS label,
                            SUM(bc_detail_article.quantite) AS total
                        FROM bc_detail_article
                        INNER JOIN article 
                            ON article.id = bc_detail_article.article_id
                            AND article.flag_suppression = 0
                        INNER JOIN bc 
                            ON bc.id = bc_detail_article.bc_id
                            AND bc.flag_suppression = 0
                        GROUP BY article.reference, EXTRACT(YEAR FROM bc.date_validation)
                    ) AS result
                    GROUP BY article_reference, label
                    ORDER BY label DESC
                    LIMIT 12
                ) AS final_result
                ORDER BY label ASC";
                break;

            default:
                return $this->response->setJSON([]);
        }

        $query = $db->query($sql);
        $results = $query->getResultArray(); // ✅ tableau associatif propre
        return $this->response->setJSON($results);
    }
}
