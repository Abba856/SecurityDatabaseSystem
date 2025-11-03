<!DOCTYPE html>
<html>
   <head>
      <meta charset='utf-8'>
      <meta http-equiv='X-UA-Compatible' content='IE=edge'>
      <title>Security Database System- Home</title>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <link rel='stylesheet' type='text/css' media='screen' href='style_1.css'>
      <script src='main.js'></script>
   </head>
   <body>
      <button name="logout" style="margin-left: 1424px;"><img src="logout.png" style="width:10px"><a href = "logout.php">Log out</a></button>
      <div class="container" style="height:980px;">
         <div class="finaldiv">
            <span class="head1"><img src="police_logo.png" width="16.2%"></span>
            <span class="head_txt">Security Database System</span>
            <span class="head2"><img src="police_logo.png" width="38%"></span>
            <br>
            <div class="navbar">
               <ul style="margin-left:20px">
                  <li><a href="home.php"><b>Information</b></a></li>
                  <li><a href="search.php" class="active"><b>Search</b></a></li>
                  <li><a href="offList.php"><b>Officers</b></a></li>
                  <li><a href="analysis.php"><b>Analytics</b></a></li>
                  <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                  <li><a href="users.php"><b>Admin Panel</b></a></li>
                  <?php endif; ?>
                  <li><a href="logout.php"><b>Logout</b></a></li>
               </ul>
            </div>
            <div class="searchGroup" >
               <form method="post">
                  <input type="text" class="searchBar" placeholder="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search Criminal's By Name" name="search">
                  <button class="searchBtn"><img src="search.png" width="50%"></button>
                  <img src="police_logo_1.png" style="position:absolute;top:140px;margin-top: -90px; background-size: 90%;margin-left: -50px; height:469px">
               </form>
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
                      $q1 = "SELECT * FROM info WHERE name = ?";
                      $stmt = mysqli_prepare($db, $q1);
                      mysqli_stmt_bind_param($stmt, "s", $data);
                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);
                      
                      if($result && mysqli_num_rows($result) > 0)
                      {
                  echo '<table border="5" style="position:relative;left:-180px;top: 65px;display:block">
                      <thead>
                  <tr>
                      <th>Criminal Image</th>
                      <th>Criminal ID</th>
                      <th>Criminal Name</th>
                      <th>Assigned Officer</th>
                      <th>Crime Type</th>
                      <th>Section</th>
                      <th>Criminals DOB</th>
                      <th>Arrest Date</th>
                      <th>Date of Crime</th>
                      <th>Gender</th>
                      <th>Address</th>
                  </tr>
                  </thead>';
                  
                          while ($row = mysqli_fetch_array($result)) {
                           $info='
                  <tr>
                      <td><img src="'.htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8').'" width="100" onerror="this.onerror=null;this.src=\'images/default.jpg\';"></td>
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
                                                
                  echo '<center><a href="printable.php" target="_blank" style="position: absolute;top: 500px;left: 400px;"><button name="print" class="submitBtn">Print</button></a></center>';
                         
                  
                      }
                      else{
                          echo "<script>alert('Record Not Found')</script>";
                      }
                      mysqli_stmt_close($stmt);
                  }
                  ?>
               </table>

            </div>
         </div>
      </div>
      </div>

   </body>
</html>