<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(private readonly CatalogService $catalog, private readonly CartService $cart) {}

    public function home(): View
    {
        return view('pages.home', ['categories' => $this->catalog->categories(), 'products' => $this->catalog->products()->where('featured', true)->take(4)]);
    }

    public function shop(Request $request): View
    {
        $products = $this->catalog->products();
        if ($request->filled('q')) {
            $query = mb_strtolower($request->string('q')->toString());
            $products = $products->filter(fn (array $product): bool => str_contains(mb_strtolower($product['name'].' '.($product['brand'] ?? '').' '.$product['category']), $query));
        }
        if ($request->filled('category')) {
            $products = $products->where('category', $request->string('category')->toString());
        }

        return view('pages.shop', ['products' => $products, 'categories' => $this->catalog->categories()]);
    }

    public function category(string $category): View
    {
        $categoryData = $this->catalog->categoryBySlug($category);
        abort_if($categoryData === null, 404);

        return view('pages.category', ['category' => $categoryData, 'products' => $this->catalog->products()->where('category', $category)]);
    }

    public function product(string $slug): View
    {
        $product = $this->catalog->productBySlug($slug);
        abort_if($product === null, 404);

        return view('pages.product', ['product' => $product, 'category' => $this->catalog->categoryBySlug($product['category']), 'relatedProducts' => $this->catalog->products()->where('category', $product['category'])->where('id', '!=', $product['id'])->take(4)]);
    }

    public function cart(): View
    {
        return view('pages.cart', ['items' => $this->cart->items(), 'cart' => $this->cart]);
    }

    public function addToCart(Request $request, int $product): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:20']]);
        $item = $this->catalog->productById($product);
        abort_if($item === null || ! $item['available'], 404);
        $cart = session('cart', []);
        $quantity = (int) ($cart[$product] ?? 0) + $validated['quantity'];
        if ($quantity > min(20, (int) ($item['stock'] ?? 20))) {
            return back()->with('error', 'La quantité demandée dépasse le stock disponible.');
        }
        $cart[$product] = $quantity;
        session(['cart' => $cart]);

        return back()->with('success', $item['name'].' ajouté au panier.');
    }

    public function updateCart(Request $request, int $product): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:20']]);
        $item = $this->catalog->productById($product);
        abort_if($item === null, 404);
        if ($validated['quantity'] > (int) ($item['stock'] ?? 20)) {
            return to_route('cart.index')->with('error', 'La quantité demandée dépasse le stock disponible.');
        }
        $cart = session('cart', []);
        if ($validated['quantity'] === 0) {
            unset($cart[$product]);
        } else {
            $cart[$product] = $validated['quantity'];
        }
        session(['cart' => $cart]);

        return to_route('cart.index')->with('success', 'Panier mis à jour.');
    }

    public function removeFromCart(int $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product]);
        session(['cart' => $cart]);

        return to_route('cart.index')->with('success', 'Article retiré du panier.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:120'], 'phone' => ['required', 'string', 'regex:/^[+0-9() .-]{8,24}$/'], 'city' => ['required', 'string', 'max:100'], 'address' => ['required', 'string', 'max:500'], 'notes' => ['nullable', 'string', 'max:500']]);
        $items = $this->cart->items();
        if ($items === []) {
            return to_route('cart.index')->with('error', 'Votre panier est vide.');
        }
        foreach ($items as $item) {
            if ($item['quantity'] > (int) ($item['product']['stock'] ?? 20)) {
                return to_route('cart.index')->with('error', 'Le stock a changé. Veuillez vérifier les quantités de votre panier.');
            }
        }
        $number = preg_replace('/\D+/', '', (string) config('store.whatsapp_number'));
        if ($number === '' || strlen($number) < 8 || strlen($number) > 15) {
            return to_route('cart.index')->with('error', 'Aucun numéro WhatsApp configuré. Ajoutez STORE_WHATSAPP_NUMBER à .env au format international (ex. 2126XXXXXXXX).');
        }
        $currency = config('store.currency');
        $lines = ['NOUVELLE COMMANDE — ÉCLORA BEAUTY', '', 'INFORMATIONS CLIENT', 'Nom : '.$validated['name'], 'Téléphone : '.$validated['phone'], 'Ville : '.$validated['city'], 'Adresse : '.$validated['address'], '', 'DÉTAIL DE LA COMMANDE'];
        foreach ($items as $index => $item) {
            $lines[] = '';
            $lines[] = ($index + 1).'. '.$item['product']['name'];
            $lines[] = 'Quantité : '.$item['quantity'];
            $lines[] = 'Prix unitaire : '.$item['product']['price'].' '.$currency;
            $lines[] = 'Sous-total : '.$item['subtotal'].' '.$currency;
        }
        $lines[] = '';
        $lines[] = 'Sous-total : '.$this->cart->subtotal().' '.$currency;
        $lines[] = 'Livraison : '.$this->cart->delivery().' '.$currency;
        $lines[] = 'TOTAL : '.$this->cart->total().' '.$currency;
        $lines[] = '';
        $lines[] = 'Notes : '.($validated['notes'] ?: 'Aucune');
        $lines[] = '';
        $lines[] = 'Merci de confirmer la disponibilité et les détails de livraison.';

        return redirect()->away('https://wa.me/'.$number.'?text='.rawurlencode(implode("\n", $lines)));
    }

    public function contact(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:24'], 'subject' => ['required', 'string', 'max:160'], 'message' => ['required', 'string', 'max:2000']]);
        $number = preg_replace('/\D+/', '', (string) config('store.whatsapp_number'));
        if ($number === '' || strlen($number) < 8 || strlen($number) > 15) {
            return to_route('contact')->withInput()->with('error', 'Aucun numéro WhatsApp configuré. Ajoutez STORE_WHATSAPP_NUMBER à .env au format international (ex. 2126XXXXXXXX).');
        }
        $message = "MESSAGE DE CONTACT — ÉCLORA BEAUTY\n\nNom : {$validated['name']}\nE-mail : {$validated['email']}\nTéléphone : ".(($validated['phone'] ?? null) ?: 'Non indiqué')."\nSujet : {$validated['subject']}\n\n{$validated['message']}";

        return redirect()->away('https://wa.me/'.$number.'?text='.rawurlencode($message));
    }
}
