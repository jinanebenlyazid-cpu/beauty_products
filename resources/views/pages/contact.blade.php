@extends('layouts.store')
@section('title', 'Contact — Éclora Beauty')
@section('content')
<section class="container section"><div class="section-head"><span class="eyebrow">Nous sommes à votre écoute</span><h1>Contactez-nous</h1><p>Envoyez-nous votre question. Après validation, WhatsApp s’ouvrira avec votre message prêt à être envoyé.</p></div>
<form class="contact-form" method="POST" action="{{ route('contact.send') }}">@csrf<label>Nom complet<input required name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120"></label><label>E-mail<input required type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255"></label><label>Téléphone (facultatif)<input name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="24"></label><label>Sujet<input required name="subject" value="{{ old('subject') }}" maxlength="160"></label><label>Votre message<textarea required name="message" maxlength="2000">{{ old('message') }}</textarea></label><button class="btn" type="submit">Préparer le message WhatsApp →</button></form>
</section>
@endsection
