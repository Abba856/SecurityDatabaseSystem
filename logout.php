<?php
   session_start();
   
   // Unset all session variables
   $_SESSION = array();
   
   // Destroy the session
   if(session_destroy()) {
      header("Location: index.php");
      exit();
   }
?>