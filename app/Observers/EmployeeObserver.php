<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    protected function getApiUrl()
    {
        return rtrim(env('DEPED_SYSTEM_API_URL', 'http://localhost:8001/api'), '/') . '/receive-user';
    }

    protected function formatEmployeeData(Employee $employee)
    {
        // Force the standardized email format
        $email = strtolower(preg_replace('/[^A-Za-z0-9.]/', '', ($employee->last_name ?? '') . '.' . ($employee->first_name ?? ''))) . '@deped.gov.ph';

        // Map Gender
        $gender = ($employee->sex == 'Male' || $employee->sex == 'M') ? 'Male' : 'Female';

        // Map general status
        $status = 'Active';
        $rawGenStatus = strtolower($employee->status);
        if ($rawGenStatus !== 'active') {
            $status = 'Inactive';
        }

        return [
            'emp_id'            => $employee->id,
            'first_name'        => $employee->first_name,
            'last_name'         => $employee->last_name,
            'middle_name'       => $employee->middle_name,
            'suffix'            => $employee->suffix,
            'gender'            => $gender,
            'position'          => $employee->position,
            'email'             => $email,
            'contact_number'    => $employee->phone,
            'address'           => $employee->address,
            'status'            => $status,
            'category'          => $employee->category ?: 'National',
            'agency'            => $employee->agency,
        ];
    }

    public function saved(Employee $employee): void
    {
        $action = (strtolower($employee->status) === 'active') ? 'upsert' : 'delete';
        $this->sync($employee, $action);
    }

    public function deleted(Employee $employee): void
    {
        $this->sync($employee, 'delete');
    }

    protected function sync(Employee $employee, string $action)
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => 'deped-sync-key-2024',
                'Accept'    => 'application/json',
            ])->timeout(15)->post($this->getApiUrl(), [
                'data'   => $this->formatEmployeeData($employee),
                'action' => $action
            ]);

            if ($response->failed()) {
                Log::error("DepEd Live Sync Failed [" . $action . "] for Emp ID " . $employee->id . ": " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("DepEd Live Sync Exception [" . $action . "] for Emp ID " . $employee->id . ": " . $e->getMessage());
        }
    }
}
