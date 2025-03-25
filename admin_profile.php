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
$admin_query = $conn->prepare("SELECT username, email, dob, password FROM admins WHERE admin_id = ?");
$admin_query->bind_param("s", $admin_id);
$admin_query->execute();
$admin_query->bind_result($admin_name, $admin_email, $admin_dob, $hashed_password);
$admin_query->fetch();
$admin_query->close();

$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_dob = $_POST['dob'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Update username, email, and DOB
    $update_query = $conn->prepare("UPDATE admins SET username = ?, email = ?, dob = ? WHERE admin_id = ?");
    $update_query->bind_param("ssss", $new_username, $new_email, $new_dob, $admin_id);
    
    if ($update_query->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile!";
    }
    $update_query->close();

    // Handle password change
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (password_verify($current_password, $hashed_password)) {
            if ($new_password === $confirm_password) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_update_query = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
                $password_update_query->bind_param("ss", $new_hashed_password, $admin_id);
                
                if ($password_update_query->execute()) {
                    $success_message .= " Password updated successfully!";
                } else {
                    $error_message = "Error updating password!";
                }
                $password_update_query->close();
            } else {
                $error_message = "New password and confirm password do not match!";
            }
        } else {
            $error_message = "Current password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-center">Edit Profile</h3>

    <!-- Success & Error Messages -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username:</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($admin_name); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin_email); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth:</label>
            <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($admin_dob); ?>" required>
        </div>

        <h5>Change Password (Optional)</h5>

        <div class="mb-3">
            <label class="form-label">Current Password:</label>
            <input type="password" class="form-control" name="current_password">
        </div>

        <div class="mb-3">
            <label class="form-label">New Password:</label>
            <input type="password" class="form-control" name="new_password">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm New Password:</label>
            <input type="password" class="form-control" name="confirm_password">
        </div>

        <button type="submit" class="btn btn-success">Update Profile</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
