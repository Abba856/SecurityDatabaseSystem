<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', 'root');
   define('DB_DATABASE', 'criminalinfo');
   
   // Create connection with error handling
   $db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
   
   // Check connection
   if (!$db) {
       die("Connection failed: " . mysqli_connect_error());
   }
   
   // Set charset to prevent character encoding issues
   mysqli_set_charset($db, "utf8");
?>