<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Security Database System - Print Records</title>
   <link rel="stylesheet" type="text/css" media="screen" href="new_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
      @media print {
         .no-print {
            display: none;
         }
      }
   </style>
</head>
<body>
   <!-- Header with Navigation (hidden when printing) -->
   <header class="no-print">
      <div class="logo-container">
         <img src="police_logo.png" alt="Security Database System Logo" class="logo">
         <h1 class="site-title">Security Database System</h1>
      </div>
      <nav class="navbar">
         <ul class="nav-links">
            <li><a href="home.php"><i class="fas fa-info-circle"></i> Information</a></li>
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

   <div class="container no-print">
      <div class="main-content">
         <div class="card fade-in-up">
            <h2 class="card-title"><i class="fas fa-print"></i> Print Criminal Records</h2>
            <p>Preview and print the search results</p>
            
            <div class="text-center mt-20 mb-20">
               <button class="btn" onclick="window.print()"><i class="fas fa-print"></i> Print Records</button>
               <a href="search.php" class="btn btn-danger ml-10"><i class="fas fa-arrow-left"></i> Back to Search</a>
            </div>
         </div>
      </div>
   </div>

   <!-- Printable Content -->
   <div class="container">
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
         
         // Allow access to users with role 'admin' or 'user' for printing
         $allowed_roles = array('admin', 'user');
         if(!in_array($_SESSION['user_role'], $allowed_roles)) {
             header("location: index.php");
             exit();
         }
         
         include("config.php");
             $data=$_SESSION['data'];
             // Use prepared statement to prevent SQL injection
             $q1 = "SELECT * FROM info WHERE name LIKE ?";
             $stmt = mysqli_prepare($db, $q1);
             $search_param = "%$data%";
             mysqli_stmt_bind_param($stmt, "s", $search_param);
             mysqli_stmt_execute($stmt);
             $result = mysqli_stmt_get_result($stmt);
             
             if($result && mysqli_num_rows($result) > 0)
             {
         echo '<div class="table-responsive">
         <table>
             <thead>
         <tr>
             <th><i class="fas fa-image"></i> Criminal Image</th>
             <th><i class="fas fa-id-card"></i> Criminal ID</th>
             <th><i class="fas fa-user"></i> Criminal Name</th>
             <th><i class="fas fa-user-tie"></i> Assigned Officer</th>
             <th><i class="fas fa-gavel"></i> Crime Type</th>
             <th><i class="fas fa-info-circle"></i> Section</th>
             <th><i class="fas fa-birthday-cake"></i> DOB</th>
             <th><i class="fas fa-calendar-check"></i> Arrest Date</th>
             <th><i class="fas fa-calendar-day"></i> Date of Crime</th>
             <th><i class="fas fa-venus-mars"></i> Gender</th>
             <th><i class="fas fa-map-marker-alt"></i> Address</th>
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
         </div>';
             }
             mysqli_stmt_close($stmt);
         ?>
   </div>

   <script>
      // Auto-print when the page loads
      window.onload = function() {
         // Uncomment the next line if you want to automatically print on load
         // window.print();
      };
   </script>
</body>
</html>