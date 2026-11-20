@extends('layouts.store')

@section('title', 'Notre histoire — Éclora Beauty')

@section('content')
<style>
    .about-page{--about-ivory:#FAF7F2;--about-blush:#EAD5D3;--about-nude:#D7BBAA;--about-wine:#652E3A;--about-charcoal:#242020;background:var(--about-ivory);overflow:hidden}
    .about-hero{min-height:min(720px,calc(100svh - 118px));position:relative;display:grid;place-items:center;text-align:center;color:#fff;background:linear-gradient(180deg,#24202010 20%,#242020a8 100%),url('/images/banners/hero-beauty.jpg') center 38%/cover}
    .about-hero:after{content:"";position:absolute;inset:16px;border:1px solid #ffffff70;pointer-events:none}
    .about-hero-content{position:relative;z-index:1;max-width:900px;padding:80px 28px}
    .about-kicker{font-size:9px;letter-spacing:.28em;text-transform:uppercase}
    .about-hero h1{font:500 clamp(48px,8vw,104px)/.99 var(--serif);letter-spacing:-.045em;margin:24px 0}
    .about-hero h1 em{color:var(--about-blush);font-weight:500}
    .about-hero p{max-width:480px;margin:0 auto 29px;color:#fff;font-size:13px;line-height:1.9}
    .about-page .btn{transition:transform .25s,background .25s,border-color .25s}
    .about-page .btn:hover{transform:translateY(-2px)}
    .about-light-btn{background:var(--about-ivory)!important;color:var(--about-charcoal)!important;border-color:var(--about-ivory)!important}
    .about-wrap{width:min(1220px,100%);margin:auto;padding:0 34px}
    .about-section{padding:112px 0}
    .about-story{display:grid;grid-template-columns:1.02fr .98fr;align-items:center;gap:clamp(42px,8vw,120px)}
    .about-story-image{position:relative;min-height:580px;overflow:hidden;background:var(--about-blush)}
    .about-story-image img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
    .about-story-image:after{content:"";position:absolute;inset:18px;border:1px solid #fff9}
    .about-copy{max-width:490px}
    .about-copy .about-kicker,.about-heading .about-kicker{color:var(--about-wine)}
    .about-copy h2,.about-heading h2,.about-promise h2,.about-final h2{font:500 clamp(38px,5vw,62px)/1.06 var(--serif);letter-spacing:-.035em;margin:20px 0}
    .about-copy h2 em,.about-heading h2 em,.about-final h2 em{color:var(--about-wine);font-weight:500}
    .about-copy p,.about-heading p,.about-promise p{font-size:13px;line-height:2;color:#756963}
    .about-signature{font:italic 25px var(--serif);color:var(--about-wine);margin-top:24px}
    .about-vision{background:#eee2db}
    .about-heading{text-align:center;max-width:670px;margin:0 auto 54px}
    .about-heading p{max-width:510px;margin:0 auto}
    .about-duo{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .about-pillar{min-height:330px;position:relative;display:flex;align-items:end;overflow:hidden;color:#fff;background-position:center;background-size:cover}
    .about-pillar:before{content:"";position:absolute;inset:0;background:linear-gradient(0deg,#242020d9,transparent 78%);transition:background .3s}
    .about-pillar:hover:before{background:linear-gradient(0deg,#652e3ad9,transparent 90%)}
    .about-pillar.mission{background-image:url('https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=1100&q=85')}
    .about-pillar.vision{background-image:url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1100&q=85')}
    .about-pillar-copy{position:relative;z-index:1;padding:32px 35px;max-width:480px}
    .about-pillar-copy span{font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:var(--about-blush)}
    .about-pillar-copy h3{font:500 34px var(--serif);margin:10px 0}
    .about-pillar-copy p{font-size:12px;line-height:1.8;color:#fff;margin:0}
    .about-values{background:var(--about-ivory)}
    .about-value-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
    .about-value{min-height:240px;padding:27px 23px;background:#fff;border:1px solid #e9dfd8;transition:transform .28s,border-color .28s,box-shadow .28s}
    .about-value:hover{transform:translateY(-5px);border-color:var(--about-nude);box-shadow:0 16px 38px #652e3a0c}
    .about-value svg{width:28px;height:28px;color:var(--about-wine);stroke:currentColor;fill:none;stroke-width:1.3}
    .about-value h3{font:500 22px/1.2 var(--serif);margin:28px 0 10px}
    .about-value p{font-size:11px;line-height:1.8;color:#756963;margin:0}
    .about-categories{padding-top:86px;background:#fff}
    .about-category-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
    .about-category{position:relative;height:340px;overflow:hidden;background:#e5d9d1;color:#fff}
    .about-category img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .65s cubic-bezier(.2,.65,.25,1)}
    .about-category:hover img{transform:scale(1.045)}
    .about-category:after{content:"";position:absolute;inset:40% 0 0;background:linear-gradient(transparent,#242020b8)}
    .about-category span{position:absolute;z-index:1;left:22px;bottom:21px;font:500 28px var(--serif)}
    .about-category span:after{content:" ↗";display:inline-block;font:14px var(--sans);transition:transform .2s}
    .about-category:hover span:after{transform:translate(3px,-3px)}
    .about-promise{min-height:520px;position:relative;display:grid;place-items:center;text-align:center;color:#fff;background:linear-gradient(#24202055,#24202070),url('https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&w=1800&q=85') center 48%/cover}
    .about-promise-content{max-width:680px;padding:70px 28px}
    .about-promise .about-kicker{font-size:9px;letter-spacing:.25em;text-transform:uppercase;color:var(--about-blush)}
    .about-promise h2{font-size:clamp(42px,6vw,70px);margin:19px 0;color:#fff}
    .about-promise p{color:#fff;max-width:490px;margin:auto}
    .about-final{padding:100px 24px;text-align:center;background:var(--about-blush)}
    .about-final h2{max-width:720px;margin:0 auto 27px}
    .about-final-actions{display:flex;justify-content:center;gap:11px;flex-wrap:wrap}
    .about-final .btn-outline{border-color:#652e3a80}
    .about-reveal{opacity:1;transform:none}
    @media(prefers-reduced-motion:no-preference){.about-reveal{opacity:0;transform:translateY(18px);transition:opacity .7s ease,transform .7s ease}.about-reveal.is-visible{opacity:1;transform:translateY(0)}}
    @media(max-width:900px){.about-section{padding:82px 0}.about-story{gap:42px}.about-story-image{min-height:460px}.about-value-grid{grid-template-columns:repeat(2,1fr)}.about-category{height:290px}}
    @media(max-width:620px){.about-hero{min-height:610px;background-position:58% center}.about-hero:after{inset:10px}.about-hero-content{padding:70px 28px}.about-wrap{padding:0 21px}.about-section{padding:68px 0}.about-story{grid-template-columns:1fr;gap:34px}.about-story-image{min-height:390px;order:2}.about-copy{order:1}.about-duo{grid-template-columns:1fr}.about-pillar{min-height:300px}.about-value-grid{gap:9px}.about-value{min-height:220px;padding:21px 17px}.about-value h3{font-size:20px;margin-top:21px}.about-category-grid{grid-template-columns:repeat(2,1fr);gap:9px}.about-category{height:240px}.about-category span{left:14px;bottom:15px;font-size:23px}.about-promise{min-height:440px}.about-final{padding:75px 22px}}
    @media(max-width:380px){.about-value-grid,.about-category-grid{grid-template-columns:1fr}.about-value{min-height:190px}.about-category{height:270px}}
</style>

<div class="about-page">
    <section class="about-hero" aria-labelledby="about-title">
        <div class="about-hero-content about-reveal">
            <span class="about-kicker">L’univers Éclora Beauty</span>
            <h1 id="about-title">Beauty is an expression.<br><em>Make it yours.</em></h1>
            <p>Des essentiels beauté choisis avec intention, pour accompagner chaque version de vous-même.</p>
            <a class="btn about-light-btn" href="{{ route('shop.index') }}">Découvrir la collection <span aria-hidden="true">→</span></a>
        </div>
    </section>

    <section class="about-section" aria-labelledby="story-title">
        <div class="about-wrap about-story">
            <div class="about-story-image about-reveal"><img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=85" alt="Une sélection de produits de beauté aux textures délicates" loading="lazy" decoding="async"></div>
            <div class="about-copy about-reveal">
                <span class="about-kicker">Notre histoire</span>
                <h2 id="story-title">La beauté commence par <em>vous.</em></h2>
                <p>Éclora est née d’une idée simple : la beauté n’a pas besoin de règles pour être précieuse. Elle se trouve dans un geste familier, une couleur inattendue, ou quelques minutes que l’on s’accorde.</p>
                <p>Notre boutique rassemble maquillage, soins et parfums dans un même espace, pour vous aider à trouver les essentiels qui s’accordent à votre rythme et à votre expression.</p>
                <div class="about-signature">Avec douceur, Éclora</div>
            </div>
        </div>
    </section>

    <section class="about-section about-vision" aria-labelledby="mission-title">
        <div class="about-wrap">
            <div class="about-heading about-reveal"><span class="about-kicker">Ce qui nous guide</span><h2 id="mission-title">Une beauté <em>qui vous ressemble.</em></h2><p>Une approche pensée autour de vos envies, de vos gestes et du plaisir de choisir.</p></div>
            <div class="about-duo">
                <article class="about-pillar mission about-reveal"><div class="about-pillar-copy"><span>Notre mission</span><h3>Rendre la beauté personnelle.</h3><p>Vous proposer un espace accueillant où explorer maquillage, soin et parfum à votre manière.</p></div></article>
                <article class="about-pillar vision about-reveal"><div class="about-pillar-copy"><span>Notre vision</span><h3>Plus de place à l’expression.</h3><p>Faire de chaque routine un moment libre, simple et inspirant — sans modèle unique à suivre.</p></div></article>
            </div>
        </div>
    </section>

    <section class="about-section about-values" aria-labelledby="values-title">
        <div class="about-wrap">
            <div class="about-heading about-reveal"><span class="about-kicker">Nos valeurs</span><h2 id="values-title">Le soin dans <em>chaque détail.</em></h2><p>De la sélection à votre expérience en boutique, nous gardons en tête ce qui compte pour vous.</p></div>
            <div class="about-value-grid">
                <article class="about-value about-reveal"><svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="10" r="4"/><path d="M7 26c.5-5 3.6-8 9-8s8.5 3 9 8M5 12l2 2m20-2-2 2"/></svg><h3>La beauté pour tous</h3><p>La beauté est personnelle. Chacun mérite un espace où se sentir libre de l’explorer.</p></article>
                <article class="about-value about-reveal"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 27S5 21 5 12a5 5 0 0 1 9-3l2 2 2-2a5 5 0 0 1 9 3c0 9-11 15-11 15Z"/><path d="m11 16 3 3 7-7"/></svg><h3>Choisis avec soin</h3><p>Une sélection réfléchie de maquillage, soins et parfums, réunis avec intention.</p></article>
                <article class="about-value about-reveal"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M4 7h3l3 15h14l4-11H9M12 27h.01M23 27h.01"/><path d="M13 11h8m-4-4v8"/></svg><h3>Un shopping facile</h3><p>Une boutique claire pour parcourir les essentiels et trouver facilement vos favoris.</p></article>
                <article class="about-value about-reveal"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M6 17a10 10 0 0 1 20 0v7a3 3 0 0 1-3 3h-3v-8h6M6 19h6v8H9a3 3 0 0 1-3-3Z"/><path d="M13 27h5"/></svg><h3>À votre écoute</h3><p>Une question ou besoin d’un conseil ? Notre équipe est là pour vous accompagner.</p></article>
            </div>
        </div>
    </section>

    <section class="about-section about-categories" aria-labelledby="categories-title">
        <div class="about-wrap">
            <div class="about-heading about-reveal"><span class="about-kicker">Explorez nos univers</span><h2 id="categories-title">Trouvez votre <em>rituel.</em></h2><p>Du premier geste au dernier détail, découvrez nos catégories beauté.</p></div>
            @php
                $aboutCategories = [
                    ['name' => 'Maquillage visage', 'slug' => 'makeup', 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80', 'alt' => 'Maquillage et pinceaux pour le teint'],
                    ['name' => 'Maquillage yeux', 'slug' => 'makeup', 'image' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=900&q=80', 'alt' => 'Palette et essentiels pour le regard'],
                    ['name' => 'Maquillage lèvres', 'slug' => 'makeup', 'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80', 'alt' => 'Rouge à lèvres dans une nuance rosée'],
                    ['name' => 'Maquillage ongles', 'slug' => 'makeup', 'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=900&q=80', 'alt' => 'Manucure soignée et vernis'],
                    ['name' => 'Soins', 'slug' => 'skincare', 'image' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80', 'alt' => 'Soin de beauté et hydratation'],
                    ['name' => 'Parfums', 'slug' => 'fragrances', 'image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=900&q=80', 'alt' => 'Flacon de parfum élégant'],
                ];
            @endphp
            <div class="about-category-grid">
                @foreach($aboutCategories as $category)
                    <a class="about-category about-reveal" href="{{ route('categories.show', $category['slug']) }}" aria-label="Découvrir {{ $category['name'] }}">
                        <img src="{{ $category['image'] }}" alt="{{ $category['alt'] }}" loading="lazy" decoding="async">
                        <span>{{ $category['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-promise" aria-labelledby="promise-title">
        <div class="about-promise-content about-reveal"><span class="about-kicker">Notre promesse</span><h2 id="promise-title">Le plaisir de choisir, tout simplement.</h2><p>Nous voulons que chaque visite soit une parenthèse agréable : des découvertes à votre rythme, des essentiels bien présentés et une équipe disponible lorsque vous en avez besoin.</p></div>
    </section>

    <section class="about-final" aria-labelledby="final-title">
        <div class="about-reveal"><span class="about-kicker">Votre prochain essentiel vous attend</span><h2 id="final-title">Discover Your <em>Beauty Favorites.</em></h2><div class="about-final-actions"><a class="btn" href="{{ route('shop.index') }}">Shop now <span aria-hidden="true">→</span></a><a class="btn btn-outline" href="{{ route('contact') }}">Contact us</a></div></div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const revealItems = document.querySelectorAll('.about-reveal');
        if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            revealItems.forEach((item) => item.classList.add('is-visible'));
            return;
        }
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealItems.forEach((item) => revealObserver.observe(item));
    });
</script>
@endsection
