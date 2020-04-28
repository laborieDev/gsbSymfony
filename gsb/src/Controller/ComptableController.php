<?php

namespace App\Controller;

use App\Entity\FicheHorsForfait;
use App\Repository\EtatRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\FicheHorsForfaitRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ComptableController extends AbstractController
{
    /**
     * @Route("/comptable", name="comptable")
     */
    public function index()
    {
        return $this->render('comptable/index.html.twig');
    }

    /**
     * @Route("/comptable/validation_fiches/{etat}/{id}", name="valid_fiche")
     */
    public function validationFiche(FicheHorsForfait $fiche, EtatRepository $repo, ManagerRegistry $mr, string $etat)
    {
        $etat = $repo->findOneBy(['idEtat' => $etat]);
        $fiche->setIdEtat($etat);

        $manager = $mr->getManager();
        $manager->persist($fiche);
        $manager->flush();

        return $this->redirectToRoute('valid_fiches',[
            "valid_message" => "Action bien prise en compte !"
        ]);
    }

    /**
     * @Route("/comptable/validation_fiches", name="valid_fiches")
     */
    public function validation(ManagerRegistry $mr)
    {
        $conn = $mr->getManager()->getConnection();

        $sql = "
            SELECT f.id, f.libelle, f.montant, f.date, f.nb_justificatifs, u.username
            FROM fiche_hors_forfait f, etat e, user u
            WHERE e.id_etat = 'CR'
            AND e.id = f.id_etat_id
            AND u.id = f.id_visiteur_id
            ORDER BY f.id DESC
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $fiches = $stmt->fetchAll();

        if(isset($_GET['valid_message']))
            return $this->render('comptable/validation.html.twig',[
                'fiches' => $fiches,
                'valid_message' => $_GET['valid_message']
            ]);

        if(isset($_GET['error_message']))
            return $this->render('comptable/validation.html.twig',[
                'fiches' => $fiches,
                'error_message' => $_GET['error_message']
            ]);

        return $this->render('comptable/validation.html.twig',[
            'fiches' => $fiches
        ]);
    }

    /**
     * @Route("/comptable/remboursement_fiches", name="rembourser_fiches")
     */
    public function remboursement(ManagerRegistry $mr)
    {
        $conn = $mr->getManager()->getConnection();

        $sql = "
            SELECT f.id, f.libelle, f.montant, f.date, f.nb_justificatifs, u.username
            FROM fiche_hors_forfait f, etat e, user u
            WHERE e.id_etat = 'VA'
            AND e.id = f.id_etat_id
            AND u.id = f.id_visiteur_id
            ORDER BY f.id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $fiches = $stmt->fetchAll();

        return $this->render('comptable/remboursement.html.twig',[
            'fiches' => $fiches
        ]);
    }
}
