-- Employee Management System Database Schema

CREATE DATABASE IF NOT EXISTS employee_management;
USE employee_management;

-- Employees Table
CREATE TABLE IF NOT EXISTS employees (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_joined DATE NOT NULL,
    status ENUM('active', 'inactive', 'resign', 'retired', 'transfer') DEFAULT 'active',
    status_date DATE NULL,
    transfer_location VARCHAR(100) NULL,
    address VARCHAR(255) NULL,
    date_of_birth DATE NULL,
    emergency_contact VARCHAR(100) NULL,
    emergency_phone VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Employee Documents Table
CREATE TABLE IF NOT EXISTS employee_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Requests Table
CREATE TABLE IF NOT EXISTS requests (
    id VARCHAR(20) PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL,
    employee_name VARCHAR(100) NOT NULL,
    request_type ENUM('leave', 'transfer', 'resignation', 'update') NOT NULL,
    request_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Data
INSERT INTO employees (id, name, position, department, email, phone, date_joined, status, address, date_of_birth, emergency_contact, emergency_phone) VALUES
('EMP001', 'John Smith', 'Senior Developer', 'Engineering', 'john.smith@company.com', '+1 (555) 123-4567', '2020-03-15', 'active', '123 Main Street, San Francisco, CA 94102', '1990-05-12', 'Jane Smith', '+1 (555) 123-9999'),
('EMP002', 'Sarah Johnson', 'HR Manager', 'Human Resources', 'sarah.johnson@company.com', '+1 (555) 234-5678', '2019-07-22', 'active', '456 Oak Avenue, San Francisco, CA 94103', '1988-08-20', 'Michael Johnson', '+1 (555) 234-9999'),
('EMP003', 'Michael Chen', 'Product Designer', 'Design', 'michael.chen@company.com', '+1 (555) 345-6789', '2021-01-10', 'active', '789 Pine Street, San Francisco, CA 94104', '1992-11-03', 'Lisa Chen', '+1 (555) 345-9999'),
('EMP004', 'Emily Davis', 'Marketing Specialist', 'Marketing', 'emily.davis@company.com', '+1 (555) 456-7890', '2018-11-05', 'active', '321 Elm Street, San Francisco, CA 94105', '1991-03-15', 'Robert Davis', '+1 (555) 456-9999'),
('EMP005', 'Robert Wilson', 'Finance Director', 'Finance', 'robert.wilson@company.com', '+1 (555) 567-8901', '2017-04-18', 'active', '654 Maple Drive, San Francisco, CA 94106', '1985-07-25', 'Mary Wilson', '+1 (555) 567-9999'),
('EMP006', 'Jessica Martinez', 'Sales Manager', 'Sales', 'jessica.martinez@company.com', '+1 (555) 678-9012', '2019-09-12', 'inactive', '987 Cedar Lane, San Francisco, CA 94107', '1989-12-08', 'Carlos Martinez', '+1 (555) 678-9999'),
('EMP007', 'David Lee', 'Backend Developer', 'Engineering', 'david.lee@company.com', '+1 (555) 789-0123', '2020-06-01', 'resign', '147 Birch Road, San Francisco, CA 94108', '1993-04-18', 'Susan Lee', '+1 (555) 789-9999'),
('EMP008', 'Amanda Brown', 'Senior Accountant', 'Finance', 'amanda.brown@company.com', '+1 (555) 890-1234', '2015-02-28', 'retired', '258 Willow Court, San Francisco, CA 94109', '1960-09-30', 'Thomas Brown', '+1 (555) 890-9999'),
('EMP009', 'Christopher Taylor', 'Project Manager', 'Operations', 'chris.taylor@company.com', '+1 (555) 901-2345', '2018-08-14', 'transfer', '369 Spruce Avenue, San Francisco, CA 94110', '1987-06-22', 'Rachel Taylor', '+1 (555) 901-9999'),
('EMP010', 'Lisa Anderson', 'UX Researcher', 'Design', 'lisa.anderson@company.com', '+1 (555) 012-3456', '2019-03-20', 'inactive', '741 Redwood Street, San Francisco, CA 94111', '1994-02-14', 'Mark Anderson', '+1 (555) 012-9999');

UPDATE employees SET status_date = '2025-12-20' WHERE id = 'EMP006';
UPDATE employees SET status_date = '2026-01-15' WHERE id = 'EMP007';
UPDATE employees SET status_date = '2026-02-01' WHERE id = 'EMP008';
UPDATE employees SET status_date = '2025-11-30', transfer_location = 'New York Office' WHERE id = 'EMP009';
UPDATE employees SET status_date = '2025-10-15' WHERE id = 'EMP010';

-- Insert Sample Requests
INSERT INTO requests (id, employee_id, employee_name, request_type, request_date, status, description) VALUES
('REQ001', 'EMP001', 'John Smith', 'leave', '2026-02-10', 'pending', 'Annual leave request for March 15-25, 2026'),
('REQ002', 'EMP003', 'Michael Chen', 'update', '2026-02-08', 'approved', 'Update contact information and emergency contact'),
('REQ003', 'EMP005', 'Robert Wilson', 'transfer', '2026-02-12', 'pending', 'Request transfer to London office'),
('REQ004', 'EMP002', 'Sarah Johnson', 'leave', '2026-02-05', 'approved', 'Medical leave for 1 week'),
('REQ005', 'EMP004', 'Emily Davis', 'resignation', '2026-02-11', 'pending', 'Resignation notice - Last working day April 30, 2026'),
('REQ006', 'EMP001', 'John Smith', 'update', '2026-01-28', 'rejected', 'Request for salary advance');
