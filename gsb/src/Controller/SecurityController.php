<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
     /**
     * @Route("/", name="security_login")
     */
    public function login(UserRepository $repo)
    {
        return $this->render('security/index.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}

    /**
     * @Route("/admin/newuser", name="new_user")
     */
    public function registration(Request $req, ManagerRegistry $mr, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $user->setDateEmbauche(new \DateTime());
            $user->setIsComptable(false);
            $manager = $mr->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->render('navbar/index.html.twig');

        }        
        return $this->render('security/newuser.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
