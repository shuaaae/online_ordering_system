<?php
session_start();
include("connection/connect.php");

// Check if token is passed in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $query = "SELECT * FROM users WHERE verification_token = '$token'";
    $result = mysqli_query($db, $query);
    $user = mysqli_fetch_array($result);

    if ($user) {
        // Verify email and clear the token
        $update_query = "UPDATE users SET email_verified = 1, verification_token = NULL WHERE verification_token = '$token'";
        if (mysqli_query($db, $update_query)) {
            echo "Email verified successfully! You can now log in.";
            header("refresh:2;url=login.php"); // Redirect to login page after 2 seconds
        } else {
            echo "Error verifying email.";
        }
    } else {
        echo "Invalid verification token.";
    }
} else {
    echo "No token provided.";
}
?>
