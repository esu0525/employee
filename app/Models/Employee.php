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
 * @property string $position
 * @property string $agency
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAgency($value)
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
        'id', 'profile_picture', 'name', 'last_name', 'first_name', 'middle_name', 'suffix', 'so_number',
        'position', 'agency', 'category', 'employment_status', 'salary_grade', 'level_of_position', 'email', 'phone',
        'date_joined', 'status', 'status_date', 'transfer_location',
        'address', 'date_of_birth', 'sex', 'civil_status', 'nationality', 'emergency_contact', 'emergency_phone',
        'effective_date', 'school', 'transfer_to', 'so_no', 'status_specify', 'retirement_under'
    ];

    protected $casts = [
        'date_joined' => 'date',
        'status_date' => 'date',
        'date_of_birth' => \App\Casts\SafeEncryptDate::class,
        'effective_date' => 'date',
        'phone' => \App\Casts\SafeEncrypt::class,
        'address' => \App\Casts\SafeEncrypt::class,
        'emergency_phone' => \App\Casts\SafeEncrypt::class,
        'emergency_contact' => \App\Casts\SafeEncrypt::class,
        'nationality' => \App\Casts\SafeEncrypt::class,
        'civil_status' => \App\Casts\SafeEncrypt::class,
        'category' => \App\Casts\SafeEncrypt::class,
        'employment_status' => \App\Casts\SafeEncrypt::class,
        'salary_grade' => \App\Casts\SafeEncrypt::class,
        'level_of_position' => \App\Casts\SafeEncrypt::class,
        'so_number' => \App\Casts\SafeEncrypt::class,
        'so_no' => \App\Casts\SafeEncrypt::class,
        'transfer_to' => \App\Casts\SafeEncrypt::class,
        'retirement_under' => \App\Casts\SafeEncrypt::class,
        'status_specify' => \App\Casts\SafeEncrypt::class,
    ];

    public function setLastNameAttribute($value) { $this->attributes['last_name'] = $this->formatTitle($value); }
    public function setFirstNameAttribute($value) { $this->attributes['first_name'] = $this->formatTitle($value); }
    public function setMiddleNameAttribute($value) { $this->attributes['middle_name'] = $this->formatTitle($value); }
    public function setNameAttribute($value) { $this->attributes['name'] = $this->formatTitle($value); }
    public function setSuffixAttribute($value) { $this->attributes['suffix'] = $this->formatTitle($value); }
    public function setPositionAttribute($value) { $this->attributes['position'] = $value; }
    public function setAgencyAttribute($value) { $this->attributes['agency'] = $value; }
    public function setAddressAttribute($value) { $this->attributes['address'] = $this->formatTitle($value); }
    public function setCivilStatusAttribute($value) { $this->attributes['civil_status'] = $this->formatTitle($value); }
    public function setNationalityAttribute($value) { $this->attributes['nationality'] = $this->formatTitle($value); }
    public function setEmergencyContactAttribute($value) { $this->attributes['emergency_contact'] = $this->formatTitle($value); }
    public function setTransferToAttribute($value) { $this->attributes['transfer_to'] = $this->formatTitle($value); }
    public function setStatusSpecifyAttribute($value) { $this->attributes['status_specify'] = $this->formatTitle($value); }
    public function setRetirementUnderAttribute($value) { $this->attributes['retirement_under'] = $this->formatTitle($value); }
    
    public function setPhoneAttribute($value) { $this->attributes['phone'] = $this->formatPhone($value); }
    public function setEmergencyPhoneAttribute($value) { $this->attributes['emergency_phone'] = $this->formatPhone($value); }

    protected function formatTitle($value)
    {
        if (empty($value)) return $value;
        return ucwords(mb_strtolower($value));
    }

    protected function formatPhone($value)
    {
        if (empty($value)) return $value;
        $clean = preg_replace('/[^0-9]/', '', $value);
        if (strlen($clean) == 11) {
            return substr($clean, 0, 4) . '-' . substr($clean, 4, 3) . '-' . substr($clean, 7);
        }
        return $value;
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function requests()
    {
        return $this->hasMany(EmployeeRequest::class);
    }
}
