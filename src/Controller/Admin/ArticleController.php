<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\ConfirmationType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/article", name="admin_article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(ArticleRepository $repository)
    {
        return $this->render('admin/article/index.html.twig', [
            'articles' => $repository->findAll(),
        ]);
    }

	/**
	 * @Route("/new", name="add")
	 */
	public function add(Request $request, EntityManagerInterface $entityManager)
	{
		$form = $this->createForm(ArticleType::class);

		/*
		 * handleRequest permet au formulaire de récupérer les données en POST et de procéder à la validation
		 */
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/*
			 * getData() permet de récupere les données de formulaire, elle retourne par défaut un tableau des champs du formulaire ou il retourne un objet de la classe a laquelle il est lié
			 */
			/** @var Article $article */
			$article = $form->getData();

			$entityManager->persist($article);
			$entityManager->flush();

			$this->addFlash('success', 'The article has been create.');
			return $this->redirectToRoute('admin_article_edit',[
				'id' => $article->getId()
			]);
		}

		return $this->render('admin/article/add.html.twig',[
			'article_form' => $form->createView()
	]);
    }

	/**
	 * @Route("/edit/{id}", name="edit")
	 */
	public function edit(Article $article, Request $request, EntityManagerInterface $entityManager)
	{
		/*
		 * on peut pré-remplir un formulaire en passant un 2ème  argument à createForm
		 * On passe un tableau associatif ou un objet si le formulaire est lié à une classe
		 */
		$form = $this->createForm(ArticleType::class, $article);
		// le formulaire va directement modifier l'objet
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/*
			 * on n'a pas besoin d'appeler $form->getData()->
			 * l'objet $article est directement modifié par le formulaire
			 * on a pas besoin d'appeler $entityManager->
			 * persistDoctrine connait déla cet objet (il existe en base de
			 * il sera automatiquement mis à jour
			 */
			$entityManager->flush();
			$this->addFlash('success', 'Article mis à jour');
		}

		return $this->render('admin/article/edit.html.twig',[
			'article' => $article,
			'article_form' => $form->createView(),
		]);
    }

    /**
	 * @Route("/{id}/delete", name="delete")
	 * On récupère les arguments par "autowiring"-> Symfony lit notre code pour nous envoyer les arguments demandés
	 */
	public function delete(Article $article, Request $request, EntityManagerInterface $entityManager)
	{
		$form = $this->createForm(ConfirmationType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->remove($article);
			$entityManager->flush();
			/*
			 * sprint() sert à formater une chaine de caractères, le %s est un emplacement pour une chaine de caractères
			 */
			$this->addFlash('info', sprintf('The article "%" has been deleted.', $article->getTitle()));
			return $this->redirectToRoute('admin_article_list');
		}
		return $this->render('admin/article/delete.html.twig',[
			'article' => $article,
			'delete_form' => $form->createView(),
		]);
    }
    /**
	 * @Route("/{id}/publish/{token}", name="publish")
	 * le paramètre token servira à vérifier que l'action a bien été demandée par l'administrateur connecté
	 * (protection contre les attaques CSRF)
	 */

	public function publish(Article $article, string $token, EntityManagerInterface $entityManager)
	{
		/*
		 * On doit nommer les jetons CSRF
		 * Symfony va comparer le jeton qu'il a enregistré en session avec ce que l'on récupère dans l'adresse
		 */
		if ($this->isCsrfTokenValid('article_publish', $token) === false) {
			$this->addFlash('danger', 'Le jeton est invalide');
			return $this->redirectToRoute('admin_article_edit', [
				'id' => $article->getId(),
			]);
		}

		$article->setPublishedAt(new \DateTime());
		$entityManager->flush();

		$this->addFlash('success', 'L\'article a été publié.');
		return $this->redirectToRoute('admin_article_edit',[
				'id' => $article->getId(),
			]);
    }
}
