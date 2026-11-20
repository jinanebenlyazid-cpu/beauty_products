<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class DummyJsonService
{
    /** @var array<string, string> */
    private const CATEGORIES = [
        'beauty' => 'makeup',
        'skin-care' => 'skincare',
        'fragrances' => 'fragrances',
    ];

    /** @return list<array<string, mixed>> */
    public function products(): array
    {
        $rate = (float) config('store.usd_to_mad_rate');
        $cacheKey = 'dummyjson.beauty-products.v2.rate-'.$rate;
        $cachedProducts = Cache::get($cacheKey);

        if (is_array($cachedProducts) && $cachedProducts !== []) {
            return $cachedProducts;
        }

        $products = [];

        foreach (self::CATEGORIES as $apiCategory => $storeCategory) {
            try {
                foreach ($this->fetchCategory($apiCategory) as $product) {
                    $normalizedProduct = $this->normalize($product, $rate, $storeCategory);

                    if ($normalizedProduct !== null) {
                        $products[] = $normalizedProduct;
                    }
                }
            } catch (Throwable) {
                // Keep any products returned by the other category endpoints.
            }
        }

        if ($products !== []) {
            Cache::put($cacheKey, $products, now()->addMinutes(10));
        }

        return $products;
    }

    /** @return list<array<string, mixed>> */
    private function fetchCategory(string $category): array
    {
        $products = [];
        $total = 1;
        $skip = 0;

        while ($skip < $total && $skip < 1000) {
            $client = Http::timeout(4);
            $caBundle = (string) config('services.dummyjson.ca_bundle');
            if ($caBundle !== '' && is_file($caBundle)) {
                $client = $client->withOptions(['verify' => $caBundle]);
            }

            $response = $client
                ->get('https://dummyjson.com/products/category/'.$category, ['limit' => 100, 'skip' => $skip])
                ->throw()
                ->json();

            if (! is_array($response) || ! is_array($response['products'] ?? null)) {
                return [];
            }

            $products = array_merge($products, $response['products']);
            $total = (int) ($response['total'] ?? count($products));
            $skip += 100;
        }

        return $products;
    }

    /** @return array<string, mixed>|null */
    private function normalize(array $product, float $rate, string $category): ?array
    {
        if (! isset($product['id'], $product['title'], $product['price'])) {
            return null;
        }

        $images = $product['images'] ?? [];
        if ($images === [] && ! empty($product['thumbnail'])) {
            $images = [$product['thumbnail']];
        }

        $originalPrice = (float) $product['price'];
        $discountPercentage = (float) ($product['discountPercentage'] ?? 0);

        return [
            'id' => (int) $product['id'],
            'name' => (string) $product['title'],
            'slug' => Str::slug($product['title']).'-'.$product['id'],
            'category' => $category,
            'description' => (string) ($product['description'] ?? ''),
            'price' => round($originalPrice * (1 - $discountPercentage / 100) * $rate, 2),
            'old_price' => round($originalPrice * $rate, 2),
            'discount_percentage' => $discountPercentage,
            'images' => array_values(array_filter($images, 'is_string')),
            'available' => (int) ($product['stock'] ?? 0) > 0,
            'stock' => (int) ($product['stock'] ?? 0),
            'brand' => $product['brand'] ?? null,
            'rating' => (float) ($product['rating'] ?? 0),
            'sku' => $product['sku'] ?? null,
            'warranty' => $product['warrantyInformation'] ?? null,
            'shipping' => $product['shippingInformation'] ?? null,
            'return_policy' => $product['returnPolicy'] ?? null,
            'tags' => $product['tags'] ?? [],
            'weight' => $product['weight'] ?? null,
            'dimensions' => $product['dimensions'] ?? [],
            'reviews' => array_map(
                static fn (array $review): array => [
                    'rating' => $review['rating'] ?? null,
                    'comment' => $review['comment'] ?? '',
                    'reviewer' => $review['reviewerName'] ?? '',
                ],
                $product['reviews'] ?? [],
            ),
            'featured' => (float) ($product['rating'] ?? 0) >= 4.5,
            'new_arrival' => false,
            'source_currency' => 'USD',
            'currency' => 'MAD',
        ];
    }
}
