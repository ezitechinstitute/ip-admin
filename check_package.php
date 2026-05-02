<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$results = DB::table('intern_table')
    ->select('id', 'name', 'intern_type', 'package')
    ->limit(15)
    ->get();

echo "Sample of populated package data:\n";
foreach ($results as $row) {
    echo "ID: {$row->id}, Name: {$row->name}, Type: {$row->intern_type}, Package: {$row->package}\n";
}

echo "\nTotal interns by package:\n";
$counts = DB::table('intern_table')
    ->select('package', DB::raw('COUNT(*) as count'))
    ->whereNotNull('package')
    ->groupBy('package')
    ->get();

foreach ($counts as $count) {
    echo "{$count->package}: {$count->count}\n";
}
