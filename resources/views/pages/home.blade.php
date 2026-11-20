@extends('layouts.store')
@section('title', 'Éclora Beauty — La beauté, tout en douceur')
@section('content')
<section class="hero">
    <div class="hero-panel">
        <div class="hero-copy">
            <span class="eyebrow">La nouvelle collection · 2026</span>
            <h1>Votre beauté,<br><em>en toute lumière.</em></h1>
            <p>Des essentiels choisis avec soin pour révéler votre éclat, chaque jour et à votre façon.</p>
            <a class="btn" href="{{ route('shop.index') }}">Découvrir la collection →</a>
        </div>
        <div
            class="hero-image"
            role="img"
            aria-label="Portrait beauté lumineux"
            style="--hero-image: url('{{ config('store.hero_image') }}')"
        ></div>
        @if (config('store.hero_video'))
            <video class="hero-video" autoplay muted loop playsinline aria-label="Vidéo beauté de la page d’accueil">
                <source src="{{ config('store.hero_video') }}" type="video/mp4">
            </video>
        @endif
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <span class="eyebrow">Trouvez votre rituel</span>
        <h2>La beauté, selon vous</h2>
    </div>
    <div class="categories">
        @foreach ($categories as $category)
            <a class="category-tile" href="{{ route('categories.show', $category['slug']) }}">
                <img loading="lazy" src="{{ $category['image'] }}" alt="{{ $category['name'] }}">
                <span class="category-title">{{ $category['name'] }}</span>
            </a>
        @endforeach
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <span class="eyebrow">Vos coups de cœur</span>
        <h2>Les essentiels Éclora</h2>
        <p>Découvrez les produits sélectionnés pour vous.</p>
    </div>
    <div class="product-grid">
        @foreach ($products as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
    <div style="text-align: center; margin-top: 32px">
        <a class="btn btn-outline" href="{{ route('shop.index') }}">Voir toute la boutique</a>
    </div>
</section>
@endsection
