<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private ProductRepository $productRepo;

    public function __construct(SessionInterface $session, ProductRepository $productRepo)
    {
        $this->session = $session;
        $this->productRepo = $productRepo;
    }

    public function add(int $id): void
    {
        $cart = $this->session->get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        $this->session->set('cart', $cart);
    }

    public function getDetailedItems(): array
    {
        $items = $this->session->get('cart', []);
        $detailedItems = [];

        foreach ($items as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            $subtotal = $product->getPrice() * $quantity;

            $detailedItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }

        return $detailedItems;
    }
    public function getTotal(): float
    {
        return array_sum(array_column($this->getDetailedItems(), 'subtotal'));
    }

    public function getCount(): int
    {
        $items = $this->session->get('cart', []);
        return array_sum($items);
    }



}
