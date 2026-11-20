@extends('layouts.store')
@section('title', $product['name'].' — Éclora Beauty')

@section('content')
<section class="container">
    <div class="page-heading"><div class="crumb"><a href="{{ route('home') }}">Accueil</a> / <a href="{{ route('categories.show', $product['category']) }}">{{ $category['name'] ?? 'Boutique' }}</a> / {{ $product['name'] }}</div></div>
    <div class="product-detail">
        <div class="detail-gallery">
            <img class="detail-image" id="main-product-image" src="{{ $product['images'][0] ?? '' }}" alt="{{ $product['name'] }}">
            @if(count($product['images'] ?? []) > 1)
                <div class="gallery-thumbs" aria-label="Images du produit">
                    @foreach($product['images'] as $image)
                        <button type="button" data-image="{{ $image }}" aria-label="Afficher une autre image"><img loading="lazy" src="{{ $image }}" alt=""></button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="detail-copy">
            <span class="eyebrow">{{ $category['name'] ?? 'Éclora Beauty' }}</span>
            <h1>{{ $product['name'] }}</h1>
            @if(!empty($product['brand']))<p><strong>Marque :</strong> {{ $product['brand'] }}</p>@endif
            <div class="detail-price">{{ number_format($product['price'], 2, ',', ' ') }} {{ config('store.currency') }}
                @if(!empty($product['old_price']) && $product['old_price'] > $product['price'])<del style="font-size:13px;color:var(--muted)">{{ number_format($product['old_price'], 2, ',', ' ') }} {{ config('store.currency') }}</del>@endif
            </div>
            @if(isset($product['rating']))<p>★ {{ number_format($product['rating'], 1, ',', ' ') }} / 5</p>@endif
            <p>{{ $product['description'] }}</p>
            <div class="detail-meta">
                @if(isset($product['sku']))<div><span>Référence</span><br>{{ $product['sku'] }}</div>@endif
                @if(isset($product['stock']))<div><span>Stock API</span><br>{{ $product['stock'] }} unités</div>@endif
                @if(!empty($product['warranty']))<div><span>Garantie</span><br>{{ $product['warranty'] }}</div>@endif
                @if(!empty($product['shipping']))<div><span>Expédition</span><br>{{ $product['shipping'] }}</div>@endif
                @if(!empty($product['return_policy']))<div><span>Retours</span><br>{{ $product['return_policy'] }}</div>@endif
                @if(!empty($product['weight']))<div><span>Poids</span><br>{{ $product['weight'] }}</div>@endif
                @if(!empty($product['dimensions']))<div><span>Dimensions</span><br>{{ implode(' × ', $product['dimensions']) }}</div>@endif
                @if(!empty($product['tags']))<div><span>Mots-clés</span><br>{{ implode(', ', $product['tags']) }}</div>@endif
            </div>
            <p><strong>Disponibilité :</strong> {{ $product['available'] ? 'En stock — à confirmer lors de la commande' : 'Indisponible' }}</p>
            <form method="POST" action="{{ route('cart.add', $product['id']) }}">
                @csrf
                <label for="quantity">Quantité</label>
                <input class="quantity" id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min(20, $product['stock'] ?? 20) }}">
                <button class="btn" type="submit" @disabled(!$product['available'])>Ajouter au panier</button>
            </form>
            <p style="font-size:10px">La commande sera préparée dans WhatsApp depuis votre panier.</p>
        </div>
    </div>
    @if(!empty($product['reviews']))
        <section class="section"><div class="section-head"><h2>Avis disponibles</h2></div><div class="categories">
            @foreach($product['reviews'] as $review)<article class="cart-side"><strong>{{ $review['reviewer'] }}</strong><p>★ {{ $review['rating'] }} / 5</p><p>{{ $review['comment'] }}</p></article>@endforeach
        </div></section>
    @endif
    @if($relatedProducts->isNotEmpty())
        <section class="section"><div class="section-head"><h2>À découvrir aussi</h2></div><div class="product-grid">
            @foreach($relatedProducts as $related) @include('components.product-card', ['product' => $related]) @endforeach
        </div></section>
    @endif
</section>
<script>
document.querySelectorAll('.gallery-thumbs button').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById('main-product-image').src = button.dataset.image;
    });
});
</script>
@endsection
