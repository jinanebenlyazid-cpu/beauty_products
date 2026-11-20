<?php

namespace App\Services;

class CartService
{
    public function __construct(private readonly CatalogService $catalog) {}

    /** @return array<int, array{product: array<string, mixed>, quantity: int, subtotal: int|float}> */
    public function items(): array
    {
        $items = [];
        foreach (session('cart', []) as $id => $quantity) {
            $product = $this->catalog->productById((int) $id);
            if ($product !== null && $product['available'] && $quantity > 0) {
                $items[] = ['product' => $product, 'quantity' => (int) $quantity, 'subtotal' => $product['price'] * (int) $quantity];
            }
        }

        return $items;
    }

    public function quantity(): int
    {
        return array_sum(array_column($this->items(), 'quantity'));
    }

    public function subtotal(): int|float
    {
        return array_sum(array_column($this->items(), 'subtotal'));
    }

    public function delivery(): int
    {
        return $this->subtotal() >= (int) config('store.free_delivery_threshold') ? 0 : (int) config('store.delivery_fee');
    }

    public function total(): int|float
    {
        return $this->subtotal() + $this->delivery();
    }
}
