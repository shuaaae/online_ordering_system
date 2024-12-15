<?php
include("../connection/connect.php");
error_reporting(0);

if(isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    
    // Check if fields are empty
    if(!empty($username) && !empty($password) && !empty($email)) {
        // Check if username already exists
        $checkUserQuery = "SELECT * FROM admin WHERE username = ?";
        $stmt = mysqli_prepare($db, $checkUserQuery);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            echo "<script>alert('Username already exists!');</script>";
        } else {
            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new admin into the database
            $insertQuery = "INSERT INTO admin (username, password, email) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($db, $insertQuery);
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $email);
            $insertResult = mysqli_stmt_execute($stmt);
            
            if($insertResult) {
                echo "<script>alert('Account created successfully. You can now login.');</script>";
                header("Location: index.php"); // Redirect to login page after successful signup
            } else {
                echo "<script>alert('Error in creating account. Please try again.');</script>";
            }
        }
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container">
        <div class="info">
            <h1>Create Admin Account</h1>
        </div>
    </div>
    <div class="form">
        <form class="signup-form" action="signup.php" method="post">
            <input type="text" placeholder="Username" name="username" required />
            <input type="password" placeholder="Password" name="password" required />
            <input type="email" placeholder="Email" name="email" required />
            <input type="submit" name="signup" value="Create now" />
        </form>
    </div>
</body>
</html>
