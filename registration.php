<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
include("connection/connect.php");
error_reporting(0);
ini_set('display_errors', '0');

// Include PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$message_type = '';


// Initialize variables
$message = "";

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Validate required fields
    if (
        empty($_POST['fName']) || 
        empty($_POST['uName']) || 
        empty($_POST['email']) || 
        empty($_POST['password']) || 
        empty($_POST['cpassword']) // Removed the comma here
    ) {
        $message = "All fields must be filled!";
    } else {
        // Sanitize inputs to prevent SQL injection
        $fName = mysqli_real_escape_string($db, $_POST['fName']);
        $username = mysqli_real_escape_string($db, $_POST['uName']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $password = mysqli_real_escape_string($db, $_POST['password']);
        $cpassword = mysqli_real_escape_string($db, $_POST['cpassword']);
        $role = 'customer'; // Set role to customer by default

        // Check for duplicate usernames and emails
        $check_username = mysqli_query($db, "SELECT uName FROM users WHERE uName = '$username'");
        $check_email = mysqli_query($db, "SELECT email FROM users WHERE email = '$email'");

        if ($password != $cpassword) {
            $message = "Passwords do not match!";
        } elseif (strlen($password) < 6) {
            $message = "Password must be at least 6 characters long!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email address!";
        } elseif (mysqli_num_rows($check_username) > 0) {
            $message = "Username already exists!";
        } elseif (mysqli_num_rows($check_email) > 0) {
            $message = "Email already exists!";
        } else {
            // Hash password for security
            $hashed_password = md5($password); // Use password_hash() in production for stronger hashing

            // Generate a unique token for email verification
            $verification_token = md5(uniqid($username, true)); // Unique token generation

            // Insert user data into the database
            $sql = "INSERT INTO users (uName, fName, email, password, status, role, verification_token) 
                    VALUES ('$username', '$fName', '$email', '$hashed_password', 1, '$role', '$verification_token')";

            if (mysqli_query($db, $sql)) {
                // Send verification email
                sendVerificationEmail($email, $verification_token);

                echo "<script>alert('Registration successful! Please check your email to verify your account.');</script>";
                header("refresh:0.1;url=login.php");
                exit;
            } else {
                $message = "Error: Could not execute query. " . mysqli_error($db);
            }
        }
    }
}

function sendVerificationEmail($email, $token) {
    // PHPMailer setup
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'lms.sorsu@gmail.com'; // Your SMTP email
        $mail->Password = 'ouqo pbob gquk opta'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'Verify Ypur Email Address');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body    = 'Click on the following link to verify your email address: <a href="http://127.0.0.1/online_ordering_system/verify.php?token=' . $token . '">Verify Email</a>';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        html, body {
            display: flex;
            height: 100vh;
            width: 100%;
            position: relative; /* To position the background overlay */
        }

        /* Pseudo-element for transparent background overlay */
        body::after {
        content: "";
        position: fixed; /* Make the background fixed */
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: url('/angelicadollendo_online/imgs/dessertBG.png') no-repeat center center;
        background-size: cover;
        opacity: 0.6; /* Set the transparency of the background image */
        z-index: -1; /* Ensure the overlay is behind content */
        }

        .left-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .right-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 500px;
            background: #fff; /* Remove transparency */
            border-radius: 15px 15px 15px 15px; /* Fix border-radius */
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .container h2 {
            font-size: 30px;
            text-align: center;
            line-height: 100px;
            color: #fff;
            background: linear-gradient(-135deg, #c850c0, #4158d0);
            margin: 0;
            padding: 0;
            border-radius: 15px 15px 0px 0px;
        }

        .container form {
            padding: 10px 30px 30px 30px;
        }

        .container form .form-control,
        .container form .form-select {
            height: 50px;
            font-size: 17px;
            border: 1px solid lightgrey;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .container form .form-control:focus,
        .container form .form-select:focus {
            border-color: #4158d0;
            box-shadow: none;
        }

        .container form label {
            font-size: 17px;
            color: #999999;
        }

        .btn-purple {
            height: 50px;
            font-size: 20px;
            font-weight: 500;
            border: none;
            border-radius: 25px;
            background: linear-gradient(-135deg, #c850c0, #4158d0);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-purple:hover {
            background: linear-gradient(-135deg, #b044b0, #3c50c0);
        }

        .btn-purple:active {
            transform: scale(0.95);
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            color: red;
        }

        .login-link {
            text-align: center;
            margin-top: 0px;
        }

        .login-link a {
            color: #4158d0;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .logo {
            width: 300px; /* Adjust the size as needed */
            margin-left: 200px;
        }

        .logo-text {
            font-size: 40px;
            font-weight: 600;
            color: #27214f;
            margin-top: 10px;
            margin-left: 200px;
        }
    </style>
</head>
<body>

<div class="left-side">
    <img src="/angelicadollendo_online/imgs/logotrans.png" alt="Logo" class="logo">
    <div class="logo-text">Ordering System</div>
</div>

<div class="right-side">
    <div class="container">
        <h2>Register an Account</h2>
        <form method="POST" action="" onsubmit="return validatePasswords();">
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fName" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="uName" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="cpassword" name="cpassword" required>
            </div>
            <!-- Removed the account type dropdown, it's now always "customer" -->
            <button type="submit" name="submit" class="btn btn-purple w-100">Create Account</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<script>
    function validatePasswords() {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('cpassword').value;

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return false;
        }
        return true;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/tether.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/animsition.min.js"></script>
<script src="js/bootstrap-slider.min.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/headroom.js"></script>
<script src="js/foodpicky.min.js"></script>

</body>
</html>