<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = "";
$error_message = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = strtolower(trim($_POST["user_id"])); // Convert to lowercase (like Instagram)
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $dob = $_POST["dob"];
    $subscription_plan = $_POST["subscription"];
    $profile_image = NULL;

    // Validate User ID (Only letters, numbers, underscores, and dots)
    if (!preg_match('/^[a-zA-Z0-9_.]+$/', $user_id)) {
        $error_message = "Invalid User ID! Only letters, numbers, underscores (_), and dots (.) are allowed.";
    } else {
        // Check if User ID is already taken
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "User ID already taken! Please choose another.";
        } else {
            // Handle Profile Image Upload
            if (!empty($_FILES["profile_image"]["name"])) {
                $target_dir = "uploads/profiles/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
                move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
                $profile_image = $target_file;
            }

            // Insert Data into MySQL
            $stmt = $conn->prepare("INSERT INTO users (user_id, username, email, password_hash, date_of_birth, subscription_plan, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $user_id, $username, $email, $password, $dob, $subscription_plan, $profile_image);

            if ($stmt->execute()) {
                // Set session variables for login
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $username;

                // Redirect to Home Page (index.php) after successful registration
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Book Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .plan {
            padding: 10px 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }
        .plan:hover {
            transform: scale(1.05);
        }
        .plan.silver:hover {
            background-color: silver;
            color: white;
        }
        .plan.gold:hover {
            background-color: gold;
            color: black;
        }
        .plan.platinum:hover {
            background-color: #e5e4e2;
            color: black;
        }
        #plan-info {
            font-size: 14px;
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Register</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
        
        <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="text" class="form-control" name="user_id" required pattern="[a-zA-Z0-9_.]+" title="Only letters, numbers, underscores (_) and dots (.) allowed" placeholder="e.g., dattateja">
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required placeholder="e.g., Datta Teja">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required placeholder="e.g., dattateja@gmail.com">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control" name="dob" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Image</label>
            <input type="file" class="form-control" name="profile_image" accept="image/*">
        </div>

        <div class="mb-3">
            <label class="form-label">Subscription Plan</label>
            <div class="d-flex justify-content-around">
                <div class="plan silver" data-features="Basic access to e-books">
                    <input type="radio" name="subscription" value="Silver" required> Silver
                </div>
                <div class="plan gold" data-features="Silver + Priority Support">
                    <input type="radio" name="subscription" value="Gold"> Gold
                </div>
                <div class="plan platinum" data-features="Gold + Unlimited Downloads">
                    <input type="radio" name="subscription" value="Platinum"> Platinum
                </div>
            </div>
            <div id="plan-info" class="text-center mt-2"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
</div>

<script>
    document.querySelectorAll('.plan').forEach(plan => {
        plan.addEventListener('mouseover', function() {
            document.getElementById('plan-info').innerText = this.getAttribute('data-features');
        });
        plan.addEventListener('mouseout', function() {
            document.getElementById('plan-info').innerText = "";
        });
    });
</script>

</body>
</html>
