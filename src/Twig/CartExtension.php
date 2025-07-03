<?php

namespace App\Twig;

use App\Service\CartService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class CartExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private CartService $cartService) {}

    public function getGlobals(): array
    {
        return [
            'count' => $this->cartService->getCount(),
            'tt' => $this->cartService->getTotal(),
            'items' => $this->cartService->getDetailedItems()
        ];
    }
}
