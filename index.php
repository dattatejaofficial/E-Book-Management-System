<?php
session_start();
$is_logged_in = isset($_SESSION["user_id"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Book Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('ebook-banner.jpg') center/cover;
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 20px;
        }
        .hero .content {
            max-width: 600px;
            font-size: 1.8rem;
            font-weight: bold;
            padding: 15px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
        }
        .features {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">📖 E-Book Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item"><a class="nav-link" href="explore_ebooks.php">📚 Explore</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">👤 Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php">⚙️ Settings</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">🚪 Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white me-2" href="login.php">🔐 Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-success text-white" href="register.php">📝 Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero">
    <div class="content">
        Welcome to the <br> E-Book Management System 📚
    </div>
</div>

<!-- About Section -->
<div class="container mt-5">
    <div class="features text-center">
        <h2>📖 About This Software</h2>
        <p>The E-Book Management System is designed to help you explore, manage, and enjoy your favorite e-books effortlessly.</p>

        <h3 class="mt-4">✨ Key Features:</h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">📚 Browse and manage your e-books</li>
            <li class="list-group-item">🔍 Search and filter by categories</li>
            <li class="list-group-item">📖 Personalized reading experience</li>
            <li class="list-group-item">🔄 Sync across multiple devices</li>
            <li class="list-group-item">🔒 Secure login & account management</li>
        </ul>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <p>© 2025 E-Book Management System | All Rights Reserved</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
