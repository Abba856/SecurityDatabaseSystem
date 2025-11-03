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
            <div class="btn_div"><button  class="btn" name="btn1" style="width: 250px;height:60px  ;margin-left: -75px;">Login to Security Database SystemSystem</button></div>
            <div class="btn_div"><button  class="btn" name="btn2" style="width: 250px;height:60px ;margin-left: -75px;">Login to Police Officer Management System</button></div>
        </form>
    </div>
</body>
</html>
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