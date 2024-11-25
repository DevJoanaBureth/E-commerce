<?php

// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(EntityManagerInterface $manager): Response
    {
        // Récupérer tous les produits depuis la base de données
        $products = $manager->getRepository(Product::class)->findAll();

        // Passer les produits à la vue
        return $this->render('product/index.html.twig', [
            'products' => $products, // Passer la liste des produits
        ]);
    }

    #[Route('/product/add', name: 'app_add_product')]
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        // Créer un nouveau produit
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le produit
            $manager->persist($product);
            $manager->flush();

            // Rediriger vers la liste des produits après ajout
            return $this->redirectToRoute('app_product');
        }

        // Rendu initial du formulaire d'ajout
        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
