<?php


namespace App\Controller\Back;

use App\Entity\Cart;
use App\Entity\Category;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/back/cart", name="app_back_cart")
 */
class CartController extends AbstractController
{
        /**
         * @Route("", name="_list", methods={"GET"})
         */
    public function list(CartRepository $cartRepository): Response
    {
        return $this->render('back/cart/index.html.twig', [
            'carts' => $cartRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CartRepository $cartRepository, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $cart = new Cart();

        $allCategories = $categoryRepository->findAll();
        $allFruits = $productRepository->findByCategory('fruits');
        $allVegetables = $productRepository->findByCategory('legumes');
        $allGroceries = $productRepository->findByCategory('epicerie');

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $cartRepository->add($cart, true);

            return $this->redirectToRoute('app_back_cart_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/cart/new.html.twig', [
            'cart' => $cart,
            'fruits' => $allFruits,
            'vegetables' => $allVegetables,
            'groceries' => $allGroceries,
            'categories' => $allCategories,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="_show", methods={"GET"})
     */
    public function show(Cart $cart): Response
    {
        return $this->render('back/cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Cart $cart, CartRepository $cartRepository): Response
    {
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on slugify le titre fournit par le user avant de l'enregistrer en BDD
            // $cart->setSlug($mySlugger->slugify($cart->getTitle()));
            $cartRepository->add($cart, true);

            return $this->redirectToRoute('app_back_cart_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/cart/edit.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Cart $cart, CartRepository $cartRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
            $cartRepository->remove($cart, true);
        }

        return $this->redirectToRoute('app_back_cart_list', [], Response::HTTP_SEE_OTHER);
    }

    /*
    public function record(?int $id = null)
    {
        $cart = $id === null ? new Cart() : Cart::find($id);
        $cart ->setName($name);
        $cart ->setSlug($slug);
        $cart ->setStock($stock);
        $cart ->setUnity($unity);
        $cart ->setPrice($price);
        $cart ->setQuantity($quantity);
       

        //if there is no error
        if (empty($errors)) {
            // we saved in BDD
            if ($cart->save()) {
                if ($id === null) {
                    // Si la sauvegarde a fonctionné, on redirige vers la liste des catégories.
                    return $this->redirectToRoute('app_back_cart_index', [], Response::HTTP_SEE_OTHER);
                } else {
                    // Si la sauvegarde a fonctionné, on redirige vers le formulaire d'édition en mode GET
                    return $this->redirectToRoute('app_back_cart_edit', [], Response::HTTP_SEE_OTHER);
                }
            } else {
                $errors[] = "La sauvegarde a échoué";
            }
        }
    }
    */
}