<?php

namespace App\Controller\Back;

use App\Entity\Product;
use App\Utils\MySlugger;
use App\Form\ProductType;
use App\Utils\GetBaseUrl;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
        $unarchivedProducts = $productRepository->findBy(['archived' => false], ['name' => 'ASC']);
        return $this->render('back/product/index.html.twig', [
            'products' => $unarchivedProducts
        ]);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
                
        if ($form->isSubmitted() && $form->isValid()) {
           $product->setArchived(false);           
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_back_product_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="_show", methods={"GET"})
     */
    public function show(Product $product, GetBaseUrl $baseUrl): Response
    {
        if ($product->getImage() === null) {
            $product->setImage('placeholder.png');
        }
        return $this->render('back/product/show.html.twig', [
            'product' => $product,
            'baseUrl' => $baseUrl->getBaseUrl()
        ]);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/{id<\d+>}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_back_product_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product/edit.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/{id<\d+>}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $product->setArchived(true);
            $productRepository->add($product, true);
        }

        return $this->redirectToRoute('app_back_product_list', [], Response::HTTP_SEE_OTHER);
    }
        
}
