<?php
// Start the session
session_start();

// Check if the user is logged in, redirect to index.php if not
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Handle profile update form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get updated values from the form data
    $newUsername = $_POST['newUsername'];
    $newPassword = $_POST['newPassword'];

    // Perform validation on updated values
    if (empty($newUsername) || empty($newPassword)) {
        $response = ["status" => "error", "message" => "New username and password are required"];
        echo json_encode($response);
        exit();
    }

    // Validate new username length
    if (strlen($newUsername) < 4 || strlen($newUsername) > 20) {
        $response = ["status" => "error", "message" => "New username must be between 4 and 20 characters"];
        echo json_encode($response);
        exit();
    }

    // Validate new password complexity
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $newPassword)) {
        $response = ["status" => "error", "message" => "New password must be at least 8 characters long and contain letters and numbers"];
        echo json_encode($response);
        exit();
    }

    // Check if the user is using their old username
    if ($newUsername === $_SESSION['username']) {
        $response = ["status" => "error", "message" => "You can't use your old username"];
        echo json_encode($response);
        exit();
    }

    // Check if the username is already taken
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newUsername]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Username already taken"]);
        exit();
    }

    // Check if the user is using their old password
    $passwordCheckSql = "SELECT password FROM users WHERE id = ?";
    $stmt = $pdo->prepare($passwordCheckSql);
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldHashedPassword = $row['password'];

    if (password_verify($newPassword, $oldHashedPassword)) {
        $response = ["status" => "error", "message" => "You can't use your old password"];
        echo json_encode($response);
        exit();
    }

    // Update profile data in the database
    $updateSql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
    $stmt = $pdo->prepare($updateSql);

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    if ($stmt->execute([$newUsername, $hashedPassword, $user_id])) {
        $_SESSION['username'] = $newUsername; // Update session with the new username
        $response = ["status" => "success", "message" => "Profile updated successfully"];
    } else {
        $response = ["status" => "error", "message" => "Profile update failed"];
    }

    echo json_encode($response);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Include jQuery library if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<main>
    <h1>Update Profile</h1>
    <form id="updateForm" method="POST">
        <label for="newUsername">New Username:</label>
        <input type="text" id="newUsername" name="newUsername" value="<?php echo $_SESSION['username']; ?>" required>
        <br>
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required>
        <br>
        <button type="submit">Update Profile</button>
    </form>
    <p><a href="javascript:void(0);" onclick="window.history.back();">Go Back</a></p>
</main>

<script>
$(document).ready(function() {
    $("#updateForm").submit(function(event) {
        event.preventDefault();
        
        $.ajax({
            type: "POST",
            url: "update.php",
            data: $(this).serialize(),
            success: function(response) {
                var result = JSON.parse(response);
                alert(result.message);
            }
        });
    });
});
</script>
</body>
</html>
