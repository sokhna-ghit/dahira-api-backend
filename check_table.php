<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== STRUCTURE TABLE PAIEMENTS ===\n";

$columns = Schema::getColumnListing('paiements');
echo "Colonnes: " . implode(', ', $columns) . "\n\n";

$tableInfo = DB::select('DESCRIBE paiements');
echo "Détails colonnes:\n";
foreach ($tableInfo as $column) {
    echo "- {$column->Field} ({$column->Type}) - Null: {$column->Null}\n";
}

echo "\n=== EXEMPLE DONNÉES ===\n";
$examples = DB::table('paiements')->limit(3)->get();
foreach ($examples as $example) {
    echo json_encode($example, JSON_PRETTY_PRINT) . "\n";
}
