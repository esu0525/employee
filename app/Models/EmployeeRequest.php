<?php

namespace App\Models;

// v1.1
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $employee_id
 * @property string $employee_name
 * @property string $agency
 * @property string $request_type
 * @property Carbon $request_date
 * @property string $status
 * @property string $description
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeRequest findOrFail($id)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeRequest where($column, $operator = null, $value = null, $boolean = 'and')
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereEmployeeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereRequestDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereRequestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmployeeRequest extends Model
{
    protected $table = 'employee_requests';

    protected $fillable = [
        'id', 'employee_id', 'employee_name', 'agency', 'request_type',
        'num_copies', 'purpose', 'request_date', 'status', 'description', 'requirements_file', 'prepared_by'
    ];

    protected $casts = [
        'request_date' => 'date',
        'purpose' => \App\Casts\SafeEncrypt::class,
        'description' => \App\Casts\SafeEncrypt::class,
    ];

    public function setEmployeeNameAttribute($value) { $this->attributes['employee_name'] = $this->formatTitle($value); }
    public function setAgencyAttribute($value) { $this->attributes['agency'] = $this->formatTitle($value); }
    public function setRequestTypeAttribute($value) { $this->attributes['request_type'] = $this->formatTitle($value); }

    protected function formatTitle($value)
    {
        if (empty($value)) return $value;
        return ucwords(mb_strtolower($value));
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
