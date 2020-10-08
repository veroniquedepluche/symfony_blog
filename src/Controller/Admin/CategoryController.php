<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\ConfirmationType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 *
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{
	/**
	 * @Route("/")
	 */
	public function index(CategoryRepository $categoryRepository)
	{
		$categorys = $categoryRepository->findAll();

		return $this->render(
			'admin/category/index.html.twig',
		[
			'categorys' => $categorys
		]);
	}

	/**
	 * L'id est optionnel et vaut null par défaut
	 * si on ne passe pas l'id dans l'url, on est en création
	 * si on passe un id, on est en modification
	 * @Route("/edition/{id}", defaults={"id": null})
	 */
	public function edit(Request $request,
						 EntityManagerInterface $entityManager,
						 CategoryRepository $categoryRepository, $id)
	{
		if (is_null($id)) {
			$category = new Category();
		} else { // modification
			$category = $categoryRepository->find($id);
		}

		// création du formulaire relié à la catégorie
		$form = $this->createForm(CategoryType::class, $category);

		// le formulaire analyse la requête et sette les valeurs des attributs Category avec les valeurs
		// saisies dans le formulaire s'il est envoyé
		$form->handleRequest($request);

		dump($category);

		// si le formulaire est sousmis
		if ($form->isSubmitted()) {
			// si les validations à partir des notes de @Assert dans l'entité Category sont ok
			if ($form->isValid()) {
				// quand on appelle la méthode flush(), la catégorie devra être enregistrée en bdd
				$entityManager->persist($category);
				// enregistrement en bdd
				$entityManager->flush();

				// enregistrement dans la session d'un message pour affichage unique
				$this->addFlash('success', 'The category is saved');
				// redirection vers la page
				return $this->redirectToRoute('app_admin_category_index');
			}
		}


		return $this->render('admin/category/edit.html.twig',
		[
			// pour pouvoir utiliser le formulaire dans le template
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/delete/{id}", name="admin_category_delete")
	 * Le ParamConverter (installé grace à sensio/framework-extra-bundle)
	 * permet de convertir les paramétres des routes.
	 * Ici, il va rechercher la Category en fonction de l'id présent dns l'adresse
	 */
	public function delete(Category $category, Request $request, EntityManagerInterface $entityManager)
	{
		$form = $this->createForm(ConfirmationType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			//dd('Delete category.');
			$entityManager->remove($category);
			$entityManager->flush();

			$this->addFlash('info', 'Category' . $category->getName() . 'has been delete');
			return $this->redirectToRoute('app_admin_category_index');
		}

		return $this->render('admin/category/delete.html.twig', [
			'delete_form' => $form->createView(),
			'category' => $category,
	]);
	}
}