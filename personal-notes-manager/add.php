<?php 
include 'db.php';
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // Sanitize and validate input
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Both title and content are required";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO notes (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        
        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Error saving note: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Note</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1>Add Note</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Note title" required 
                       value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" placeholder="Write your note..." required><?= 
                    isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' 
                ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="save-btn">Save</button>
                <a href="index.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>

