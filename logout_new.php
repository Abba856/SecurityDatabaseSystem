<?php
   session_start();
   
   // Unset all session variables
   $_SESSION = array();
   
   // Destroy the session
   if(session_destroy()) {
      // Redirect to the main login page
      header("Location: index.php");
      exit();
   } else {
      // If session destruction failed, redirect anyway
      header("Location: index.php");
      exit();
   }
?>