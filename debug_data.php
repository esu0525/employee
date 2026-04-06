<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Employee;

$categories = Employee::all()->pluck('category')->unique()->filter()->values()->toArray();
$statuses = Employee::all()->pluck('employment_status')->unique()->filter()->values()->toArray();

echo "Categories:\n";
print_r($categories);
echo "\nStatuses:\n";
print_r($statuses);
