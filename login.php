<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Handle Login Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = trim($_POST["user_id_or_email"]); // Can be User ID or Email
    $password = $_POST["password"];

    // Check if the user exists (by user_id or email)
    $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE user_id = ? OR email = ?");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password_hash"])) {
            // Successful Login
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            header("Location: index.php"); // Redirect to dashboard or homepage
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "User ID or Email not found!";
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
    <title>Login - E-Book Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Login</h2>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="mt-4">
        
        <div class="mb-3">
            <label class="form-label">User ID or Email</label>
            <input type="text" class="form-control" name="user_id_or_email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="text-center mt-3">
        <a href="register.php">Don't have an account? Register</a>
    </div>
</div>

</body>
</html>
//