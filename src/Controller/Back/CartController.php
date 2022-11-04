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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


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
            'carts' => $cartRepository->findBy(['archived' => false], ['name' => 'ASC']),
        ]);
    }
 
    /**
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
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

    /**
     * @Route("/{id}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Cart $cart, CartRepository $cartRepository, CartProductRepository $cartProductRepository, EntityManagerInterface $em): Response
    {
        if ($this->isDeletable($cart) === true) {
            if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
                $cartProductRepository->removeAllFromCart($cart);
                $cartRepository->remove($cart, true);
            }
        } else {
            $cart->setArchived(true);
            $em->flush();
        }
        return $this->redirectToRoute('app_back_cart_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Modify the onSale status of a cart
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/onsale/{id<\d+>}", name="_sale-status", methods={"POST"})
     */
    public function changeOnSaleStatus(Cart $cart, EntityManagerInterface $em)
    {
        if ($cart !== null) {
            $cart->setOnSale(!$cart->isOnSale());
            $em->flush();
            $data['cartId'] = $cart->getId();
            return $this->json($data, Response::HTTP_OK);
        }
    }

    /**
     * Verify if a cart is linked with orders
     */
    public function isDeletable(?Cart $cart): bool
    {
        if (count($cart->getCartOrders()) > 0) {
            return false;
        }

        return true;
    }

}