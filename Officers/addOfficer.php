<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Database System - Add Officer</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../new_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script type="text/javascript">
        function submitBtn(){
            var contact = document.forms["offInfo"]["contact"].value;
            if(contact.length==10 && !isNaN(contact) && contact.substr(0,1)>='6'){
                return true;
            }
            else{
                alert('Please enter a valid 10-digit mobile number');
                return false;
            }
        }
    </script>
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
                <li><a href="addOfficer.php" class="active"><i class="fas fa-user-plus"></i> Add Officer</a></li>
                <li><a href="searchOff.php"><i class="fas fa-search"></i> Search</a></li>
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
                <h2 class="card-title"><i class="fas fa-user-tie"></i> Add New Officer</h2>
                <p>Enter officer details to add to the system</p>
                
                <form id="offInfo" method="post" onsubmit="return submitBtn()" class="mt-20">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="offName" class="form-label"><i class="fas fa-user"></i> Officer's Name</label>
                                <input type="text" class="form-control" name="offName" id="offName" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="offID" class="form-label"><i class="fas fa-id-card"></i> Officer ID</label>
                                <input type="text" class="form-control" name="offID" id="offID" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="ID" class="form-label"><i class="fas fa-fingerprint"></i> Assigned Case ID</label>
                                <input type="text" class="form-control" name="ID" id="ID" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact" class="form-label"><i class="fas fa-phone"></i> Contact</label>
                                <input type="text" class="form-control" name="contact" id="contact" required>
                            </div>
                        </div>
                        
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                                <div class="mt-10">
                                    <label class="form-label"><input type="radio" name="gender" value="M" required> Male</label>
                                    <label class="form-label ml-10"><input type="radio" name="gender" value="F" required> Female</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="weapon" class="form-label"><i class="fas fa-gun"></i> Weapon Assigned</label>
                                <select name="weapon" id="weapon" class="form-control" required>
                                    <option value="">--Select weapon assigned to officer--</option>
                                    <option value="M4">M4</option>
                                    <option value="M107">M107</option>
                                    <option value="Smith and Wesson M&P">Smith and Wesson M&P</option>
                                    <option value="Glock Pistol">Glock Pistol</option>
                                    <option value="Pistol Auto 9mm 1A">Pistol Auto 9mm 1A</option>
                                    <option value="MP5 German Automatic Sub-machine Gun">MP5 German Automatic Sub-machine Gun</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="role" class="form-label"><i class="fas fa-briefcase"></i> Select Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="">--Select role of officer--</option>
                                    <option value="Sr.PI">Sr.PI(Senior Police Inspector)</option>
                                    <option value="API">API(Asst. Police Inspector)</option>
                                    <option value="PSI">PSI(Police Sub-Inspector)</option>
                                    <option value="HC">HC(Head Constable)</option>
                                    <option value="C">Constable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-20">
                        <button type="submit" class="btn"><i class="fas fa-plus"></i> Add Officer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    session_start();
    // Check if user is logged in and has appropriate role
    if(!isset($_SESSION['login_user']) || !isset($_SESSION['user_role'])){
        header("location: ../index.php");
        exit();
    }
    
    // Only allow admin users to add officers
    if($_SESSION['user_role'] !== 'admin') {
        header("location: ../index.php");
        exit();
    }
    
    include("../config.php");
    
    if($_SERVER['REQUEST_METHOD']=='POST')
    {
        $offName = htmlspecialchars($_POST['offName'], ENT_QUOTES, 'UTF-8');
        $offID = (int)$_POST['offID'];
        $ID = (int)$_POST['ID'];
        $contact = preg_replace('/[^0-9]/', '', $_POST['contact']); // Only allow numbers in contact
        $gender = $_POST['gender'];
        $weapon = htmlspecialchars($_POST['weapon'], ENT_QUOTES, 'UTF-8');
        $role = htmlspecialchars($_POST['role'], ENT_QUOTES, 'UTF-8');
        
        // Validate contact number length
        if(strlen($contact) != 10) {
            echo "<script>alert('Error: Contact number must be 10 digits.');</script>";
        } else {
            // Prepared statement to prevent SQL injection
            $q1 = "INSERT INTO `officer`(`offName`, `offID`, `ID`, `contact`, `gender`, `weapon`, `role`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $q1);
            mysqli_stmt_bind_param($stmt, "siissss", $offName, $offID, $ID, $contact, $gender, $weapon, $role);
            
            if(mysqli_stmt_execute($stmt))
            {
                echo "<script>alert('Officer added successfully!');</script>";
            }
            else{
               echo "<script>alert('Error: " . mysqli_error($db) . "');</script>";
            }
            mysqli_stmt_close($stmt);
        }
    }
    ?>
</body>
</html>