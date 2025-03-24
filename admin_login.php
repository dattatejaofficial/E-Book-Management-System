<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values
    $login_input = trim($_POST['login_input']);
    $password = trim($_POST['password']);

    // Check if the input is an admin_id or email by checking if it's an email format
    if (filter_var($login_input, FILTER_VALIDATE_EMAIL)) {
        // If the input is an email, query based on email
        $stmt = $conn->prepare("SELECT admin_id, password FROM admins WHERE email = ?");
    } else {
        // If it's not an email, assume it's an admin_id
        $stmt = $conn->prepare("SELECT admin_id, password FROM admins WHERE admin_id = ?");
    }

    // Bind the parameter and execute the query
    $stmt->bind_param("s", $login_input);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($admin_id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['admin_id'] = $admin_id;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "Invalid Admin ID or Password!";
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="col-md-6 mx-auto">
        <h3 class="text-center">Admin Login</h3>
        <p class="text-danger"><?php echo $message; ?></p>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Admin ID or Email</label>
                <input type="text" class="form-control" name="login_input" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">Not registered? <a href="admin_register.php">Register here</a></p>
    </div>
</div>

</body>
</html>
