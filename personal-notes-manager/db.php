<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'notes_db';

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for full Unicode support
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log the error and show a user-friendly message
    error_log($e->getMessage());
    die("Unable to connect to database. Please try again later.");
}
?>