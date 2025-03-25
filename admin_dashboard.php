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

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$admin_query = $conn->prepare("SELECT username FROM admins WHERE admin_id = ?");
$admin_query->bind_param("s", $admin_id);
$admin_query->execute();
$admin_query->bind_result($admin_name);
$admin_query->fetch();
$admin_query->close();

// Fetch all books
$books_query = "SELECT * FROM books ORDER BY book_id DESC";
$books_result = $conn->query($books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function showDeleteModal(book_id) {
            document.getElementById("confirmDeleteBtn").setAttribute("data-book-id", book_id);
            var myModal = new bootstrap.Modal(document.getElementById("deleteModal"));
            myModal.show();
        }

        function confirmDelete() {
            var book_id = document.getElementById("confirmDeleteBtn").getAttribute("data-book-id");
            window.location.href = "delete_book.php?book_id=" + book_id;
        }
    </script>
    <style>
        .cover-img {
            width: 60px;
            height: 80px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand">E-Book Management - Admin Panel</a>
        <span class="text-white">Welcome, <?php echo $admin_name; ?>!</span>
        <a href="admin_logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>

<!-- Dashboard Content -->
<div class="container mt-4">
    <h3 class="text-center">Admin Dashboard</h3>

    <!-- Add E-Book Button -->
    <div class="text-end mb-3">
        <a href="add_books.php" class="btn btn-primary">Add New E-Book</a>
    </div>

    <!-- Books Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Cover Page</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Version</th>
                    <th>Ratings</th>
                    <th>Keywords</th>
                    <th>Actions</th> <!-- Added Action Column -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($books_result->num_rows > 0) {
                    while ($row = $books_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='uploads/cover_pages/" . $row['cover_page'] . "' class='cover-img'></td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['version']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ratings']) . "/5</td>";
                        echo "<td>" . htmlspecialchars($row['keywords']) . "</td>";
                        echo "<td>
                                <a href='edit_book.php?book_id=" . $row['book_id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                <button onclick='showDeleteModal(" . $row['book_id'] . ")' class='btn btn-danger btn-sm mt-3'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No books available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this book? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
