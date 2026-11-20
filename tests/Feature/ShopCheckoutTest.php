<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShopCheckoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $beautyProduct = [
            'id' => 1,
            'title' => 'Essence Mascara Lash Princess',
            'description' => 'Mascara test description.',
            'category' => 'beauty',
            'price' => 10,
            'discountPercentage' => 10,
            'rating' => 4.8,
            'stock' => 15,
            'brand' => 'Essence',
            'sku' => 'BEA-ESS-001',
            'weight' => 4,
            'dimensions' => ['width' => 15, 'height' => 13, 'depth' => 22],
            'warrantyInformation' => '1 week warranty',
            'shippingInformation' => 'Ships in 3-5 business days',
            'availabilityStatus' => 'In Stock',
            'returnPolicy' => '7 days return policy',
            'minimumOrderQuantity' => 1,
            'tags' => ['mascara'],
            'images' => ['https://cdn.dummyjson.com/mascara.webp', 'https://cdn.dummyjson.com/mascara-2.webp'],
            'thumbnail' => 'https://cdn.dummyjson.com/mascara-thumb.webp',
            'reviews' => [['rating' => 5, 'comment' => 'Nice sample.', 'reviewerName' => 'Test Reviewer', 'reviewerEmail' => 'private@example.test']],
        ];

        Http::fake([
            'dummyjson.com/products/category/beauty*' => Http::response(['products' => [$beautyProduct], 'total' => 1], 200),
            'dummyjson.com/products/category/skin-care*' => Http::response(['products' => [], 'total' => 0], 200),
            'dummyjson.com/products/category/fragrances*' => Http::response(['products' => [], 'total' => 0], 200),
        ]);
    }

    public function test_customer_can_browse_categories_open_product_and_add_it_to_the_cart(): void
    {
        $this->get('/')->assertOk()->assertSee('Essence Mascara Lash Princess')->assertDontSee('Retour');
        Http::assertSentCount(3);
        $this->get('/categories/makeup')->assertOk()->assertSee('Essence Mascara Lash Princess')->assertSee('Retour');
        $this->get('/products/essence-mascara-lash-princess-1')->assertOk()->assertSee('BEA-ESS-001')->assertSee('1 week warranty')->assertSee('Test Reviewer');
        $this->post('/cart/1', ['quantity' => 2])->assertRedirect();
        $this->assertSame(2, session('cart.1'));
        $this->get('/cart')->assertOk()->assertSee('179,46 MAD');
    }

    public function test_checkout_uses_api_prices_converted_to_store_currency_and_opens_whatsapp(): void
    {
        config(['store.whatsapp_number' => '212600000000']);
        $response = $this->withSession(['cart' => [1 => 2]])->post('/checkout/whatsapp', [
            'name' => 'Sara Test',
            'phone' => '+212 600 000 000',
            'city' => 'Casablanca',
            'address' => '10 rue des Fleurs',
            'notes' => 'Appeler à l’arrivée',
        ]);
        $response->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/212600000000?text=', $response->headers->get('Location'));
        $message = rawurldecode((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY));
        $this->assertStringContainsString('Essence Mascara Lash Princess', $message);
        $this->assertStringContainsString('179.46 MAD', $message);
        $this->assertStringContainsString('209.46 MAD', $message);
        $this->assertStringContainsString('Casablanca', $message);
    }

    public function test_checkout_does_not_send_an_order_without_a_configured_whatsapp_number(): void
    {
        config(['store.whatsapp_number' => '']);
        $this->withSession(['cart' => [1 => 1]])->post('/checkout/whatsapp', [
            'name' => 'Sara Test',
            'phone' => '+212 600 000 000',
            'city' => 'Casablanca',
            'address' => '10 rue des Fleurs',
        ])->assertRedirect('/cart')->assertSessionHas('error');
    }

    public function test_contact_form_is_validated_and_prepares_a_whatsapp_message(): void
    {
        config(['store.whatsapp_number' => '212600000000']);
        $this->get('/contact')->assertOk()->assertSee('E-mail')->assertSee('Retour');
        $response = $this->post('/contact', [
            'name' => 'Sara Test',
            'email' => 'sara@example.test',
            'subject' => 'Question produit',
            'message' => 'Avez-vous ce produit en stock ?',
        ]);
        $response->assertRedirect();
        $message = rawurldecode((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY));
        $this->assertStringContainsString('sara@example.test', $message);
        $this->assertStringContainsString('Question produit', $message);
    }
}
