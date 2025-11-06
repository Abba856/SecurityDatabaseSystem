<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Security Database System - Search Records</title>
   <link rel="stylesheet" type="text/css" media="screen" href="new_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <script src='main.js'></script>
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
            <li><a href="home.php"><i class="fas fa-info-circle"></i> Information</a></li>
            <li><a href="search.php" class="active"><i class="fas fa-search"></i> Search</a></li>
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
            <h2 class="card-title"><i class="fas fa-search"></i> Search Criminal Records</h2>
            <p>Find criminal records by name, ID, or other details</p>
            
            <div class="search-container mt-20">
               <form method="post" class="w-100">
                  <input type="text" class="search-bar" placeholder="Search criminal by name..." name="search" required>
                  <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
               </form>
            </div>
            
            <?php
               session_start();
               
               // Regenerate session ID periodically to prevent session fixation
               if (!isset($_SESSION['login_time'])) {
                   session_regenerate_id(true);
                   $_SESSION['login_time'] = time();
               } elseif (time() - $_SESSION['login_time'] > 3600) { // 1 hour timeout
                   session_regenerate_id(true);
                   $_SESSION['login_time'] = time();
               }
               
               // Check if user is logged in and has appropriate role
               if(!isset($_SESSION['login_user']) || !isset($_SESSION['user_role'])){
                   header("location: index.php");
                   exit();
               }
               
               // Allow access to users with role 'admin' or 'user' for search functionality
               $allowed_roles = array('admin', 'user');
               if(!in_array($_SESSION['user_role'], $allowed_roles)) {
                   header("location: index.php");
                   exit();
               }
               
               include("config.php");
               
               if($_SERVER['REQUEST_METHOD']=='POST')
               {
                   $data=$_POST['search'];
                   $_SESSION['data']=$data;
                   
                   // Use prepared statement to prevent SQL injection
                   $q1 = "SELECT * FROM info WHERE name LIKE ?";
                   $stmt = mysqli_prepare($db, $q1);
                   $search_param = "%$data%";
                   mysqli_stmt_bind_param($stmt, "s", $search_param);
                   mysqli_stmt_execute($stmt);
                   $result = mysqli_stmt_get_result($stmt);
                   
                   if($result && mysqli_num_rows($result) > 0)
                   {
                       echo '<div class="table-responsive mt-20">
                       <table>
                           <thead>
                               <tr>
                                   <th>Criminal Image</th>
                                   <th>Criminal ID</th>
                                   <th>Criminal Name</th>
                                   <th>Assigned Officer</th>
                                   <th>Crime Type</th>
                                   <th>Section</th>
                                   <th>DOB</th>
                                   <th>Arrest Date</th>
                                   <th>Date of Crime</th>
                                   <th>Gender</th>
                                   <th>Address</th>
                               </tr>
                           </thead>
                           <tbody>';
                   
                       while ($row = mysqli_fetch_array($result)) {
                           $info='<tr>
                               <td><img src="'.htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8').'" width="80" class="rounded-img" onerror="this.onerror=null;this.src=\'images/default.jpg\';"></td>
                               <td>'.htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['offname'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['crime'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['more'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['dob'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['arrDate'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['crimeDate'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['sex'], ENT_QUOTES, 'UTF-8').'</td>
                               <td>'.htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8').'</td>
                           </tr>';
                           echo $info;
                       }
                       
                       echo '</tbody>
                       </table>
                       </div>
                       <div class="text-center mt-20">
                           <a href="printable.php" target="_blank">
                               <button class="btn"><i class="fas fa-print"></i> Print Records</button>
                           </a>
                       </div>';
                   }
                   else{
                       echo "<div class='error text-center mt-20'><i class='fas fa-exclamation-triangle'></i> No records found for '$data'</div>";
                   }
                   mysqli_stmt_close($stmt);
               }
            ?>
         </div>
      </div>
   </div>
</body>
</html>