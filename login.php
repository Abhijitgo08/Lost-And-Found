<?php
include 'db.php';
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username= $_POST['username'];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // Validate email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format. Please enter a valid email.";
    } else {
        // Check if the user exists in the database
        $check_query = "SELECT * FROM Users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($password, $user['password'])) { 
                $_SESSION['username'] = $user['username'];
                header('Location: home.php',true,303); 
                exit;
            } 
            else{
                $message = "Incorrect password. Please try again.";
                header("Location: login.php");
                exit;
            }
        } else {
            //Register the new user**
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_query = "INSERT INTO Users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header('Location: home.php',true,303); 
                exit;
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css"> 
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="logo.jpeg" alt="Logo">
        </div>
        <div class="title-container">
            <h1 class="title">Amrutvahini College Of Engineering, Sangamner</h1>
        </div>
    </header>

    <div class="login-container">
        <h2>Sign Up or Login</h2>
        
        <!-- Display PHP message -->
        <?php 
            if (isset($message) && $message !== "") {
            echo "<p>" . htmlspecialchars($message) . "</p>";
            unset($message);
        }
        ?>
        
        <form method="post" action="">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>

    <video autoplay muted loop id="bg-video">
        <source src="login bg_video.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>
</body>
</html>  