<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\MakeOrderType;
use App\Service\Orders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrdersController extends AbstractController
{
    /**
     * @Route("/orders", name="orders")
     */
    public function index()
    {
        return $this->render('orders/index.html.twig', [
            'controller_name' => 'OrdersController',
        ]);
    }

    /**
     * @Route("/orders/add-to-cart/{id}/{quantity}", name="orders_add_to_cart")
     *
     * @throws
     */
    public function addToCart(Orders $orders, Request $request, Product $product, $quantity = 1)
    {
        $orders->addToCart($product, $this->getUser(), $quantity);

        if ($request->isXmlHttpRequest()) {
            return $this->cartInHeader($orders);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/orders/cart-in-header", name="orders_cart_in_header")
     *
     * @throws
     */
    public function cartInHeader(Orders $orders)
    {
        $cart = $orders->getCartFromSession($this->getUser());

        return $this->render('orders/cart_in_header.html.twig', ['cart' => $cart]);
    }

    /**
     * @Route("/cart", name="orders_cart")
     *
     * @throws
     */
    public function cart(Orders $orders)
    {
        return $this->render('orders/cart.html.twig', [
            'cart' => $orders->getCartFromSession($this->getUser())
        ]);
    }

    /**
     * @Route("/cart/update-quantity/{id}", name="orders_update_item_quantity")
     */
    public function updateItemQuantity(OrderItem $item, Orders $orders, Request $request)
    {
        $quantity = (int)$request->request->get('quantity');

        if ( $quantity < 1 || $quantity > 1000 ) {
            throw new \InvalidArgumentException();
        }

        $cart = $orders->updateItemQuantity($item, $quantity);

        return new JsonResponse(
            $this->renderView('orders/cart.json.twig', ['cart' => $cart]),
            200,
            [],
            true
        );
    }

    /**
     * @Route("/cart/remove-item/{id}", name="orders_remove_item")
     */
    public function removeItem(OrderItem $item, Orders $orders, Request $request)
    {
        $cart = $orders->removeItem($item);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(
                $this->renderView('orders/cart.json.twig', ['cart' => $cart]),
                200,
                [],
                true
            );
        }

        return $this->redirectToRoute('orders_cart');
    }

    /**
     * @Route("/cart/checkout", name="orders_checkout")
     * @throws
     */
    public function checkout(Orders $orders, Request $request)
    {
        $cart = $orders->getCartFromSession($this->getUser());
        $form = $this->createForm(MakeOrderType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orders->checkout($cart);

            return $this->redirectToRoute('orders_success');
        }

        return $this->render('orders/checkout.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cart/success", name="orders_success")
     */
    public function success()
    {
        return $this->render('orders/success.html.twig');
    }

}
