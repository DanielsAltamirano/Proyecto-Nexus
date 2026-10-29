<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo 'Monday: ' . \Illuminate\Support\Facades\Cache::get('promo_monday_generic_discount', 25) . PHP_EOL;
echo 'Receta: ' . \Illuminate\Support\Facades\Cache::get('promo_receta_completa_discount', 20) . PHP_EOL;
