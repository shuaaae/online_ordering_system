<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Check if fields are empty
    if(!empty($username) && !empty($password)) {
        
        // Use prepared statements to prevent SQL injection
        $loginquery = "SELECT * FROM admin WHERE username = ?";
        $stmt = mysqli_prepare($db, $loginquery);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_array($result)) {
            // Verify the password using password_verify() if password is hashed
            if(password_verify($password, $row['password'])) {
                $_SESSION["adm_id"] = $row['adm_id'];
                header("refresh:1;url=dashboard.php");
            } else {
                echo "<script>alert('Invalid Username or Password!');</script>";
            }
        } else {
            echo "<script>alert('Invalid Username or Password!');</script>";
        }
    } else {
        echo "<script>alert('Please enter both username and password.');</script>";
    }
}
?>

                <head>
                    <meta charset="UTF-8">
                    <title>Admin Login</title>

                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

                    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
                    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
                    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

                    <link rel="stylesheet" href="css/login.css">


                </head>

                <body>
    <div class="container">
        <div class="info">
            <h1>Admin Panel</h1>
        </div>
    </div>
    <div class="form">
        <div class="thumbnail"><img src="images/manager.png" /></div>
        <form class="login-form" action="index.php" method="post">
            <input type="text" placeholder="Username" name="username" required />
            <input type="password" placeholder="Password" name="password" required />
            <input type="submit" name="submit" value="Login" />
        </form>
        
        <!-- Signup link -->
        <div class="signup-link">
            <p>Don't have Admin account? <a href="signup.php">Signup here</a></p>
        </div>
    </div>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='js/index.js'></script>
</body>
</html>
