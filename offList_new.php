<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Security Database System - Officers List</title>
   <link rel="stylesheet" type="text/css" media="screen" href="new_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <li><a href="offList.php" class="active"><i class="fas fa-users"></i> Officers</a></li>
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
            <h2 class="card-title"><i class="fas fa-users"></i> Police Officers Database</h2>
            <p>View and manage police officer records</p>
            
            <div class="table-responsive mt-20">
               <table>
                  <thead>
                     <tr>
                        <th><i class="fas fa-user"></i> Officer Name</th>
                        <th><i class="fas fa-id-card"></i> Officer ID</th>
                        <th><i class="fas fa-fingerprint"></i> ID</th>
                        <th><i class="fas fa-phone"></i> Contact</th>
                        <th><i class="fas fa-venus-mars"></i> Gender</th>
                        <th><i class="fas fa-gun"></i> Weapon</th>
                        <th><i class="fas fa-briefcase"></i> Role</th>
                     </tr>
                  </thead>
                  <tbody>
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
                        
                        // Allow access to users with role 'admin' or 'user' to view officer list
                        $allowed_roles = array('admin', 'user');
                        if(!in_array($_SESSION['user_role'], $allowed_roles)) {
                            header("location: index.php");
                            exit();
                        }
                        
                        include("config.php");
                        
                        $q1 = "SELECT * FROM `officer`";
                        $result = mysqli_query($db,$q1);
                        if($result){
                            while($row=mysqli_fetch_array($result)){
                                echo'
                                <tr>
                                    <td>'.htmlspecialchars($row['offName'], ENT_QUOTES, 'UTF-8').'</td>
                                    <td>'. htmlspecialchars($row['offID'], ENT_QUOTES, 'UTF-8').'</td>
                                    <td>'. htmlspecialchars($row['ID'], ENT_QUOTES, 'UTF-8').'</td>
                                    <td>'.htmlspecialchars($row['contact'], ENT_QUOTES, 'UTF-8').'</td>
                                    <td>'. htmlspecialchars($row['gender'], ENT_QUOTES, 'UTF-8').'</td>
                                    <td>'. htmlspecialchars($row['weapon'], ENT_QUOTES, 'UTF-8').'</td>
                                    <td>'.htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8').'</td>
                                </tr>';
                            }
                        }
                        else{
                            echo '<tr><td colspan="7" class="text-center">Error loading officers data</td></tr>';
                        }
                     ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</body>
</html>