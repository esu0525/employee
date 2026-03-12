<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

$employees = Employee::all();
foreach ($employees as $e) {
    if (empty($e->last_name) || empty($e->first_name)) {
        $name = trim($e->name);
        if (strpos($name, ',') !== false) {
            $parts = explode(',', $name);
            $e->last_name = trim($parts[0]);
            $e->first_name = trim($parts[1]);
        } else {
            $parts = explode(' ', $name);
            if (count($parts) >= 2) {
                $e->last_name = array_pop($parts);
                $e->first_name = implode(' ', $parts);
            } else {
                $e->last_name = $name;
                $e->first_name = $name;
            }
        }
        $e->save();
        echo "Updated {$e->id}: {$e->last_name}, {$e->first_name}\n";
    }
}
echo "Data fix complete.\n";
