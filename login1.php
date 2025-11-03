<?php
   include("config.php");
   session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      
      $myusername = mysqli_real_escape_string($db,$_POST['uname']);
      $mypassword = $_POST['pass']; // Don't escape password, will be hashed
      
      // Use prepared statement to prevent SQL injection
      $sql = "SELECT * FROM users WHERE uname = ?";
      $stmt = mysqli_prepare($db, $sql);
      mysqli_stmt_bind_param($stmt, "s", $myusername);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      $count = mysqli_num_rows($result);
      
      if($count == 1) {
         $row = mysqli_fetch_assoc($result);
         $hashed_password = $row['pass'];
         $user_role = $row['role']; // Get user role
         
         // Check if password is hashed (starts with $) or plain text for backward compatibility
         if ((substr($hashed_password, 0, 1) === '$' && password_verify($mypassword, $hashed_password)) || 
             $hashed_password === $mypassword) {
            $_SESSION['login_user'] = $myusername;
            $_SESSION['user_role'] = $user_role; // Store user role in session
            $_SESSION['login_time'] = time(); // Prevent session fixation
            
            header("location: home.php");
            exit();
         } else {
            echo "<script>alert('Invalid Username or Password')</script>";
         }
      } else {
         echo "<script>alert('Invalid Username or Password')</script>";
      }
      mysqli_stmt_close($stmt);
   }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Login</title>
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>
</head>
<body>
    <div id="box1">
        <span id="sp1"><img src="logo.jpg"  width="15%"></span>
        <br>
        <br>
        <form method="post">
            Username
            <br> 
            <input type="text" class="inp" name="uname" required>
            <br>
            <br>
            Password 
            <br>
            <input type="password" class="inp" name="pass" required>
            <div class="btn_div"><button type="submit" class="btn">Login</button></div>
        </form>
    </div>
</body>
</html>