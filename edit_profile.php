<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$user_id = $_SESSION['user_id'];

// Initialize variables
$name = $gender = $email = "";
$nameErr = $genderErr = $emailErr = "";

// Fetch user data
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $conn->prepare("SELECT name, gender, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $name = $user['name'];
    $gender = $user['gender'];
    $email = $user['email'];
    $stmt->close();
}

// Update user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);

    // Validate input
    if (empty($name)) {
        $nameErr = "Name is required";
    }
    if (empty($gender)) {
        $genderErr = "Gender is required";
    }
    if (empty($email)) {
      $emailErr = "Email is required";
    }

    if (empty($nameErr) && empty($genderErr)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, gender = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $gender, $email, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully";
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating profile: " . $conn->error;
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
    <link rel="stylesheet" href="style.css">
    <title>Edit Profile</title>
</head>
<body>

<header>
    <div class="logo">
        <img src="virtual3.png" alt="">
        VIRTUAL
    </div>
    <div class="username">
        <?php 
        if (isset($_SESSION['username'])) {
            echo '<a href="profile.php">Welcome, ' . htmlspecialchars($_SESSION['username']) . '</a>';
        }
        ?>
    </div>
</header>

<div class="profile">
    <h1>Edit Profile</h1>
    <?php
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green;'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="wrapper">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <span style="color:red;"><?php echo $nameErr; ?></span>
        </div>
        <div class="wrapper">
            <label for="gender">Gender:</label><br>
            <input type="radio" name="gender" value="Male" id="male" <?php if ($gender == 'Male') echo 'checked'; ?>> Male<br>
            <input type="radio" name="gender" value="Female" id="female" <?php if ($gender == 'Female') echo 'checked'; ?>> Female<br>
            <span style="color:red;"><?php echo $genderErr; ?></span>
        </div>
        <div class="wrapper">
            <label for="email">Email:</label><br>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <span style="color:red;"><?php echo $emailErr; ?></span>
        </div>
        <br>
        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>
