<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $name
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
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $employee_id
 * @property string $document_name
 * @property string $file_path
 * @property \Illuminate\Support\Carbon $upload_date
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeDocument findOrFail($id)
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereUploadDate($value)
 * @mixin \Eloquent
 */
	class EmployeeDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $employee_id
 * @property string $employee_name
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
	class EmployeeRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

