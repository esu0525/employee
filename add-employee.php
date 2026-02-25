<?php
require_once 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // Generate new employee ID
    $employee_id = generateEmployeeId($conn);
    
    // Get form data
    $name = escapeString($conn, trim($_POST['name']));
    $position = escapeString($conn, trim($_POST['position']));
    $department = escapeString($conn, trim($_POST['department']));
    $email = escapeString($conn, trim($_POST['email']));
    $phone = escapeString($conn, trim($_POST['phone']));
    $date_joined = escapeString($conn, trim($_POST['date_joined']));
    $address = isset($_POST['address']) ? escapeString($conn, trim($_POST['address'])) : '';
    $date_of_birth = isset($_POST['date_of_birth']) && !empty($_POST['date_of_birth']) ? escapeString($conn, trim($_POST['date_of_birth'])) : null;
    $emergency_contact = isset($_POST['emergency_contact']) ? escapeString($conn, trim($_POST['emergency_contact'])) : '';
    $emergency_phone = isset($_POST['emergency_phone']) ? escapeString($conn, trim($_POST['emergency_phone'])) : '';
    
    // Insert employee
    $query = "INSERT INTO employees (
        id, name, position, department, email, phone, date_joined, 
        status, address, date_of_birth, emergency_contact, emergency_phone
    ) VALUES (
        '$employee_id', '$name', '$position', '$department', '$email', '$phone', '$date_joined',
        'active', " . ($address ? "'$address'" : "NULL") . ", " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . ", 
        " . ($emergency_contact ? "'$emergency_contact'" : "NULL") . ", " . ($emergency_phone ? "'$emergency_phone'" : "NULL") . "
    )";
    
    if ($conn->query($query)) {
        $_SESSION['success_message'] = "Employee $employee_id added successfully!";
        header('Location: index.php');
    } else {
        $_SESSION['error_message'] = "Error adding employee: " . $conn->error;
        header('Location: index.php');
    }
    
    closeDBConnection($conn);
    exit();
} else {
    header('Location: index.php');
    exit();
}
?>
