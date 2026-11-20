@extends('layouts.store')
@section('title', $category['name'].' — Éclora Beauty')
@section('content')<section class="container section"><div class="section-head"><span class="eyebrow">Nos catégories</span><h1>{{ $category['name'] }}</h1><p>{{ $category['description'] }}</p></div><div class="product-grid">@forelse($products as $product) @include('components.product-card', ['product' => $product]) @empty<div class="empty">Aucun produit disponible dans cette catégorie pour le moment.</div>@endforelse</div></section>@endsection
