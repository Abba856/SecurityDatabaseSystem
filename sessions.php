<?php
   include('config.php');
   session_start();
   
   $user_check = $_SESSION['login_user'];
   
   // Use prepared statement to prevent SQL injection
   $ses_sql = "SELECT uname, role FROM users WHERE uname = ?";
   $stmt = mysqli_prepare($db, $ses_sql);
   mysqli_stmt_bind_param($stmt, "s", $user_check);
   mysqli_stmt_execute($stmt);
   $result = mysqli_stmt_get_result($stmt);
   
   $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
   
   $login_session = $row['uname'];
   $_SESSION['user_role'] = $row['role']; // Ensure role is available in session
   
   if(!isset($_SESSION['login_user'])){
      header("location: index.php");
      die();
   }
?>