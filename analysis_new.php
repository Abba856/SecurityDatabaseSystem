<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Security Database System - Analytics</title>
   <link rel="stylesheet" type="text/css" media="screen" href="new_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-core.min.js"></script>
   <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-pie.min.js"></script>
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
            <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
            <li><a href="offList.php"><i class="fas fa-users"></i> Officers</a></li>
            <li><a href="analysis.php" class="active"><i class="fas fa-chart-bar"></i> Analytics</a></li>
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
            <h2 class="card-title"><i class="fas fa-chart-pie"></i> Crime Analytics Dashboard</h2>
            <p>Visual representation of crime statistics and trends</p>
            
            <div class="stats-container mt-20">
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
                  
                  // Allow access to users with role 'admin' or 'user' for analytics
                  $allowed_roles = array('admin', 'user');
                  if(!in_array($_SESSION['user_role'], $allowed_roles)) {
                      header("location: index.php");
                      exit();
                  }
                  
                  include("config.php");
                  
                  // Get total count
                  $q2 = "SELECT COUNT(*) as total FROM `info`";
                  $result = mysqli_query($db, $q2);
                  $row = mysqli_fetch_assoc($result);
                  $total = $row['total'];
                  
                  // Initialize variables
                  $Ragging = $Robbery = $Kidnapping = $Rape = $Murder = $Fraud = 0;
                  
                  // Get count for each crime type
                  $crimes = array("Ragging", "Robbery", "Kidnapping", "Rape", "Murder", "Fraud");
                  $crimeCounts = array();
                  
                  foreach($crimes as $crime) {
                      $stmt = mysqli_prepare($db, "SELECT COUNT(*) as count FROM info WHERE crime = ?");
                      mysqli_stmt_bind_param($stmt, "s", $crime);
                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);
                      $row = mysqli_fetch_assoc($result);
                      $crimeCounts[$crime] = $row['count'];
                      mysqli_stmt_close($stmt);
                  }
                  
                  // Display summary statistics
                  echo '<div class="stat-card">
                           <div class="stat-number">'. $total .'</div>
                           <div class="stat-label">Total Records</div>
                        </div>';
                  
                  foreach($crimeCounts as $crime => $count) {
                      $percentage = ($total > 0) ? round(($count / $total) * 100, 1) : 0;
                      echo '<div class="stat-card">
                               <div class="stat-number">'. $count .'</div>
                               <div class="stat-label">'. $crime .' ('. $percentage .'%)</div>
                            </div>';
                  }
               ?>
            </div>
            
            <div class="chart-container mt-20" style="text-align: center;">
               <h3><i class="fas fa-chart-pie"></i> Crime Distribution</h3>
               <div id="chart-container" style="height: 400px; width: 100%; max-width: 800px; margin: 0 auto;"></div>
            </div>
         </div>
      </div>
   </div>

   <script>
      anychart.onDocumentReady(function() {
         var data = [
            {x: "Ragging", value: <?php echo isset($crimeCounts['Ragging']) ? $crimeCounts['Ragging'] : 0; ?>},
            {x: "Robbery", value: <?php echo isset($crimeCounts['Robbery']) ? $crimeCounts['Robbery'] : 0; ?>},
            {x: "Kidnapping", value: <?php echo isset($crimeCounts['Kidnapping']) ? $crimeCounts['Kidnapping'] : 0; ?>},
            {x: "Rape", value: <?php echo isset($crimeCounts['Rape']) ? $crimeCounts['Rape'] : 0; ?>},
            {x: "Murder", value: <?php echo isset($crimeCounts['Murder']) ? $crimeCounts['Murder'] : 0; ?>},
            {x: "Fraud", value: <?php echo isset($crimeCounts['Fraud']) ? $crimeCounts['Fraud'] : 0; ?>},
         ];
         
         var chart = anychart.pie();
         chart.title("Crime Distribution");
         chart.data(data);
         
         // Set chart colors
         chart.palette(["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#FF9F40"]);
         
         chart.container("chart-container");
         chart.draw();
      });
   </script>
</body>
</html>