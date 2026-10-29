<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

$sales = \App\Models\Sale::orderBy('id', 'desc')->take(10)->get();
foreach ($sales as $s) {
    echo "{$s->receipt_id} | desc: {$s->discount_pct} | receta: {$s->has_receta_completa}" . PHP_EOL;
}
