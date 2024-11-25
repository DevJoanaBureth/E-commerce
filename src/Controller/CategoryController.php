<?php

// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // Récupérer toutes les catégories depuis la base de données
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle catégorie
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $path = $this->getParameter('app.dir.public') . 'uploads/';

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'image de la catégorie (si elle est présente)
            $file = $form['image']->getData();
            
            if ($file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newName = 'uploads/' . $originalName . '-' . uniqid() . '.' . $file->guessExtension();
                $category->setImage($newName);
                
                try {
                    $file->move($path, $newName);
                } catch (FileException $e) {
                    // Afficher l'erreur si l'image ne peut pas être déplacée
                    echo $e->getMessage();
                }
            }

            // Enregistrer la catégorie dans la base de données
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category added successfully.');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        // Afficher une catégorie spécifique
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire pour modifier la catégorie
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications
            $entityManager->flush();

            $this->addFlash('success', 'Category updated successfully.');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            // Supprimer la catégorie
            $entityManager->remove($category);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Category deleted successfully.');

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
