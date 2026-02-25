<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'position', 'department', 'email', 'phone', 
        'date_joined', 'status', 'status_date', 'transfer_location',
        'address', 'date_of_birth', 'emergency_contact', 'emergency_phone'
    ];

    protected $casts = [
        'date_joined' => 'date',
        'status_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function requests()
    {
        return $this->hasMany(EmployeeRequest::class);
    }
}
