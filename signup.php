<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'config.php';

$nameErr = $usernameErr = $emailErr = $passwordErr = $confirmPasswordErr = $genderErr = "";
$name = $username = $email = $password = $confirmPassword = $gender = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);
    $email = trim($_POST['email']);
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';

    $isValid = true;

    if (empty($name)) {
        $nameErr = 'Name is required';
        $isValid = false;
    }

    if (empty($username)) {
        $usernameErr = "Username is required";
        $isValid = false;
    }

    if (empty($email)) {
        $emailErr = "Email is required";
        $isValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $isValid = false;
    }

    if (empty($password)) {
        $passwordErr = "Password is required";
        $isValid = false;
    }

    if (empty($confirmPassword)) {
        $confirmPasswordErr = "Confirm password is required";
        $isValid = false;
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordErr = "Passwords do not match";
        $isValid = false;
    }

    if (empty($gender)) {
        $genderErr = "Gender field is required";
        $isValid = false;
    }

    if ($isValid) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        

        if ($stmt->num_rows > 0) {
            $emailErr = "Email or Username already exists";
        } else {
            $stmt->close();
        
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, gender) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $username, $email, $hashedPassword, $gender);

            if ($stmt->execute()) {
                $name = $email = $username = $password = $confirmPassword  = $gender = "";
                header("Location: index.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Signup Form</title>
</head>
<body>
    <div class="signup login">
        <h2>Sign Up</h2>
        <div class="forms">
            <form action="signup.php" method="post">
                <div class="wrapper">
                    <label for="name">Full Name:</label>
                    <input type="text" name="name" placeholder="Enter your name..." id="name" value="<?php echo htmlspecialchars($name); ?>">
                    <span style="color:red;"><?php echo $nameErr; ?></span>
                </div>
                <div class="wrapper">
                    <label for="username">Username:</label>
                    <input type="text" name="username" placeholder="Enter your username" id="username" value="<?php echo htmlspecialchars($username); ?>">
                    <span style="color:red;"><?php echo $usernameErr; ?></span>
                </div>
                <div class="wrapper">
                    <label for="email">Email:</label>
                    <input type="email" name="email" placeholder="Enter your email" id="email" value="<?php echo htmlspecialchars($email); ?>">
                    <span style="color:red;"><?php echo $emailErr; ?></span>
                </div>
                <div class="wrapper">
                    <label for="password">Password:</label>
                    <input type="password" name="password" placeholder="Enter your password" id="password">
                    <span style="color:red;"><?php echo $passwordErr; ?></span>
                </div>
                <div class="wrapper">
                    <label for="confirm-password">Confirm password:</label>
                    <input type="password" name="confirm-password" placeholder="Confirm your password" id="confirm-password">
                    <span style="color:red;"><?php echo $confirmPasswordErr; ?></span>
                </div>
                <div class="wrapper">
                    <label for="gender">Select Gender</label>
                    <input type="radio" name="gender" value="Male" id="male" <?php if ($gender == 'Male') echo 'checked'; ?>> 
                    <label for="male">Male</label>
                    <br>
                    <input type="radio" name="gender" value="Female" id="female" <?php if ($gender == 'Female') echo 'checked'; ?>> 
                    <label for="female">Female</label>
                    <br>
                    <span><?php echo $genderErr; ?></span>
                </div>

                <button type="submit">Signup</button>
                <span>Already have an account? 
                    <a style="color: blue" href="login.php">Login</a>
                </span>
            </form>
        </div>
    </div>
</body>
</html>
