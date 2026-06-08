<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->bootstrap();
echo 'Default DB: ' . config('database.default') . PHP_EOL;
try {
    $count = \App\Models\ChefProfile::count();
    echo "ChefProfile count: $count" . PHP_EOL;
    $chefs = \App\Models\ChefProfile::with('user')->limit(3)->get();
    foreach ($chefs as $c) {
        echo " - {$c->id}: {$c->business_name} (user:{$c->user_id}, online:{$c->is_online})" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
// Clean up
$__ = new ReflectionClass(\Illuminate\Contracts\Console\Kernel::class);
unlink(__FILE__);
