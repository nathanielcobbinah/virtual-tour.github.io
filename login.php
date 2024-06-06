<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'config.php';

$usernameErr = $passwordErr = "";
$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $isValid = true;

    if (empty($username)) {
        $usernameErr = "Username or Email is required";
        $isValid = false;
    }

    if (empty($password)) {
        $passwordErr = "Password is required";
        $isValid = false;
    }

    if ($isValid) {
        // Check if the user exists in the database
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['gender']  = $user['gender'];

                // Redirect to dashboard or any other page
                header("Location: index.php");
                exit();
            } else {
                $passwordErr = "Invalid password";
            }
        } else {
            $usernameErr = "User not found";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login Form</title>
</head>
<body>
    <div class="login">
        <h2>Sign In</h2>
        <div class="forms">
            <form action="login.php" method="post">
                <div class="wrapper">
                    <label for="username">Username or Email:</label>
                    <input type="text" name="username" placeholder="Enter your username or email..." id="" value="<?php echo htmlspecialchars($username); ?>">
                    <span style="color:red;"><?php echo $usernameErr; ?></span>
                </div>
                <div class="wrapper">
                    <label for="password">Password:</label>
                    <input type="password" name="password" placeholder="Enter your password" id="">
                    <span style="color:red;"><?php echo $passwordErr; ?></span>
                </div>
                <div class="remember">
                    <input type="checkbox" name="remember" id="">
                    <span>Remember me</span>
                </div>
                <button type="submit">Login</button>
                <span>Don't have an account? 
                    <a href="signup.php">Signup</a>
                </span>
            </form>
        </div>
    </div>
</body>
</html>
