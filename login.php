<?php
include("connection/connect.php");
session_start();

// Initialize $username
$username = '';

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($db, $_POST['uName']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Correct column name uName instead of username
        $loginquery = "SELECT * FROM users WHERE uName='$username'";

        // Debug: Output the query to check the SQL statement
        // echo "Query: " . $loginquery . "<br>";

        // Executing the query
        $result = mysqli_query($db, $loginquery);

        // Check if the query is executed
        if (!$result) {
            die('Query Error: ' . mysqli_error($db)); // Check if there is any error in the query
        }

        $row = mysqli_fetch_array($result);

        // Debug: Check if any result was returned
        // echo "Row: " . print_r($row, true) . "<br>";

        if (is_array($row)) {
            // Check email verification status
            if ($row['email_verified'] == 0) {
                echo "<script>alert('Email not verified. Please verify your email to login.');</script>";
            } else {
                // Verify the password using password_verify()
                if (password_verify($password, $row['password'])) {
                    $_SESSION["user_id"] = $row['u_id'];
                    header("Location: index.php"); // Redirect to the home page
                    exit;
                } else {
                    echo "<script>alert('Invalid Username or Password!');</script>";
                }
            }
        } else {
            echo "<script>alert('Invalid Username or Password!');</script>";
        }
    } else {
        echo "<script>alert('Please fill in both fields.');</script>"; // Ensure both fields are provided
    }
}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Login Form</title>
      <style>
         @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
         * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
           font-family: 'Poppins', sans-serif;
         }
         html, body {
           display: grid;
           height: 100%;
           width: 100%;
           place-items: center;
           background: url('/online_ordering_system/images/dessertBG.png') no-repeat center center;
           background-size: cover;
         }
         ::selection {
           background: #4158d0;
           color: #fff;
         }
         .wrapper {
           width: 380px;
           background: #fff;
           border-radius: 15px;
           box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
         }
         .wrapper .title {
           font-size: 35px;
           font-weight: 600;
           text-align: center;
           line-height: 100px;
           color: #fff;
           user-select: none;
           border-radius: 15px 15px 0 0;
           background: linear-gradient(-135deg, #c850c0, #4158d0);
         }
         .wrapper form {
           padding: 10px 30px 50px 30px;
         }
         .wrapper form .field {
           height: 50px;
           width: 100%;
           margin-top: 20px;
           position: relative;
         }
         .wrapper form .field input {
           height: 100%;
           width: 100%;
           outline: none;
           font-size: 17px;
           padding-left: 20px;
           border: 1px solid lightgrey;
           border-radius: 25px;
           transition: all 0.3s ease;
         }
         .wrapper form .field input:focus,
         form .field input:valid {
           border-color: #4158d0;
         }
         .wrapper form .field label {
           position: absolute;
           top: 50%;
           left: 20px;
           color: #999999;
           font-weight: 400;
           font-size: 17px;
           pointer-events: none;
           transform: translateY(-50%);
           transition: all 0.3s ease;
         }
         form .field input:focus ~ label,
         form .field input:valid ~ label {
           top: 0%;
           font-size: 16px;
           color: #4158d0;
           background: #fff;
           transform: translateY(-50%);
         }
         form .content {
           display: flex;
           width: 100%;
           height: 50px;
           font-size: 16px;
           align-items: center;
           justify-content: space-around;
         }
         form .content .checkbox {
           display: flex;
           align-items: center;
           justify-content: center;
         }
         form .content input {
           width: 15px;
           height: 15px;
           background: red;
         }
         form .content label {
           color: #262626;
           user-select: none;
           padding-left: 5px;
         }
         form .content .pass-link {
           color: "";
         }
         form .field input[type="submit"] {
           color: #fff;
           border: none;
           padding-left: 0;
           margin-top: -10px;
           font-size: 20px;
           font-weight: 500;
           cursor: pointer;
           background: linear-gradient(-135deg, #c850c0, #4158d0);
           transition: all 0.3s ease;
         }
         form .field input[type="submit"]:active {
           transform: scale(0.95);
         }
         form .signup-link {
           color: #262626;
           margin-top: 20px;
           text-align: center;
         }
         form .pass-link a,
         form .signup-link a {
           color: #4158d0;
           text-decoration: none;
         }
         form .pass-link a:hover,
         form .signup-link a:hover {
           text-decoration: underline;
         }

      </style>
   </head>
   <body>
      <div class="wrapper">
         <div class="title">
            Login Form
         </div>
         <form action="" method="POST">
            <div class="field">
               <!-- Use a ternary operator to check if $username is set -->
               <input type="text" name="uName" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
               <label>Username</label>
            </div>
            <div class="field">
               <input type="password" name="password" required>
               <label>Password</label>
            </div>
            <div class="content">
               <div class="checkbox">
                  <input type="checkbox" id="remember-me">
                  <label for="remember-me">Remember me</label>
               </div>
               <div class="pass-link">
                  <a href="./forgot-pass/forgot.php">Forgot password?</a>
               </div>
            </div>
            <div class="field">
               <input type="submit" name="submit" value="Login">
            </div>
            <div class="signup-link">
               Don't have an account? <a href="registration.php">Signup now</a>
            </div>
         </form>
         <?php
            if (isset($message)) {
                echo "<p style='color: red; text-align: center;'>$message</p>";
            }
         ?>
      </div>
   </body>
</html>

<?php
include("connection/connect.php"); 
error_reporting(0); 
session_start(); 

// Initialize $username
$username = ''; 

if (isset($_POST['submit'])) { 
    $username = $_POST['uName'];  
    $password = $_POST['password'];
    
    if (!empty($_POST["submit"])) {
        $loginquery = "SELECT * FROM users WHERE username='$uName' && password='" . md5($password) . "'"; //selecting matching records
        $result = mysqli_query($db, $loginquery); //executing
        $row = mysqli_fetch_array($result);
        
        if (is_array($row)) {
            $_SESSION["user_id"] = $row['u_id']; 
            header("refresh:1;url=index.php"); 
        } else {
            $message = "Invalid Username or Password!"; 
        }
    }
}
?>


        


