<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRequest extends Model
{
    protected $fillable = [
        'employee_id', 'employee_name', 'request_type', 
        'request_date', 'status', 'description'
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
