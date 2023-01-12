<?php

namespace App\Controller\Blog;

use App\Form\PostType;
use App\Form\SearchType;
use App\Entity\Post\Post;
use App\Model\SearchData;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('pages/post/index.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        return $this->render('pages/post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $postRepository->findPublished($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/article/create', name: 'post.add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PostRepository $postRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $form->getData()->prePersist();
            $form->getData()->setCreatedAt(new \DateTimeImmutable());
            $form->getData()->setUser($this->getUser());
                        
            $image = $form->get("image")->getData();
            if ($image != null) {
                $imageName = md5(strtolower(uniqid().'.'.$image->getClientOriginalName())).'.'.$image->guessExtension();
                $image->move("static/images", $imageName);
                $form->getData()->setImage($imageName);
            }
            
            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('post.show', ["slug" => $slug]);
        }

        return $this->renderForm('pages/post/create.html.twig', [
            'postForm' => $form,
        ]);
    }

    #[Route('/article/{slug}/edit', name: 'post.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Post $post, Request $request, PostRepository $postRepository, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() !== $post->getUser()) {
            return $this->redirectToRoute('post.show', ["slug" => $post->getSlug()]);
        };

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $form->getData()->prePersist();
            $form->getData()->preUpdate();
            
            // foreach ($form->get('categories')->getData() as $value) {
            //     $post->addCategory($value);
            // }

            $image = $form->get("image")->getData();
            if ($image != null) {
                $imageName = md5(strtolower(uniqid().'.'.$image->getClientOriginalName())).'.'.$image->guessExtension();
                $image->move("static/images", $imageName);
                $post->getImage() ? $this->removeFile($post->getImage()) : '';
                $form->getData()->setImage($imageName);
            }

            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('post.show', ["slug" => $slug]);
        }

        return $this->renderForm('pages/post/edit.html.twig', [
            'postForm' => $form,
        ]);
    }

    #[Route('/article/{slug}/delete', name: 'post.delete', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Post $post, Request $request, PostRepository $postRepository, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() !== $post->getUser()) {
            return $this->redirectToRoute('post.show', ["slug" => $post->getSlug()]);
        };

        if($post->getImage()) {
            $this->removeFile($post->getImage());
        };
        $manager->remove($post);
        $manager->flush();

        return $this->redirectToRoute('post.index');
    }

    #[Route('/article/user', name: 'post.user', methods: ['GET'])]
    public function getPostUser(Request $request, PostRepository $postRepository, PaginatorInterface $paginatorInterface)
    {
        $posts = $postRepository->findBy(['user' => $this->getUser()]);

        return $this->render('pages/post/index.html.twig', [
            'posts' => $paginatorInterface->paginate($posts, $request->query->getInt('page', 1), 6)
        ]);

    }
    
    #[Route('/article/{slug}', name: 'post.show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('pages/post/show.html.twig', [
            'post' => $post
        ]);
    }

    public function removeFile(string $fileName)
    {
        $file_path = $this->getParameter('images_dir') . '/' . $fileName;
        if (file_exists($file_path)) unlink($file_path);
    }

    #[Route('/fetch/{search}', name: 'app_fetch')]
    public function getSearchfilter(PostRepository $postRepository, string $search): JsonResponse
    {
        if ($search) {
            $result = $postRepository->findBySearchView($search);
        } else 
        {
            $result-> $repo->findall();
        }

        return $this->json($result, 200, [], ['groups' => 'search:view']);
    }

}
