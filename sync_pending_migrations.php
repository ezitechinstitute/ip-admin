<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

$migrationFiles = File::files(database_path('migrations'));
$ranMigrations = DB::table('migrations')->pluck('migration')->toArray();

foreach ($migrationFiles as $file) {
    $name = $file->getBasename('.php');
    if (!in_array($name, $ranMigrations)) {
        // This is a pending migration
        $content = File::get($file->getRealPath());
        if (preg_match("/Schema::create\('([^']+)'/", $content, $matches)) {
            $table = $matches[1];
            if (Schema::hasTable($table)) {
                echo "Table $table exists for pending migration $name. Syncing...\n";
                DB::table('migrations')->updateOrInsert(
                    ['migration' => $name],
                    ['batch' => 99]
                );
            }
        }
    }
}

echo "Sync completed.\n";
