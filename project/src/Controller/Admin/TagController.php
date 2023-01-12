<?php

namespace App\Controller\Admin;

use App\Form\TagType;
use App\Entity\Post\Tag;
use App\Repository\Post\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/tag')]
class TagController extends AbstractController
{
    #[Route('/create', name: 'tag.add', methods: ['GET', 'POST'])]
    public function new(Request $request, TagRepository $tagRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TagType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $form->getData()->prePersist();
            $form->getData()->setCreatedAt(new \DateTimeImmutable());
            
            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('post.index');
        }

        return $this->renderForm('pages/tag/create.html.twig', [
            'tagForm' => $form,
        ]);
    }

    #[Route('/{slug}/edit', name: 'tag.edit', methods: ['GET', 'POST'])]
    public function edit(Tag $tag, Request $request, TagRepository $tagRepository, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $form->getData()->prePersist();
            $form->getData()->preUpdate();

            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('post.index');
        }

        return $this->renderForm('pages/tag/edit.html.twig', [
            'tagForm' => $form,
        ]);
    }
}
