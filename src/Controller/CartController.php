<?php

namespace App\Controller;

use App\Entity\CartHistory;
use App\Entity\Order;
use App\Repository\CartHistoryRepository;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


final class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_view')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getDetailedItems(),
            'total' => $cartService->getTotal(),
            'count' => $cartService->getCount(),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, CartService $cartService): JsonResponse
    {
        $cartService->add($id);

        return new JsonResponse([
            'success' => true,
            'count' => $cartService->getCount()
        ]);
    }
    #[Route('/cart/checkout', name: 'cart_checkout', methods: ['GET', 'POST'])]
    public function checkout(CartService $cartService, SessionInterface $session, OrderRepository $orderRepository, Request $request): Response
    {

        $items = $cartService->getDetailedItems();
        $total = $cartService->getTotal();

        if (empty($items)) {
            $this->addFlash('error', 'Your cart is empty.');
            return $this->redirectToRoute('cart_view');
        }
        if ($request->isMethod('POST')) {
            $paymentMethod = $request->request->get('payment_method');

            if ($paymentMethod === 'stripe') {
                return $this->redirectToRoute('stripe_checkout');
            }
            if ($paymentMethod === 'paypal') {
                return $this->redirectToRoute('paypal_checkout');
            }
        }
        return $this->render('cart/checkout.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/cart/stripe/checkout', name: 'stripe_checkout')]
    public function stripeCheckout(CartService $cartService, Request $request): Response
    {
        $cartItems = $cartService->getDetailedItems();

        if (empty($cartItems)) {
            $this->addFlash('error', 'Your cart is empty.');
            return $this->redirectToRoute('cart_view');
        }

        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['product']->getName(),
                    ],
                    'unit_amount' => intval($item['product']->getPrice() * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->generateUrl('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cart_view', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        } catch (ApiErrorException $e) {
            dd('Stripe error: ' . $e->getMessage());
        } catch (\Exception $e) {
            dd('General error: ' . $e->getMessage());
        }

        return new JsonResponse(['url' => $session->url]);

    }

    #[Route('/stripe/success', name: 'stripe_success')]
    public function stripeSuccess(
        CartService $cartService,
        SessionInterface $session,
        OrderRepository $orderRepository,
        CartHistoryRepository $cartHistoryRepo,
        Security $security
    ): Response
    {
        $items = $cartService->getDetailedItems();
        $total = $cartService->getTotal();

        if (empty($items)) {
            $this->addFlash('error', 'Your cart is empty.');
            return $this->redirectToRoute('cart_view');
        }

        $orderReference = strtoupper(uniqid('ORD-'));

        $user = $security->getUser();

        $userName = $user && $user->getFullName() ? $user->getFullName() : 'Gast';
        $userEmail = $user ? $user->getEmail() : 'guess@example.com';
        $userAddress = $user && $user->getAddress() ? $user->getAddress() : 'Unbekannte Adresse';

        $order = new Order();
        $order->setUserName($userName)
            ->setUserEmail($userEmail)
            ->setUserAddress($userAddress)
            ->setTotal($total)
            ->setPaymentMethod('stripe')
            ->setPaymentStatus('Paid')
            ->setIsPending(true)
            ->setOrderReference($orderReference);

        foreach ($items as $item) {
            $cartHistory = new CartHistory();
            $cartHistory->setProductName($item['product']->getName())
                ->setProductPrice($item['product']->getPrice())
                ->setQuantity($item['quantity'])
                ->setSubTotal($item['subtotal'])
                ->setOrderReference($orderReference)
                ->setOrder($order);

            $cartHistoryRepo->save($cartHistory);
        }

        $orderRepository->saveOrder($order);
        $session->remove('cart');

        return $this->render('cart/confirmation.html.twig', [
            'items' => $items,
            'total' => $total,
            'order' => $order
        ]);

    }

}
