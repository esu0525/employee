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
        $lastName  = $employee->last_name;
        $firstName = $employee->first_name;
        $middleName = $employee->middle_name;
        $suffix    = $employee->suffix;

        // Fallback: parse from 'name' field if individual parts are empty
        if (empty($lastName) && empty($firstName) && !empty($employee->name)) {
            $rawName = trim($employee->name);
            if (str_contains($rawName, ',')) {
                $parts = explode(',', $rawName, 2);
                $lastName = trim($parts[0]);
                $rest = trim($parts[1] ?? '');
                $bits = preg_split('/\s+/', $rest);
                $suffixes = ['JR.', 'SR.', 'III', 'IV', 'V', 'II', 'JR', 'SR'];
                $firstBits = [];
                foreach ($bits as $i => $bit) {
                    $upper = strtoupper(trim($bit, '. '));
                    if (in_array($upper, $suffixes)) {
                        $suffix = trim($bit);
                    } elseif (strlen(trim($bit, '.')) === 1 && $i > 0) {
                        $middleName = trim($bit);
                    } else {
                        $firstBits[] = $bit;
                    }
                }
                $firstName = implode(' ', $firstBits);
            } else {
                $parts = preg_split('/\s+/', $rawName);
                if (count($parts) >= 2) {
                    $lastName = array_pop($parts);
                    $firstName = implode(' ', $parts);
                } else {
                    $firstName = $rawName;
                    $lastName = '';
                }
            }
        }

        // Generate standardized email
        $cleanLast = preg_replace('/[^A-Za-z0-9]/', '', $lastName ?? '');
        $cleanFirst = preg_replace('/[^A-Za-z0-9]/', '', $firstName ?? '');
        $email = strtolower($cleanLast . '.' . $cleanFirst) . '@deped.gov.ph';
        if (empty($cleanLast) && empty($cleanFirst)) {
            $email = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $employee->name ?? 'unknown')) . '@deped.gov.ph';
        }

        // Map Gender
        $gender = (strcasecmp($employee->sex, 'Male') === 0 || strcasecmp($employee->sex, 'M') === 0) ? 'Male' : 'Female';

        // Map general status
        $status = (strcasecmp($employee->status, 'active') === 0) ? 'Active' : 'Inactive';

        return [
            'emp_id'            => $employee->id,
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'middle_name'       => $middleName,
            'suffix'            => $suffix,
            'name'              => $employee->name,
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
        // Don't sync if this update came from the external system itself
        if (config('syncing_from_external')) return;

        $action = (strcasecmp($employee->status, 'active') === 0) ? 'upsert' : 'delete';
        $this->sync($employee, $action);
    }

    public function deleted(Employee $employee): void
    {
        if (config('syncing_from_external')) return;
        $this->sync($employee, 'delete');
    }

    protected function sync(Employee $employee, string $action)
    {
        try {
            $apiKey = env('API_SYNC_KEY', 'deped-sync-key-2024');
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept'    => 'application/json',
            ])->timeout(20)->post($this->getApiUrl(), [
                'data'   => $this->formatEmployeeData($employee),
                'action' => $action
            ]);

            if ($response->failed()) {
                Log::error("Automatic Sync Failed [" . $action . "] for Emp ID " . $employee->id . ": " . $response->body());
            } else {
                Log::info("Automatic Sync Success [" . $action . "] for Emp ID " . $employee->id);
            }
        } catch (\Exception $e) {
            Log::error("Automatic Sync Exception [" . $action . "] for Emp ID " . $employee->id . ": " . $e->getMessage());
        }
    }
}
