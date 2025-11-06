<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Database System - Search Officers</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../new_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src='../main.js'></script>
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="logo-container">
            <img src="../police_logo.png" alt="Security Database System Logo" class="logo">
            <h1 class="site-title">Security Database System</h1>
        </div>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="addOfficer.php"><i class="fas fa-user-plus"></i> Add Officer</a></li>
                <li><a href="searchOff.php" class="active"><i class="fas fa-search"></i> Search</a></li>
                <li><a href="weapon.php"><i class="fas fa-gun"></i> Weapons</a></li>
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li><a href="../users.php"><i class="fas fa-cog"></i> Users</a></li>
                <?php endif; ?>
                <li><a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="main-content">
            <div class="card fade-in-up">
                <h2 class="card-title"><i class="fas fa-search"></i> Search Officers</h2>
                <p>Find officer records by name or other details</p>
                
                <div class="search-container mt-20">
                    <form method="post">
                        <input type="text" class="search-bar" placeholder="Search officer by name..." name="search" required>
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
                    header("location: ../index.php");
                    exit();
                }
                
                // Only allow admin users to search officers
                if($_SESSION['user_role'] !== 'admin') {
                    header("location: ../index.php");
                    exit();
                }
                
                include("../config.php");
                if($_SERVER['REQUEST_METHOD']=='POST')
                {
                    $data = $_POST['search'];
                    // Use prepared statement to prevent SQL injection
                    $q1 = "SELECT offName, offID, ID, contact, gender, weapon, role FROM officer WHERE offName = ?";
                    $stmt = mysqli_prepare($db, $q1);
                    mysqli_stmt_bind_param($stmt, "s", $data);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if($result)
                    {
                        if (mysqli_num_rows($result)<=0) {
                          echo "<script>alert('Record Not Found')</script>";
                          die('');}
                            echo '
                    <div class="table-responsive mt-20">
                    <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Officer Name</th>
                            <th><i class="fas fa-id-card"></i> Officer ID</th>
                            <th><i class="fas fa-fingerprint"></i> Assigned Case ID</th>
                            <th><i class="fas fa-phone"></i> Contact</th>
                            <th><i class="fas fa-venus-mars"></i> Gender</th>
                            <th><i class="fas fa-gun"></i> Weapon</th>
                            <th><i class="fas fa-briefcase"></i> Role</th>
                        </tr>
                    </thead>
                    <tbody>';
            while ($row = mysqli_fetch_array($result)){
            echo'
            <tr>
                <td>'.$row['offName'].'</td>
                <td>'.$row['offID'].'</td>
                 <td>'.$row['ID'].'</td>
                 <td>'.$row['contact'].'</td>
                  <td>'.$row['gender'].'</td>
                   <td>'.$row['weapon'].'</td>
                    <td>'.$row['role'].'</td>
            </tr>

        ';

            }
            echo '</tbody></table></div>';

                    }
                    }
                    else{
                        echo"";
                    }
            ?>
            </div>
        </div>
    </div>
</body>
</html>