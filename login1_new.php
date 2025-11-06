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
            $error = "Invalid Username or Password";
         }
      } else {
         $error = "Invalid Username or Password";
      }
      mysqli_stmt_close($stmt);
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Database System - Criminal Records Login</title>
    <link rel="stylesheet" type="text/css" media="screen" href="new_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="logo-container">
            <img src="logo.jpg" alt="Security Database System Logo" class="logo">
            <h1 class="site-title">Security Database System</h1>
        </div>
    </header>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fas fa-user-lock"></i> Criminal Records Login</h2>
                <p>Access to criminal records database</p>
            </div>
            
            <div class="login-body">
                <?php if(isset($error)): ?>
                    <div class="error text-center mb-20"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label for="uname" class="form-label"><i class="fas fa-user"></i> Username</label>
                        <input type="text" class="form-control" name="uname" id="uname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pass" class="form-label"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" class="form-control" name="pass" id="pass" required>
                    </div>
                    
                    <button type="submit" class="btn btn-block login-btn"><i class="fas fa-sign-in-alt"></i> Login</button>
                </form>
                
                <div class="mt-20 text-center">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Main Menu</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>