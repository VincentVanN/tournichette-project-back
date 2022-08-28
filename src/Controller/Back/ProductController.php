<?php

namespace App\Controller\Back;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\OrderProduct;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @Route("/back/product", name="app_back_product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("", name="_list", methods={"GET"})
     */
    public function list(ProductRepository $productRepository): Response
    {
        return $this->render('back/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_back_product_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
        // /**
        // * @Route("/dash", name="_dashboard", methods={"GET"})
        // */
        // public function dash(Request $request, OrderRepository $order, ProductRepository $product)
        // {
        //     //$session = $request->getSession();
            
        //     $idOrder = count($order->findAll());
        //     $stock = 0;
        //     $allProducts = $product->findAll();
            
        //     for ($i = 0; $i < count($allProducts); $i++) {
        //         $stock = $stock + $allProducts[$i]->getStock();
        //     }
        //     return $this->render('back/product/index.html.twig', [
                
        //         'idOrder' => $idOrder,
        //         'stock' => $stock,
        //         'products' => $product,
        //     ]);
        // }

    /**
     * @Route("/{id}", name="_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('back/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on slugify le titre fournit par le user avant de l'enregistrer en BDD
            // $product->setSlug($mySlugger->slugify($product->getTitle()));
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_back_product_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_back_product_list', [], Response::HTTP_SEE_OTHER);
    }
        
}