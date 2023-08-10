<?php
// Start the session
session_start();

// Check if the user is already logged in, redirect to index.php if true
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'db.php';

    // Get username and password from the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform validation on $username and $password
    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username and password are required"]);
        exit();
    }

    // Check if the username exists
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() === 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $row['password'];

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username; // Set the username session variable
            echo json_encode(["status" => "success"]);
            exit();
        }
    }

    echo json_encode(["status" => "error", "message" => "Invalid login credentials"]);
    exit();
}
?>
<!-- HTML code including the form and JavaScript code -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title, and stylesheet links -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Include jQuery library if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<main>
    <h1>Login</h1>
    <form id="loginForm" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <p><a href="javascript:void(0);" onclick="window.history.back();">Go Back</a></p>
</main>

<script>
$(document).ready(function() {
    $("#loginForm").submit(function(event) {
        event.preventDefault();
        
        $.ajax({
            type: "POST",
            url: "login.php",
            data: $(this).serialize(),
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === "success") {
                    // Show a success alert and then redirect
                    alert("Login successful!");
                    window.location.href = "index.php"; // Redirect to index.php upon successful login
                } else {
                    // Show an alert for invalid credentials
                    alert(result.message);
                }
            }
        });
    });
});
</script>
</body>
</html>