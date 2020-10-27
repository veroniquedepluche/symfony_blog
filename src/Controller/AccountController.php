<?php

namespace App\Controller;

use App\Form\UserProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     * 
     * On peut limiter l'accès à une route ou un controller
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request,
    EntityManagerInterface $entityManagerInterface,
    UserPasswordEncoderInterface $encoder)
    {
    	// sauvegarde de l'adresse email en cas d'erreur
		$email = $this->getUser()->getEmail();

		// on peut récupérer le user actuellement connecté avec $this->getUser());
        $form = $this->createForm(UserProfilFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	//Récupération du champ plainPassword
			$password = $form->get('plainPassword')->getData();

			// on met à jour le mdp seulement si le champ est rempli
			if ($password !==null) {
				$hash = $encoder->encodePassword($this->getUser(), $password);
				$this->getUser()->setPassword($hash);
			}
			$entityManagerInterface->flush();
			$this->addFlash('success', 'Your information is up to date');

		} else {
        	/*
        	 * on remet l'adresse email original du user pour éviter qu'il soit déconnecté
        	 */
			$this->getUser()->setEmail($email);
		}

        // traitement du formulaire

        return $this->render('account/index.html.twig', [
            'profile_form' => $form->createView(),
        ]);
    }
}
