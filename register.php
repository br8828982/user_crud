<?php
// Start the session
session_start();

// Check if the user is already logged in, redirect to index.php if true
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
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

    // Validate username length
    if (strlen($username) < 4 || strlen($username) > 20) {
        echo json_encode(["status" => "error", "message" => "Username must be between 4 and 20 characters"]);
        exit();
    }

    // Validate password complexity (example: at least 8 characters, containing letters and numbers)
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long and contain letters and numbers"]);
        exit();
    }

    // Check if the username is already taken
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Username already taken"]);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);

    // Execute the SQL query
    if ($stmt->execute([$username, $hashedPassword])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    exit(); // Terminate script
}
?>
<!-- HTML code including the form and JavaScript code -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title, and stylesheet links -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Include jQuery library if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <main>
        <h1>Register</h1>
        <form id="registerForm" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Register</button>
        </form>
        <p><a href="javascript:void(0);" onclick="window.history.back();">Go Back</a></p>
    </main>

    <script>
    $(document).ready(function() {
        $("#registerForm").submit(function(event) {
            event.preventDefault();
            
            $.ajax({
                type: "POST",
                url: "register.php",
                data: $(this).serialize(),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === "success") {
                        alert("Registration successful!");
                        window.location.href = "login.php";
                    } else {
                        alert(result.message); // Display the error message
                    }
                }
            });
        });
    });
    </script>
</body>
</html>