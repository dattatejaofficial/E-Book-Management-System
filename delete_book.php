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

if (!isset($_GET['book_id']) || empty($_GET['book_id'])) {
    die("Invalid Book ID.");
}

$book_id = $_GET['book_id'];

// Fetch book details
$stmt = $conn->prepare("SELECT cover_page, book_file FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();
$stmt->close();

// Delete book files
$cover_path = "uploads/cover_pages/" . $book["cover_page"];
$book_path = "uploads/books/" . $book["book_file_name"];

if (file_exists($cover_path)) {
    unlink($cover_path);
}
if (file_exists($book_path)) {
    unlink($book_path);
}

// Delete book from database
$delete_stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
$delete_stmt->bind_param("i", $book_id);
$delete_stmt->execute();
$delete_stmt->close();

$conn->close();

// Redirect to dashboard
header("Location: admin_dashboard.php?message=Book+deleted+successfully");
exit();
?>
