<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = trim($_POST['admin_id']); // Manually entered
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO admins (admin_id, username, email, password, dob) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $admin_id, $username, $email, $password, $dob);

    if ($stmt->execute()) {
        $message = "Registration successful! <a href='admin_login.php'>Login here</a>";
    } else {
        $message = "Error: " . $stmt->error;
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
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="col-md-6 mx-auto">
        <h3 class="text-center">Admin Registration</h3>
        <p class="text-success"><?php echo $message; ?></p>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Admin ID</label>
                <input type="text" class="form-control" name="admin_id" required placeholder="Choose a unique Admin ID">
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="mt-3 text-center">Already registered? <a href="admin_login.php">Login here</a></p>
    </div>
</div>

</body>
</html>
