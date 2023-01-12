<?php

namespace App\Controller\Admin;

use App\Form\CategoryType;
use App\Entity\Post\Category;
use App\Repository\Post\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    #[Route('/create', name: 'category.add', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $form->getData()->prePersist();
            $form->getData()->setCreatedAt(new \DateTimeImmutable());
            
            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('post.index');
        }

        return $this->renderForm('pages/category/create.html.twig', [
            'categoryForm' => $form,
        ]);
    }

    #[Route('/{slug}/edit', name: 'category.edit', methods: ['GET', 'POST'])]
    public function edit(category $category, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(CategoryType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $form->getData()->prePersist();
            $form->getData()->preUpdate();

            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('post.index');
        }

        return $this->renderForm('pages/category/edit.html.twig', [
            'categoryForm' => $form,
        ]);
    }
}
