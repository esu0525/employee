<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncEmployeesToDepEd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deped:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all employees to DepEd System via API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = config('services.deped_system.api_url', env('DEPED_SYSTEM_API_URL')) . '/receive-masterlist';
        
        $this->info("Syncing employees to: {$apiUrl}");

        $employees = Employee::where('status', 'active')->get()->map(function ($employee) {
            $fullName = trim(($employee->last_name ?? '') . ', ' . ($employee->first_name ?? '') . ', ' . ($employee->middle_name ?? '') . ' ' . ($employee->suffix ?? ''));
            return [
                'employee_id' => $employee->id,
                'full_name' => $fullName,
                'first_name' => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'last_name' => $employee->last_name,
                'suffix' => $employee->suffix,
                'gender' => $employee->sex,
                'position' => $employee->position,
                'date_hired' => $employee->date_joined ? $employee->date_joined->toDateString() : null,
                'email' => strtolower(preg_replace('/[^A-Za-z0-9.]/', '', ($employee->last_name ?? '') . '.' . ($employee->first_name ?? ''))) . '@deped.gov.ph',
                'contact_number' => $employee->phone,
                'address' => $employee->address,
                'status' => $employee->status,
                'category' => $employee->category,
                'employment_status' => $employee->employment_status,
                'agency' => $employee->agency,
            ];
        })->toArray();

        $this->info("Found " . count($employees) . " employees to sync.");

        $apiKey = 'deped-sync-key-2024';
        $chunks = array_chunk($employees, 50);
        $successCount = 0;
        $errorCount = 0;

        foreach ($chunks as $index => $chunk) {
            $batchNum = $index + 1;
            $this->info("Sending Batch $batchNum...");
            try {
                $response = Http::timeout(300)->withHeaders([
                    'X-API-KEY' => $apiKey
                ])->post($apiUrl, [
                    'employees' => $chunk,
                    'action' => 'upsert'
                ]);

                if ($response->successful()) {
                    $successCount += count($chunk);
                    $this->info("Batch $batchNum success.");
                } else {
                    $errorCount += count($chunk);
                    $this->error("Batch $batchNum Failed: " . $response->body());
                }
            } catch (\Exception $e) {
                $errorCount += count($chunk);
                $this->error("Batch $batchNum Exception: " . $e->getMessage());
            }
        }

        $this->info("Sync completed. Successfully synced: $successCount. Failed: $errorCount.");
    }
}
