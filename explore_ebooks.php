<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";
$sql = "SELECT * FROM books";

if (!empty($search)) {
    $sql .= " WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR keywords LIKE '%$search%'";
}

$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore E-Books</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
        }
        .book-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }
        .book-card:hover {
            transform: scale(1.02);
        }
        .cover {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }
        .rating {
            font-weight: bold;
            color: #ff9800;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .navbar-toggler {
            border: none;
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-success text-white" href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Explore E-Books 📚</h2>

    <!-- Search Form -->
    <form method="GET" action="explore_ebooks.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Title, Author, or Keywords..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <div class="row">
        <?php if (count($books) > 0): ?>
            <?php foreach ($books as $book): ?>
                <div class="col-md-4 mb-4">
                    <a href="read_ebook.php?book_id=<?php echo $book['book_id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="book-card">
                            <img src="<?php echo 'uploads/cover_pages/' . $book['cover_page']; ?>" alt="Cover" class="cover">
                            <h5 class="mt-2"><?php echo $book['title']; ?></h5>
                            <p><strong>Author:</strong> <?php echo $book['author']; ?></p>
                            <p><strong>Version:</strong> <?php echo $book['version']; ?></p>
                            <p class="rating">⭐ <?php echo $book['ratings']; ?>/5</p>
                            <p><strong>Keywords:</strong> <?php echo $book['keywords']; ?></p>
                        </div>
                    </a>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">No books found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
