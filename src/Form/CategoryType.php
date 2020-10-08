<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder// ajout champs
            ->add(
            	// nom champ de formulaire correspondant
				// au nom de l'attribut dans l'entité Category
            	'name',
				// type de champ de formulaire correspondant : input type text
				TextType::class,
				// tableau d'options pour le champ de formulaire
				[
					'label' => 'Name',
					'attr' => ['placeholder' => 'Category Name']
				]

			)// par défaut les champs ont l'attribut required, on ajoute cette option pour l'enlever
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
