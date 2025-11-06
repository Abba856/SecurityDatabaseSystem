<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Database System - Login</title>
    <link rel="stylesheet" type="text/css" href="new_style.css">
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
                <h2><i class="fas fa-shield-alt"></i> Welcome Back</h2>
                <p>Sign in to access the Security Database System</p>
            </div>
            
            <div class="login-body">
                <div class="login-button-group">
                    <form method="post">
                        <button type="submit" class="btn btn-block login-btn" name="btn1">
                            <i class="fas fa-user-lock"></i> Security Database System Login
                        </button>
                    </form>
                    
                    <form method="post">
                        <button type="submit" class="btn btn-block login-btn" name="btn2" style="background: linear-gradient(to right, #27ae60, #2ecc71);">
                            <i class="fas fa-user-tie"></i> Police Officer Management Login
                        </button>
                    </form>
                </div>
                
                <div class="mt-20 text-center">
                    <p class="text-light">Secure access to criminal and police records</p>
                    <p><small>Authorized personnel only</small></p>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Check if user is already logged in
    session_start();
    if(isset($_SESSION['login_user']) && isset($_SESSION['user_role'])) {
        // Redirect based on user role
        if($_SESSION['user_role'] === 'admin') {
            // Admin can access both areas, direct to home by default
            header("location: home.php");
        } else {
            // Regular users go to home page
            header("location: home.php");
        }
        exit();
    }

    if (isset($_POST['btn1'])) {
        header("location: login1.php");
    }
    else if (isset($_POST['btn2'])){
        header("location: login2.php");
    }
    ?>
</body>
</html>