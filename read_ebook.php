<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['book_id'])) {
    die("Invalid book ID.");
}

$book_id = $_GET['book_id'];
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("s", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();
$file_path = 'uploads/books/' . $book['book_file_name']; // Assuming book files are stored in 'uploads' folder
$file_extension = pathinfo($file_path, PATHINFO_EXTENSION);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read E-Book: <?php echo htmlspecialchars($book['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            margin-top: 20px;
        }
        .iframe-container {
            width: 100%;
            height: 600px;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">E-Book Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_books.php">My Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center"><?php echo htmlspecialchars($book['title']); ?></h2>
    <p class="text-center"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>

    <!-- Display PDF using iframe -->
    <?php if ($file_extension === 'pdf'): ?>
        <div class="iframe-container">
            <iframe src="<?php echo $file_path; ?>" width="100%" height="600px"></iframe>
        </div>

    <!-- Display Text-Based E-Books -->
    <?php elseif (in_array($file_extension, ['txt', 'md', 'html'])): ?>
        <div class="card p-3">
            <pre style="white-space: pre-wrap;"><?php echo file_get_contents($file_path); ?></pre>
        </div>

    <!-- Display EPUB -->
    <?php elseif ($file_extension === 'epub'): ?>
        <iframe class="iframe-container" src="https://epubjs-reader.herokuapp.com/?url=<?php echo urlencode($file_path); ?>"></iframe>

    <!-- Unsupported file type -->
    <?php else: ?>
        <p class="alert alert-warning">This book format is not supported for direct reading.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
