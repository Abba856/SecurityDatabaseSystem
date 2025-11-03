<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Security Database System- Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style_1.css'>
</head>
<body>
    <button name="logout" style="margin-left: 1300px;"><img src="../logout.png" style="width:10px"><a href = "../logout.php">Log out</a></button>
    <div class="container">
        <div class="finaldiv">
        <span class="head1"><img src="police_logo.png" width="16.2%"></span>
        <span class="head_txt">Security Database SystemSystem</span>
        <span class="head2"><img src="police_logo.png" width="38%"></span>
    
    <br>
    <div class="navbar">
        <ul style="margin-left:130px">
            <li><a href="addOfficer.php"><b>Add Officer</b></a></li>
            <li><a href="searchOff.php" ><b>Search Records</b></a></li>
            <li><a href="weapon.php" class="active"><b>Weapons Assigned</b></a></li>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="../admin_users.php"><b>Admin Panel</b></a></li>
            <?php endif; ?>
        </ul>
        <br>
        <br>
        <table border="5" style="position:absolute; left:150px;top:200px;">
        <tr>
            <th>Officer ID</th>
            <th>Officer Name</th>
            <th>Weapon</th>
            <th>Role</th>
        </tr>
        
    </div>
    </div>
    </body>
    </html>
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
        
        // Allow access to users with role 'admin' to view officer list
        if($_SESSION['user_role'] !== 'admin') {
            header("location: ../index.php");
            exit();
        }
        
        include("../config.php");
        $q1 = "SELECT * FROM `officer`";
        $result = mysqli_query($db,$q1);
        if($result){
            while($row=mysqli_fetch_array($result)){
                echo'
                
        <tr>
                
                <td>'. htmlspecialchars($row['offID'], ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row['offName'], ENT_QUOTES, 'UTF-8').'</td>
                <td>'. htmlspecialchars($row['weapon'], ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8').'</td>
        </tr>';
            }   
        }
        else{
            echo"<tr><td colspan='4'>Error retrieving data</td></tr>";
        }
     ?>
