<?php 
include 'db.php';
session_start();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die("Invalid request method");
}

// Validate note ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid note ID");
}

$id = (int)$_GET['id'];

// Verify the note exists first
$stmt = $conn->prepare("SELECT id FROM notes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Note not found");
}

// Delete the note
$stmt = $conn->prepare("DELETE FROM notes WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    die("Error deleting note: " . $conn->error);
}
?>