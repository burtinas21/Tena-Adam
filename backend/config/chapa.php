<?php

return [
    'public_key' => env('CHAPA_PUBLIC_KEY'),
    'secret_key' => env('CHAPA_SECRET_KEY'),
    'base_url' => env(
        'CHAPA_BASE_URL',
        'https://api.chapa.co/v1'
    ),
];