<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'employee_management');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Close database connection
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Escape string for SQL
function escapeString($conn, $str) {
    return $conn->real_escape_string($str);
}

// Generate unique employee ID
function generateEmployeeId($conn) {
    $query = "SELECT id FROM employees ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = intval(substr($row['id'], 3));
        $newId = 'EMP' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'EMP001';
    }
    
    return $newId;
}

// Generate unique request ID
function generateRequestId($conn) {
    $query = "SELECT id FROM requests ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = intval(substr($row['id'], 3));
        $newId = 'REQ' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'REQ001';
    }
    
    return $newId;
}
?>
