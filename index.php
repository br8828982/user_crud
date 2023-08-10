<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>

    <main>
        <h1>Welcome to Your Web App!</h1>
        <?php
        if (isset($_SESSION['user_id'])) {
            echo "<p>Hello, " . $_SESSION['username'] . "!</p>";
            echo "<p><a href='profile.php'>View Profile</a></p>";
            echo "<p><a href='logout.php'>Logout</a></p>";
        } else {
            echo "<p><a href='register.php'>Register</a> or <a href='login.php'>Login</a> to get started.</p>";
        }
        ?>
    </main>
</body>
</html>
