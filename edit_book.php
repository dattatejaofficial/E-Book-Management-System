<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ebook_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate book ID
if (!isset($_GET['book_id']) || empty($_GET['book_id'])) {
    die("Invalid Book ID.");
}
$book_id = $_GET['book_id'];

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Book not found.");
}
$book = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $version = $_POST["version"];
    $ratings = $_POST["ratings"];
    $keywords = $_POST["keywords"];

    // File upload directories
    $cover_dir = "uploads/cover_pages/";
    $book_dir = "uploads/books/";

    // Process cover page upload
    if (!empty($_FILES["cover_page"]["name"])) {
        $cover_page = basename($_FILES["cover_page"]["name"]);
        $cover_target = $cover_dir . $cover_page;
        
        // Delete old cover page
        if (!empty($book["cover_page"]) && file_exists($cover_dir . $book["cover_page"])) {
            unlink($cover_dir . $book["cover_page"]);
        }

        move_uploaded_file($_FILES["cover_page"]["tmp_name"], $cover_target);
    } else {
        $cover_page = $book["cover_page"]; // Keep existing if not changed
    }

    // Process book file upload
    if (!empty($_FILES["book_file"]["name"])) {
        $book_file = basename($_FILES["book_file"]["name"]);
        $book_target = $book_dir . $book_file;
        
        // Delete old book file
        if (!empty($book["book_file"]) && file_exists($book_dir . $book["book_file"])) {
            unlink($book_dir . $book["book_file"]);
        }

        move_uploaded_file($_FILES["book_file"]["tmp_name"], $book_target);
    } else {
        $book_file = $book["book_file"]; // Keep existing if not changed
    }

    // Update book details in the database
    $update_stmt = $conn->prepare("UPDATE books SET title=?, author=?, version=?, ratings=?, keywords=?, cover_page=?, book_file=? WHERE book_id=?");
    $update_stmt->bind_param("sssssssi", $title, $author, $version, $ratings, $keywords, $cover_page, $book_file, $book_id);

    if ($update_stmt->execute()) {
        header("Location: admin_dashboard.php?message=Book+updated+successfully");
        exit();
    } else {
        echo "Error updating book.";
    }

    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="text-center">Edit Book</h3>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Book Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo $book['title']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" class="form-control" name="author" value="<?php echo $book['author']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Version</label>
            <input type="text" class="form-control" name="version" value="<?php echo $book['version']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ratings (1-5)</label>
            <input type="number" class="form-control" name="ratings" value="<?php echo $book['ratings']; ?>" min="1" max="5" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Keywords (comma separated)</label>
            <input type="text" class="form-control" name="keywords" value="<?php echo $book['keywords']; ?>" required>
        </div>

        <!-- Cover Page Upload -->
        <div class="mb-3">
            <label class="form-label">Cover Page (optional)</label><br>
            <img src="uploads/cover_pages/<?php echo $book['cover_page']; ?>" width="100"><br>
            <input type="file" class="form-control mt-2" name="cover_page" accept="image/*">
        </div>

        <!-- Book File Upload -->
        <div class="mb-3">
            <label class="form-label">Book File (optional)</label><br>
            <a href="uploads/books/<?php echo $book['book_file_name']; ?>" target="_blank">Current File</a><br>
            <input type="file" class="form-control mt-2" name="book_file" accept=".pdf,.epub,.mobi,.docx">
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Book</button>
    </form>
    
    <div class="text-center mt-3 mb-3">
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
