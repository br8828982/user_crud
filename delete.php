<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $confirmDelete = $_POST['confirm_delete'];

    if ($confirmDelete === "yes") {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);

        session_unset();
        session_destroy();
        
        echo json_encode(["status" => "success"]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Please type 'yes' to confirm profile deletion"]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title, and stylesheet links -->
</head>
<body>

    <main>
        <h1>Delete Profile</h1>
        <p>Are you sure you want to delete your profile?</p>
        <form id="deleteForm" method="POST">
            <label for="confirm_delete">Type 'yes' to confirm:</label>
            <input type="text" id="confirm_delete" name="confirm_delete" required>
            
            <button type="submit">Delete</button>
        </form>
        <p><a href="javascript:void(0);" onclick="window.history.back();">Go Back</a></p>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#deleteForm").submit(function(event) {
            event.preventDefault();

            $.ajax({
                type: "POST",
                url: "delete.php",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        alert("Profile deleted successfully");
                        window.location.href = "index.php";
                    } else {
                        alert(response.message); // Display the error message
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
