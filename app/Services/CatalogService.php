<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CatalogService
{
    public function __construct(private readonly DummyJsonService $dummyJson) {}

    /** @return Collection<int, array<string, mixed>> */
    public function products(): Collection
    {
        $apiProducts = $this->dummyJson->products();

        if ($apiProducts !== []) {
            return collect($apiProducts);
        }

        $contents = file_get_contents(storage_path('app/data/products.json'));
        $data = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $contents), true, flags: JSON_THROW_ON_ERROR);

        return collect($data['products'] ?? []);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function categories(): Collection
    {
        $products = $this->products();

        if (collect($products)->contains(fn (array $product): bool => ($product['source_currency'] ?? null) === 'USD')) {
            return collect([
                ['slug' => 'makeup', 'name' => 'Maquillage', 'description' => 'Maquillage et essentiels beauté.', 'image' => $products->firstWhere('category', 'makeup')['images'][0] ?? ''],
                ['slug' => 'skincare', 'name' => 'Soins', 'description' => 'Soins de la peau.', 'image' => $products->firstWhere('category', 'skincare')['images'][0] ?? ''],
                ['slug' => 'fragrances', 'name' => 'Parfums', 'description' => 'Parfums et fragrances.', 'image' => $products->firstWhere('category', 'fragrances')['images'][0] ?? ''],
            ]);
        }

        $contents = file_get_contents(storage_path('app/data/categories.json'));
        $data = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $contents), true, flags: JSON_THROW_ON_ERROR);

        return collect($data['categories'] ?? []);
    }

    /** @return array<string, mixed>|null */
    public function productBySlug(string $slug): ?array
    {
        return $this->products()->firstWhere('slug', $slug);
    }

    /** @return array<string, mixed>|null */
    public function productById(int $id): ?array
    {
        return $this->products()->firstWhere('id', $id);
    }

    /** @return array<string, mixed>|null */
    public function categoryBySlug(string $slug): ?array
    {
        return $this->categories()->firstWhere('slug', $slug);
    }
}
