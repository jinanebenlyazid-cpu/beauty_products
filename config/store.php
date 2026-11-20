<?php

return [
    'hero_image' => env('STORE_HERO_IMAGE', '/images/banners/hero-video.mp4'),
    'hero_video' => env('STORE_HERO_VIDEO', '/images/banners/hero-video.mp4'),
    'whatsapp_number' => env('STORE_WHATSAPP_NUMBER', ''),
    'currency' => env('STORE_CURRENCY', 'MAD'),
    'usd_to_mad_rate' => (float) env('STORE_USD_TO_MAD_RATE', 9.97),
    'delivery_fee' => (int) env('STORE_DELIVERY_FEE', 30),
    'free_delivery_threshold' => (int) env('STORE_FREE_DELIVERY_THRESHOLD', 500),
];
