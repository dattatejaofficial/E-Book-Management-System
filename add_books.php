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

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $version = trim($_POST["version"]);
    $ratings = $_POST["ratings"];
    $keywords = trim($_POST["keywords"]);

    // Handle Cover Page Upload
    $cover_dir = "uploads/cover_pages/";
    if (!file_exists($cover_dir)) {
        mkdir($cover_dir, 0777, true);
    }
    $cover_file = $cover_dir . basename($_FILES["cover_page"]["name"]);
    move_uploaded_file($_FILES["cover_page"]["tmp_name"], $cover_file);
    $cover_page = basename($_FILES["cover_page"]["name"]);

    // Handle Book File Upload
    $book_dir = "uploads/books/";
    if (!file_exists($book_dir)) {
        mkdir($book_dir, 0777, true);
    }
    $book_file = $book_dir . basename($_FILES["book_file"]["name"]);
    move_uploaded_file($_FILES["book_file"]["tmp_name"], $book_file);
    $book = basename($_FILES["book_file"]["name"]);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO books (title, author, version, ratings, keywords, cover_page, book_file_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $title, $author, $version, $ratings, $keywords, $cover_page, $book);

    if ($stmt->execute()) {
        $success = "Book added successfully!";
    } else {
        $error = "Error adding book: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add E-Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand">E-Book Management - Admin Panel</a>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</nav>

<!-- Add Book Form -->
<div class="container mt-4">
    <h3 class="text-center">Add a New E-Book 📖</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="add_books.php" method="POST" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Book Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Version</label>
            <input type="text" name="version" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ratings (out of 5)</label>
            <input type="number" name="ratings" class="form-control" step="0.1" min="0" max="5" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Keywords (comma-separated)</label>
            <input type="text" name="keywords" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cover Page (JPG, PNG)</label>
            <input type="file" name="cover_page" class="form-control" accept="image/png, image/jpeg" required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-Book File (PDF, EPUB, TXT)</label>
            <input type="file" name="book_file" class="form-control" accept=".pdf, .epub, .txt" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">Add Book</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
