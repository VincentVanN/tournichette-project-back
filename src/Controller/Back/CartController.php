<?php


namespace App\Controller\Back;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Form\CartType;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
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
    public function new(Request $request, CartRepository $cartRepository, ProductRepository $productRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {
        $cart = new Cart();

        $allCategories = $categoryRepository->findAll();
        $allFruits = $productRepository->findByCategory('fruits');
        $allVegetables = $productRepository->findByCategory('legumes');
        $allGroceries = $productRepository->findByCategory('epicerie');

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $allFormProducts = $request->get('products');
            $allFormProducts = filter_var_array($allFormProducts, FILTER_SANITIZE_NUMBER_INT);
            $allFormQuantity = $request->get('quantity');

            for ($i=0; $i < count($allFormProducts) ; $i++) { 
                $cartProduct = new CartProduct;

                $insertedProduct = $productRepository->find($allFormProducts[$i]);
                if ($insertedProduct !== null && $allFormQuantity[$i] > 0) {
                    $cartProduct->setProduct($insertedProduct);

                    $allFormQuantity[$i] = filter_var($allFormQuantity[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
                    $cartProduct->setQuantity(($allFormQuantity[$i]));
                    
                    $cart->addCartProduct($cartProduct);
                    $em->persist($cartProduct);
                }
                // TODO errors if $insertedProduct is null
            }

            $cart->setOnSale(
                count($cart->getCartProducts()) === 0 ? false : true
            );
            $cart->setArchived(false);
            $em->persist($cart);
            $em->flush();

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

    // /**
    //  * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
    //  */
    // public function edit(Request $request, Cart $cart, ProductRepository $productRepository, CartRepository $cartRepository): Response
    // {
    //     $form = $this->createForm(CartType::class, $cart);
    //     $form->handleRequest($request);
    //     $allProduct = $productRepository->findAll();

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // on slugify le titre fournit par le user avant de l'enregistrer en BDD
    //         // $cart->setSlug($mySlugger->slugify($cart->getTitle()));
    //         $cartRepository->add($cart, true);

    //         return $this->redirectToRoute('app_back_cart_list', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('back/cart/edit.html.twig', [
    //         'cart' => $cart,
    //         'form' => $form,
    //     ]);
    // }

    /**
     * @Route("/{id}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Cart $cart, CartRepository $cartRepository, CartProductRepository $cartProductRepository): Response
    {
        if ($this->isDeletable($cart) === true) {
            if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
                $cartProductRepository->removeAllFromCart($cart);
                $cartRepository->remove($cart, true);
            }
        } elseif ($this->isDeletable($cart) === false) {
            $cart->setArchived(true);
        }
        // dd($this->isDeletable($cart));
        return $this->redirectToRoute('app_back_cart_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Verify if a cart is linked with orders
     */
    public function isDeletable(?Cart $cart): bool
    {
        $cartOrders = $cart->getCartOrders();

        if (count($cartOrders) > 0) {
            return false;
        }

        return true;
    }

}