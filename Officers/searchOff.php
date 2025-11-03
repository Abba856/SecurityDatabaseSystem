<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Security Database System- Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../style_1.css'>
    <script src='main.js'></script>
</head>
<body>
    <button name="logout" style="margin-left: 1300px;"><img src="../logout.png" style="width:10px"><a href = "../logout.php">Log out</a></button>
    <div class="container" style="height:800px;">
        <div class="finaldiv">
        <span class="head1"><img src="../police_logo.png" width="16.2%"></span>
        <span class="head_txt">Security Database System</span>
        <span class="head2"><img src="../police_logo.png" width="38%"></span>
    
    <br>
    <div class="navbar">
        <ul style="margin-left:135px">
            <li><a href="addOfficer.php"><b>Add Officer</b></a></li>
            <li><a href="searchOff.php" class="active"><b>Search</b></a></li>
            <li><a href="weapon.php"><b>Weapons Assigned</b></a></li>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="../users.php"><b>Admin Panel</b></a></li>
            <?php endif; ?>
        </ul>
    </div>
        <span class="searchGroup">
            <form method="post">
            <input type="text" class="searchBar" placeholder="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search officer's By Name" name="search">
            <button class="searchBtn"><img src="../search.png" width="50%"></button>
            <img src="police_logo_1.png" style="margin-left:-90px;margin-top: 85px;">
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
        <table border="5" style="position:absolute; left:-50px;top:200px;">
        <tr>
            <th>Officer Name</th>
            <th>Officer ID</th>
            <th>Assigned case ID</th>
            <th>Contact</th>
            <th>Gender</th>
            <th>Weapon</th>
            <th>Role</th>
        </tr>';
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

            }
            }
            else{
                echo"";
            }
    ?>
    </table></div>
        </span>
    </div>
    </div>
    </body>
    </html>
    