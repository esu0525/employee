<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Employee;

$total = Employee::where('status', 'active')->count();
$with_cat = Employee::where('status', 'active')->get()->filter(fn($e) => !empty(trim($e->category)))->count();
$with_stat = Employee::where('status', 'active')->get()->filter(fn($e) => !empty(trim($e->employment_status)))->count();

$cats = Employee::where('status', 'active')->get()->pluck('category')->unique()->toArray();
$stats = Employee::where('status', 'active')->get()->pluck('employment_status')->unique()->toArray();

echo "Total Active: $total\n";
echo "With Category: $with_cat\n";
echo "With Status: $with_stat\n";
echo "Categories Found: " . implode(', ', array_map(fn($v) => "'$v'", $cats)) . "\n";
echo "Statuses Found: " . implode(', ', array_map(fn($v) => "'$v'", $stats)) . "\n";
