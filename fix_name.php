<?php
use App\Models\Employee;

$e = Employee::find('EMP011');
if($e) {
    if (trim($e->name) == '') {
        $e->name = 'MARIA C. SANTOS';
        $e->last_name = 'SANTOS';
        $e->first_name = 'MARIA';
        $e->middle_name = 'C';
        $e->save();
        echo "Restored Maria Santos (EMP011)\n";
    } else {
        echo "Employee EMP011 already has a name: " . $e->name . "\n";
    }
} else {
    echo "Employee EMP011 not found\n";
}
