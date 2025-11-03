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
<html>
   <head>
      <meta charset='utf-8'>
      <meta http-equiv='X-UA-Compatible' content='IE=edge'>
      <title>Security Database System- Home</title>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <link rel='stylesheet' type='text/css' media='screen' href='style_1.css'>
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
      <button name="logout" style="margin-left: 1424px;"><img src="logout.png" style="width:10px"><a href = "logout.php">Log out</a></button>
      <div class="container" style="height:980px;">
      <div class="finaldiv">
         <span class="head1"><img src="police_logo.png" width="16.2%"></span>
         <span class="head_txt">Security Database System</span>
         <span class="head2"><img src="police_logo.png" width="38%"></span>
         <br>
         <div class="navbar" style="background-color:yellow;">
            <ul style="margin-left:40px">
               <li><a href="index.php" class="active"><b>Information</b></a></li>
               <li><a href="search.php"><b>Search</b></a></li>
               <li><a href="offList.php"><b>Officers</b></a></li>
               <li><a href="analysis.php"><b>Analytics</b></a></li>
               <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
               <li><a href="users.php"><b>User Panel</b></a></li>
               <?php endif; ?>
               <li><a href="logout.php"><b>Logout</b></a></li>
            </ul>
         </div>
         <form id="crimeInfo" method="post" style="position:absolute;top:140px;background-image: url('police_logo_1.png'); background-repeat:no-repeat;margin-top: 50px; background-size: 90%; width: 50%;height:469px" enctype="multipart/form-data">
            <table >
               <tr>
                  <td>Criminals Image</td>
                  <td>&nbsp;&nbsp;&nbsp;<input type="file" name="my_img"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Criminal ID</td>
                  <td><input type="text" required name="ID"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Criminal Name</td>
                  <td><input type="text" required name="name"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Assigned Officer</td>
                  <td><input type="text" required name="offName"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Crime Type</td>
                  <td>
                     <select required name="crime">
                        <option value="">--Select Crime--</option>
                        <option value="Ragging">Ragging</option>
                        <option value="Robbery">Robbery</option>
                        <option value="Kidnapping">Kidnapping</option>
                        <option value="Rape">Rape</option>
                        <option value="Murder">Murder</option>
                        <option value="Fraud">Fraud</option>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>
                     Section
                  </td>
                  <td>
                     <input type="text" name="more" required>
                  </td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Criminals DOB</td>
                  <td><input type="date" required name="dob"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Arrest Date</td>
                  <td><input type="date" required name="arrDate"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Date of Crime</td>
                  <td><input type="date" required name="crimeDate"></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Gender</td>
                  <td>
                     <input type="radio" name="sex" value="M">Male
                     <input type="radio" name="sex" value="F">Female
                     <input type="radio" name="sex" value="O">Others
                  </td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
               <tr>
                  <td>Address </td>
                  <td>&nbsp;<textarea rows="2" required name="address"></textarea></td>
               </tr>
               <tr>
                  <td> 
                     <br>
                  </td>
               </tr>
            </table>
            <button type="submit" class="submitBtn" onclick="submitBtn()" value="upload" name="submit"><b>Submit</b></button>
         </form>
      </div>
   </body>
</html>