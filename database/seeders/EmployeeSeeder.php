<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Employee::insert([
            ['id' => 'EMP001', 'name' => 'John Smith', 'position' => 'Senior Developer', 'department' => 'Engineering', 'email' => 'john.smith@company.com', 'phone' => '+1 (555) 123-4567', 'date_joined' => '2020-03-15', 'status' => 'active', 'address' => '123 Main Street, San Francisco, CA 94102', 'date_of_birth' => '1990-05-12', 'emergency_contact' => 'Jane Smith', 'emergency_phone' => '+1 (555) 123-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP002', 'name' => 'Sarah Johnson', 'position' => 'HR Manager', 'department' => 'Human Resources', 'email' => 'sarah.johnson@company.com', 'phone' => '+1 (555) 234-5678', 'date_joined' => '2019-07-22', 'status' => 'active', 'address' => '456 Oak Avenue, San Francisco, CA 94103', 'date_of_birth' => '1988-08-20', 'emergency_contact' => 'Michael Johnson', 'emergency_phone' => '+1 (555) 234-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP003', 'name' => 'Michael Chen', 'position' => 'Product Designer', 'department' => 'Design', 'email' => 'michael.chen@company.com', 'phone' => '+1 (555) 345-6789', 'date_joined' => '2021-01-10', 'status' => 'active', 'address' => '789 Pine Street, San Francisco, CA 94104', 'date_of_birth' => '1992-11-03', 'emergency_contact' => 'Lisa Chen', 'emergency_phone' => '+1 (555) 345-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP004', 'name' => 'Emily Davis', 'position' => 'Marketing Specialist', 'department' => 'Marketing', 'email' => 'emily.davis@company.com', 'phone' => '+1 (555) 456-7890', 'date_joined' => '2018-11-05', 'status' => 'active', 'address' => '321 Elm Street, San Francisco, CA 94105', 'date_of_birth' => '1991-03-15', 'emergency_contact' => 'Robert Davis', 'emergency_phone' => '+1 (555) 456-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP005', 'name' => 'Robert Wilson', 'position' => 'Finance Director', 'department' => 'Finance', 'email' => 'robert.wilson@company.com', 'phone' => '+1 (555) 567-8901', 'date_joined' => '2017-04-18', 'status' => 'active', 'address' => '654 Maple Drive, San Francisco, CA 94106', 'date_of_birth' => '1985-07-25', 'emergency_contact' => 'Mary Wilson', 'emergency_phone' => '+1 (555) 567-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP006', 'name' => 'Jessica Martinez', 'position' => 'Sales Manager', 'department' => 'Sales', 'email' => 'jessica.martinez@company.com', 'phone' => '+1 (555) 678-9012', 'date_joined' => '2019-09-12', 'status' => 'inactive', 'address' => '987 Cedar Lane, San Francisco, CA 94107', 'date_of_birth' => '1989-12-08', 'emergency_contact' => 'Carlos Martinez', 'emergency_phone' => '+1 (555) 678-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP007', 'name' => 'David Lee', 'position' => 'Backend Developer', 'department' => 'Engineering', 'email' => 'david.lee@company.com', 'phone' => '+1 (555) 789-0123', 'date_joined' => '2020-06-01', 'status' => 'resign', 'address' => '147 Birch Road, San Francisco, CA 94108', 'date_of_birth' => '1993-04-18', 'emergency_contact' => 'Susan Lee', 'emergency_phone' => '+1 (555) 789-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP008', 'name' => 'Amanda Brown', 'position' => 'Senior Accountant', 'department' => 'Finance', 'email' => 'amanda.brown@company.com', 'phone' => '+1 (555) 890-1234', 'date_joined' => '2015-02-28', 'status' => 'retired', 'address' => '258 Willow Court, San Francisco, CA 94109', 'date_of_birth' => '1960-09-30', 'emergency_contact' => 'Thomas Brown', 'emergency_phone' => '+1 (555) 890-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP009', 'name' => 'Christopher Taylor', 'position' => 'Project Manager', 'department' => 'Operations', 'email' => 'chris.taylor@company.com', 'phone' => '+1 (555) 901-2345', 'date_joined' => '2018-08-14', 'status' => 'transfer', 'address' => '369 Spruce Avenue, San Francisco, CA 94110', 'date_of_birth' => '1987-06-22', 'emergency_contact' => 'Rachel Taylor', 'emergency_phone' => '+1 (555) 901-9999', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'EMP010', 'name' => 'Lisa Anderson', 'position' => 'UX Researcher', 'department' => 'Design', 'email' => 'lisa.anderson@company.com', 'phone' => '+1 (555) 012-3456', 'date_joined' => '2019-03-20', 'status' => 'inactive', 'address' => '741 Redwood Street, San Francisco, CA 94111', 'date_of_birth' => '1994-02-14', 'emergency_contact' => 'Mark Anderson', 'emergency_phone' => '+1 (555) 012-9999', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\EmployeeRequest::insert([
            ['employee_id' => 'EMP001', 'employee_name' => 'John Smith', 'request_type' => 'leave', 'request_date' => '2026-02-10', 'status' => 'pending', 'description' => 'Annual leave request for March 15-25, 2026', 'created_at' => now(), 'updated_at' => now()],
            ['employee_id' => 'EMP003', 'employee_name' => 'Michael Chen', 'request_type' => 'update', 'request_date' => '2026-02-08', 'status' => 'approved', 'description' => 'Update contact information and emergency contact', 'created_at' => now(), 'updated_at' => now()],
            ['employee_id' => 'EMP005', 'employee_name' => 'Robert Wilson', 'request_type' => 'transfer', 'request_date' => '2026-02-12', 'status' => 'pending', 'description' => 'Request transfer to London office', 'created_at' => now(), 'updated_at' => now()],
            ['employee_id' => 'EMP002', 'employee_name' => 'Sarah Johnson', 'request_type' => 'leave', 'request_date' => '2026-02-05', 'status' => 'approved', 'description' => 'Medical leave for 1 week', 'created_at' => now(), 'updated_at' => now()],
            ['employee_id' => 'EMP004', 'employee_name' => 'Emily Davis', 'request_type' => 'resignation', 'request_date' => '2026-02-11', 'status' => 'pending', 'description' => 'Resignation notice - Last working day April 30, 2026', 'created_at' => now(), 'updated_at' => now()],
            ['employee_id' => 'EMP001', 'employee_name' => 'John Smith', 'request_type' => 'update', 'request_date' => '2026-01-28', 'status' => 'rejected', 'description' => 'Request for salary advance', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
