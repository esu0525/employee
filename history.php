<?php
require_once 'includes/db.php';
$conn = getDBConnection();

// Redirect to inactive by default
header('Location: history-inactive.php');
exit();
?>
