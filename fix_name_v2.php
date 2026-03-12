<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

$e = Employee::find('EMP011');
if($e) {
    $e->name = 'MARIA C. SANTOS';
    $e->last_name = 'SANTOS';
    $e->first_name = 'MARIA';
    $e->middle_name = 'C';
    $e->save();
    echo "Restored Maria Santos (EMP011)\n";
} else {
    echo "Employee EMP011 not found\n";
}
