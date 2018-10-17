<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Orders
{
    const CART_SESSION_NAME = 'shoppingCartId';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $adminEmail;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        ParameterBagInterface $parameterBag,
        Mailer $mailer
    ) {
        $this->em = $entityManager;
        $this->session = $session;
        $this->adminEmail = $parameterBag->get('admin_email');
        $this->mailer = $mailer;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function addToCart(Product $product, ?User $user, $quantity = 1)
    {
        $order = $this->getCartFromSession($user);
        $items = $order->getItems();

        if (isset($items[$product->getId()])) {
            $item = $items[$product->getId()];
            $item->addQuantity($quantity);
        } else {
            $item = new OrderItem();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $order->addItem($item);
        }

        $this->saveCart($order);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMInvalidArgumentException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    public function getCartFromSession(?User $user): Order
    {
        $orderId = $this->session->get(self::CART_SESSION_NAME);

        if ($orderId) {
            /** @var Order $order */
            $order = $this->em->find(Order::class, $orderId);
        } else {
            $order = null;
        }

        if (!$order) {
            $order = new Order();
            $order->setUser($user);
        }

        return $order;
    }

    /**
     * @param OrderItem $item
     * @param int $quantity
     *
     * @return Order
     *
     * @throws
     */
    public function updateItemQuantity(OrderItem $item, int $quantity): Order
    {
        $item->setQuantity($quantity);
        $this->em->flush();

        return $item->getOrder();
    }

    public function removeItem(OrderItem $item): Order
    {
        $order = $item->getOrder();
        $order->removeItem($item);
        $this->em->remove($item);
        $this->em->flush();

        return $order;
    }

    public function checkout(Order $order)
    {
        $order->setStatus(Order::STATUS_ORDERED);
        $this->em->flush();
        $this->removeCart();
        $this->mailer->send($this->adminEmail, 'orders/admin.email.twig', ['order' => $order]);
    }

    private function saveCart(Order $order)
    {
        $this->em->persist($order);
        $this->em->flush();
        $this->session->set(self::CART_SESSION_NAME, $order->getId());
    }

    private function removeCart()
    {
        $this->session->remove(self::CART_SESSION_NAME);
    }

}
