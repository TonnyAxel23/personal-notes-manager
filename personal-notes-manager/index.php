<?php 
include 'db.php';

// Initialize variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Notes per page
$offset = ($page - 1) * $limit;

// Prepare the base query
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM notes 
        WHERE title LIKE ? OR content LIKE ? 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?";

// Prepare the search term
$searchTerm = '%' . $search . '%';

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total notes for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS()");
$totalNotes = $totalResult->fetch_row()[0];
$totalPages = ceil($totalNotes / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Personal Notes Manager</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h1>My Notes</h1>
        
        <div class="action-bar">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search notes..." 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Search</button>
            </form>
            <a href="add.php" class="add-note-btn">+ Add New Note</a>
        </div>

        <?php if ($totalNotes == 0): ?>
            <div class="no-notes">
                <p>No notes found. <a href="add.php">Create your first note</a></p>
            </div>
        <?php else: ?>
            <div class="notes-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="note">
                    <h2><?= htmlspecialchars($row['title']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <div class="note-footer">
                        <small><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></small>
                        <div class="note-actions">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this note?')" 
                               class="delete-btn">Delete</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $page-1 ?>">Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" 
                       <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $page+1 ?>">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>