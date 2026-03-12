<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $box_number
 * @property string $position
 * @property string $department
 * @property string $email
 * @property string $phone
 * @property Carbon $date_joined
 * @property string $status
 * @property Carbon|null $status_date
 * @property string|null $transfer_location
 * @property string|null $address
 * @property Carbon|null $date_of_birth
 * @property string|null $emergency_contact
 * @property string|null $emergency_phone
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Employee find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Employee findOrFail($id, $columns = ['*'])
 * @method static Employee create(array $attributes = [])
 * @method static Employee orderBy($column, $direction = 'asc')
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmployeeDocument> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmployeeRequest> $requests
 * @property-read int|null $requests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDateJoined($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmergencyPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereStatusDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereTransferLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Employee extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'profile_picture', 'name', 'last_name', 'first_name', 'middle_name', 'box_number', 'so_number',
        'position', 'department', 'email', 'phone',
        'date_joined', 'status', 'status_date', 'transfer_location',
        'address', 'date_of_birth', 'sex', 'marital_status', 'religion', 'blood_type', 'nationality', 'emergency_contact', 'emergency_phone'
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
