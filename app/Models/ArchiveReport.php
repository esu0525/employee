<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveReport extends Model
{
    protected $fillable = [
        'title',
        'period_coverage',
        'regional_office',
        'file_name',
        'format',
        'employee_ids',
        'employee_count',
        'generated_by',
    ];

    protected $casts = [
        'employee_ids' => 'array',
    ];

    /**
     * Get the employees included in this report.
     */
    public function getEmployeeNamesAttribute()
    {
        if (!$this->employee_ids) return [];
        return Employee::whereIn('id', $this->employee_ids)->pluck('name', 'id')->toArray();
    }
}
