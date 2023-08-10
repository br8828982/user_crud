<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title, and stylesheet links -->
</head>
<body>

    <main>
        <h1>Your Profile</h1>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <!-- Display other profile information here -->
        <p><a href="update.php">Update Profile</a></p>
        <p><a href="delete.php">Delete Profile</a></p>
        <p><a href="javascript:void(0);" onclick="window.history.back();">Go Back</a></p>
    </main>

</body>
</html>
