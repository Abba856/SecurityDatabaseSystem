<?php
   // Check if user is logged in and has appropriate role - This must be at the top
   session_start();
   
   // Regenerate session ID periodically to prevent session fixation
   if (!isset($_SESSION['login_time'])) {
       session_regenerate_id(true);
       $_SESSION['login_time'] = time();
   } elseif (time() - $_SESSION['login_time'] > 3600) { // 1 hour timeout
       session_regenerate_id(true);
       $_SESSION['login_time'] = time();
   }
   
   if(!isset($_SESSION['login_user']) || !isset($_SESSION['user_role'])){
       header("location: index.php");
       exit();
   }
   
   // Allow access to users with role 'admin' or 'user' for criminal management
   $allowed_roles = array('admin', 'user');
   if(!in_array($_SESSION['user_role'], $allowed_roles)) {
       header("location: index.php");
       exit();
   }

   $id=$name=$offName=$crime=$dob=$arrDate=$crimeDate=$sex=$address=$folder=$fname=$more="";

   if(isset($_POST['submit'])){
       // Database connection
       include("config.php");
       
       // File upload validation
       if(isset($_FILES['my_img']) && $_FILES['my_img']['error'] == 0) {
           $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
           $filename = $_FILES['my_img']['name'];
           $filetype = $_FILES['my_img']['type'];
           $filesize = $_FILES['my_img']['size'];
           
           // Verify file extension
           $ext = pathinfo($filename, PATHINFO_EXTENSION);
           if(!array_key_exists($ext, $allowed)) {
               echo "<script>alert('Error: Please select a valid image file.');</script>";
           } elseif($filesize > 500000) { // 500KB limit
               echo "<script>alert('Error: File size is larger than the allowed limit.');</script>";
           } else {
               // Sanitize filename
               $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
               $folder = "images/" . $filename;
               
               if(move_uploaded_file($_FILES['my_img']['tmp_name'], $folder)) {
                   // Sanitize input data
                   $id = (int)$_POST['ID'];
                   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
                   $offName = htmlspecialchars($_POST['offName'], ENT_QUOTES, 'UTF-8');
                   $crime = $_POST['crime'];
                   $dob = $_POST['dob'];
                   $arrDate = $_POST['arrDate'];
                   $crimeDate = $_POST['crimeDate'];
                   $sex = $_POST['sex'];
                   $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
                   $more = htmlspecialchars($_POST['more'], ENT_QUOTES, 'UTF-8');
                   
                   // Validate date formats
                   $date1 = DateTime::createFromFormat('Y-m-d', $dob);
                   $date2 = DateTime::createFromFormat('Y-m-d', $arrDate);
                   $date3 = DateTime::createFromFormat('Y-m-d', $crimeDate);
                   
                   if(!$date1 || !$date2 || !$date3) {
                       echo "<script>alert('Error: Invalid date format.');</script>";
                   } else {
                       // Prepared statement to prevent SQL injection
                       $q1 = "INSERT INTO `info`(`id`, `name`, `offname`, `crime`, `dob`, `arrDate`, `crimeDate`, `sex`, `address`,`img`,`more`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                       $stmt = mysqli_prepare($db, $q1);
                       mysqli_stmt_bind_param($stmt, "issssssssss", $id, $name, $offName, $crime, $dob, $arrDate, $crimeDate, $sex, $address, $folder, $more);
                       
                       if(mysqli_stmt_execute($stmt)) {
                           echo "<script>alert('Data Stored Successfully!');</script>";
                       } else {
                           echo "<script>alert('Error: " . mysqli_error($db) . "');</script>";
                       }
                       mysqli_stmt_close($stmt);
                   }
               } else {
                   echo "<script>alert('Error: There was a problem uploading your file.');</script>";
               }
           }
       } else {
           echo "<script>alert('Error: Please select an image file.');</script>";
       }
   }
   ?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Security Database System - Criminal Records</title>
   <link rel="stylesheet" type="text/css" media="screen" href="new_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <script type="text/javascript">
      function submitBtn()
      {
          if (document.forms["crimeInfo"]["ID"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if (document.forms["crimeInfo"]["name"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if (document.forms["crimeInfo"]["offName"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if (document.forms["crimeInfo"]["crime"].value=="--Select Crime--" || document.forms["crimeInfo"]["crime"].value=="")
          {
              alert('Please select a crime type');
              return false;
          }
          else if(document.forms["crimeInfo"]["dob"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if(document.forms["crimeInfo"]["more"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if(document.forms["crimeInfo"]["arrDate"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if(document.forms["crimeInfo"]["crimeDate"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if(!getRadioValue("sex")) // Check if any gender radio button is selected
          {
              alert('Please select a gender');
              return false;
          }
          else if(document.forms["crimeInfo"]["address"].value=="")
          {
              alert('Please fill out all the fields');
              return false;
          }
          else if(document.forms["crimeInfo"]["my_img"].value=="")
          {
              alert('Please select an image');
              return false;
          }
          else{
              // Date validation
              var dob = new Date(document.forms["crimeInfo"]["dob"].value);
              var currentDate = new Date();
              if(dob > currentDate) {
                  alert('Date of birth cannot be in the future');
                  return false;
              }
              return true;
          }
      }
      
      // Function to get the selected value of a radio button group
      function getRadioValue(radioName) {
          var radios = document.forms["crimeInfo"].elements[radioName];
          for (var i = 0; i < radios.length; i++) {
              if (radios[i].checked) {
                  return radios[i].value;
              }
          }
          return null;
      }
      
   </script>
</head>
<body>
   <!-- Header with Navigation -->
   <header>
      <div class="logo-container">
         <img src="police_logo.png" alt="Security Database System Logo" class="logo">
         <h1 class="site-title">Security Database System</h1>
      </div>
      <nav class="navbar">
         <ul class="nav-links">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
            <li><a href="offList.php"><i class="fas fa-users"></i> Officers</a></li>
            <li><a href="analysis.php"><i class="fas fa-chart-bar"></i> Analytics</a></li>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="users.php"><i class="fas fa-cog"></i> User Panel</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
         </ul>
      </nav>
   </header>

   <div class="container">
      <div class="main-content">
         <div class="card fade-in-up">
            <h2 class="card-title"><i class="fas fa-user-injured"></i> Criminal Information Form</h2>
            <p>Add new criminal records to the database</p>
            
            <form id="crimeInfo" method="post" enctype="multipart/form-data" class="mt-20">
               <div class="form-row">
                  <div class="form-col">
                     <div class="form-group">
                        <label for="my_img" class="form-label"><i class="fas fa-image"></i> Criminal's Image</label>
                        <input type="file" class="form-control" name="my_img" id="my_img" required>
                     </div>
                     
                     <div class="form-group">
                        <label for="ID" class="form-label"><i class="fas fa-id-card"></i> Criminal ID</label>
                        <input type="text" class="form-control" required name="ID" id="ID">
                     </div>
                     
                     <div class="form-group">
                        <label for="name" class="form-label"><i class="fas fa-user"></i> Criminal Name</label>
                        <input type="text" class="form-control" required name="name" id="name">
                     </div>
                     
                     <div class="form-group">
                        <label for="offName" class="form-label"><i class="fas fa-user-tie"></i> Assigned Officer</label>
                        <input type="text" class="form-control" required name="offName" id="offName">
                     </div>
                     
                     <div class="form-group">
                        <label for="crime" class="form-label"><i class="fas fa-gavel"></i> Crime Type</label>
                        <select required name="crime" id="crime" class="form-control">
                           <option value="">--Select Crime--</option>
                           <option value="Ragging">Ragging</option>
                           <option value="Robbery">Robbery</option>
                           <option value="Kidnapping">Kidnapping</option>
                           <option value="Rape">Rape</option>
                           <option value="Murder">Murder</option>
                           <option value="Fraud">Fraud</option>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-col">
                     <div class="form-group">
                        <label for="more" class="form-label"><i class="fas fa-info-circle"></i> Section/Details</label>
                        <input type="text" class="form-control" name="more" id="more" required>
                     </div>
                     
                     <div class="form-group">
                        <label for="dob" class="form-label"><i class="fas fa-birthday-cake"></i> Date of Birth</label>
                        <input type="date" class="form-control" required name="dob" id="dob">
                     </div>
                     
                     <div class="form-group">
                        <label for="arrDate" class="form-label"><i class="fas fa-calendar-check"></i> Arrest Date</label>
                        <input type="date" class="form-control" required name="arrDate" id="arrDate">
                     </div>
                     
                     <div class="form-group">
                        <label for="crimeDate" class="form-label"><i class="fas fa-calendar-day"></i> Date of Crime</label>
                        <input type="date" class="form-control" required name="crimeDate" id="crimeDate">
                     </div>
                     
                     <div class="form-group">
                        <label class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                        <div class="mt-10">
                           <label class="form-label"><input type="radio" name="sex" value="M" required> Male</label>
                           <label class="form-label ml-10"><input type="radio" name="sex" value="F"> Female</label>
                           <label class="form-label ml-10"><input type="radio" name="sex" value="O"> Others</label>
                        </div>
                     </div>
                     
                     <div class="form-group">
                        <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Address</label>
                        <textarea rows="3" class="form-control" required name="address" id="address"></textarea>
                     </div>
                  </div>
               </div>
               
               <div class="text-center mt-20">
                  <button type="submit" class="btn" onclick="submitBtn()" value="upload" name="submit">
                     <i class="fas fa-save"></i> Submit Record
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
</body>
</html>