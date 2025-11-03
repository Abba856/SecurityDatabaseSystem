<!DOCTYPE html>
<html>
   <head>
      <meta charset='utf-8'>
      <meta http-equiv='X-UA-Compatible' content='IE=edge'>
      <title>Security Database System- Home</title>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <link rel='stylesheet' type='text/css' media='screen' href='style_1.css'>
      <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-core.min.js"></script>
      <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-pie.min.js"></script>
      <style type="text/css">
         #contain{
            height: 600px;
            width: 600px;
            margin-left: 150px;
            margin-top: 20px;
         }
      </style>
   </head>
   <body>
      <button name="logout" style="margin-left: 1424px;"><img src="logout.png" style="width:10px"><a href = "logout.php">Log out</a></button>
      <div class="container" style="height:780px;">
         <div class="finaldiv">
            <span class="head1"><img src="police_logo.png" width="16.2%"></span>
            <span class="head_txt">Security Database System</span>
            <span class="head2"><img src="police_logo.png" width="38%"></span>
            <br>
            <div class="navbar">
               <ul style="margin-left:20px">
                  <li><a href="index.php"><b>Information</b></a></li>
                  <li><a href="search.php"><b>Search</b></a></li>
                  <li><a href="offList.php"><b>Officers</b></a></li>
                  <li><a href="analysis.php" class="active"><b>Analytics</b></a></li>
                  <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                  <li><a href="admin_users.php"><b>Admin Panel</b></a></li>
                  <?php endif; ?>
                  <li><a href="logout.php"><b>Logout</b></a></li>
               </ul>
               <div id="contain" style=""></div>
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
                      $crimeCounts[$crime] = ($total > 0) ? ($row['count'] / $total) * 100 : 0;
                      mysqli_stmt_close($stmt);
                  }
                  
                  echo '
                  <script>
                  anychart.onDocumentReady(function() {
                     var data = [
                        {x: "Ragging", value: "'.$crimeCounts['Ragging'].'"},
                        {x: "Robbery", value: "'.$crimeCounts['Robbery'].'"},
                        {x: "Kidnapping", value: "'.$crimeCounts['Kidnapping'].'"},
                        {x: "Rape", value: "'.$crimeCounts['Rape'].'"},
                        {x: "Murder", value: "'.$crimeCounts['Murder'].'"},
                        {x: "Fraud", value: "'.$crimeCounts['Fraud'].'"},
                     ];
                    var chart = anychart.pie();
                    chart.title("Crime Rate");
                    chart.data(data);
                    chart.container("contain");
                    chart.draw();
                    });
                    </script>';
              ?>
               
            </div>

            </div>
         </div>
   </body>
</html>