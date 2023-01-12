<?php

namespace App\Form;

use App\Entity\Post\Tag;
use App\Entity\Post\Post;
use App\Entity\Post\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => array(
                    'placeholder' => 'Ajouter un Titre'
                )
            ])
            ->add('slug', HiddenType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (jpg)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'mimeTypes' => 'image/*',
                        'mimeTypesMessage' => 'Merci de choisir une image inférieur à 1MG',
                    ])
                ]
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tag',
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])            
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => array(
                    'placeholder' => 'Ajouter votre description'
                )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
