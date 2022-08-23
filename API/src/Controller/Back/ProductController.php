<?php

namespace App\Controller\Back;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/back/product", name="app_back_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('back/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on slugify le titre fournit par le user avant de l'enregistrer en BDD
            // plus besoin car on a fait un écouteur d'événements
            // $product->setSlug($mySlugger->slugify($product->getTitle()));

            $productRepository->add($product, true);

            return $this->redirectToRoute('app_back_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('back/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on slugify le titre fournit par le user avant de l'enregistrer en BDD
            // $product->setSlug($mySlugger->slugify($product->getTitle()));
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_back_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_back_product_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function record(?int $id = null)
    {
        $product = $id === null ? new Product() : Product::find($id);
        $product ->setName($name);
        $product ->setSlug($slug);
        $product ->setStock($stock);
        $product ->setUnity($unity);
        $product ->setPrice($price);
        $product ->setQuantity($quantity);
        $product ->setCategory($category);

        //if there is no error
        if (empty($errors)) {
            // we saved in BDD
            if ($category->save()) {
                if ($id === null) {
                    // Si la sauvegarde a fonctionné, on redirige vers la liste des catégories.
                    return $this->redirectToRoute('app_back_product_index', [], Response::HTTP_SEE_OTHER);
                } else {
                    // Si la sauvegarde a fonctionné, on redirige vers le formulaire d'édition en mode GET
                    return $this->redirectToRoute('app_back_product_edit', [], Response::HTTP_SEE_OTHER);
                }
            } else {
                $errors[] = "La sauvegarde a échoué";
            }
        }
    }

        /**
        * @Route("/dash", name="dashboard")
        */
        public function dash(Request $request, OrderRepository $order, ProductRepository $product, OrderProduct $orderproduct)
        {
            //$session = $request->getSession();
            
            $idOrder = count($order->findAll());
            $stocknumber = 0;
            $allProducts = $product->findAll();
            
            for ($i = 0; $i < count($allProducts); $i++) {
                $stocknumber = $stocknumber + $allProducts[$i]->getStock();
            }
            return $this->render('back/product/index.html.twig', [
                
                'idOrder' => $idOrder,
                'stockNumber' => $stocknumber,
            ]);
        }
}
