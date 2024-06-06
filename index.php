<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Store the name in session if not already set
if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = $user['name'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Welcome to the Virtual World</title>
    <script src="index.js"></script>
</head>
<body>
<?php include 'header.php'; ?>

<div class="hero">
    <div class="herotitle">
        <h1>WELCOME <?php echo htmlspecialchars($_SESSION['name']); ?> TO THE DIGITAL WORLD</h1>
        <em>Embark on a journey where reality converges with the limitless possibilities of the digital realm.</em>
        <button>START EXPLORING</button>
    </div>
</div>

</body>
</html>
