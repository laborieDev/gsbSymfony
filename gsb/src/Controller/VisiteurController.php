<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FicheFType;

use App\Form\FicheHfType;
use App\Entity\FicheForfait;
use App\Entity\FicheHorsForfait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VisiteurController extends AbstractController
{
    /**
     * @Route("/visiteur/newFicheFrais", name="new_fiche_forfait")
     * @Route("/visiteur/editFicheFrais/{id}", name="edit_fiche_forfait")
     */
    public function gestionFicheForfait(FicheForfait $fiche = null, Request $req, ManagerRegistry $mr, Security $sec)
    {
        $gestion = "Modifier";

        if(!$fiche){
            $fiche = new FicheForfait();
            $gestion = "Ajouter";
        }

        $form = $this->createForm(FicheFType::class, $fiche);
        
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){

            if(!$fiche->getId()){
                $fiche->setDate(new \DateTime())
                      ->setIdVisiteur($sec->getUser());
            }
            
            $manager = $mr->getManager();
            $manager->persist($fiche);
            $manager->flush();

            // return $this->redirectToRoute('fiche_forfait_show', ['id' => $fiche->getId()]);
        }

        return $this->render('visiteur/gestFicheFrais.html.twig', [
            'formFiche' => $form->createView(),
            'gestion' => $gestion
        ]);
    }

    /**
     * @Route("/visiteur/newFicheHf", name="new_fiche_hf")
     * @Route("/visiteur/editFicheHf/{id}", name="edit_fiche_hf")
     */
    public function gestionFicheForfaitHf(FicheHorsForfait $fiche = null, Request $req, ManagerRegistry $mr, Security $sec)
    {
        $gestion = "Modifier";

        if(!$fiche){
            $fiche = new FicheHorsForfait();
            $gestion = "Ajouter";
        }

        $form = $this->createForm(FicheHfType::class, $fiche);
        
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager = $mr->getManager();

            if(!$fiche->getId()){
                $etatRepo = $manager->getRepository("App\Entity\Etat");

                $fiche->setDate(new \DateTime())
                      ->setIdVisiteur($sec->getUser())
                      ->setIdEtat($etatRepo->findOneBy(['idEtat' => 'CR']));
            }
            
            $manager->persist($fiche);
            $manager->flush();

            // return $this->redirectToRoute('fiche_forfait_show', ['id' => $fiche->getId()]);
        }

        return $this->render('visiteur/gestFicheFraisHf.html.twig', [
            'formFiche' => $form->createView(),
            'gestion' => $gestion
        ]);
    }

    /**
     * @Route("/visiteur/mesFiches", name="get_fiches")
     */
    public function getFiches()
    {
        return$this->render('visiteur/getFichesFrais.html.twig');
    }
}
