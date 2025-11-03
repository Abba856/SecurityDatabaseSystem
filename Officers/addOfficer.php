<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Security Database System- Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../style_1.css'>
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
    ?>
    <button name="logout" style="margin-left: 1300px;"><img src="../logout.png" style="width:10px"><a href = "../logout.php">Log out</a></button>
    <div class="container">
        <div class="finaldiv">
        <span class="head1"><img src="police_logo.png" width="16.2%"></span>
        <span class="head_txt">Security Database System</span>
        <span class="head2"><img src="police_logo.png" width="38%"></span>
    
    <br>
    <div class="navbar">
        <ul style="margin-left:135px">
            <li><a href="addOfficer.php" class="active"><b>Add Officer</b></a></li>
            <li><a href="searchOff.php" ><b>Search</b></a></li>
            <li><a href="weapon.php"><b>Weapons Assigned</b></a></li>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="../users.php"><b>Users</b></a></li>
            <?php endif; ?>
        </ul>
    </div>
        <div id="crimeInfo" style="background-image: url('police_logo_1.png'); background-repeat:no-repeat;margin-top: 50px; background-size: 60%;">
            <form id="offInfo" method="post" onsubmit="return submitBtn()">
                <table>
                    <tr>
                        <td>
                            Officers Name
                        </td>
                        <td>
                            <input type="text" name="offName" required>
                        </td>
                    </tr>
                    <tr>
                        <td><br></td>
                    </tr>
                    <tr>
                        <td>
                            Officers ID
                        </td>
                        <td>
                            <input type="text" name="offID" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Assigned Case(ID)
                        </td>
                        <td>
                            <input type="text" name="ID" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Contact
                        </td>
                        <td>
                            <input type="text" name="contact" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Gender
                        </td>
                        <td>
                            <input type="radio" name="gender" value="M" required>Male &nbsp;&nbsp;<input type="radio" name="gender" value="F" required>Female
                        </td>
                        <tr>
                                                    <td>
                            <br>
                        </td>
                        </tr>
                        <tr>
                        <td>
                            Weapon Assigned
                        </td>
 
                        <td>
                        <select name="weapon" required>
                                <option value="">--Select weapon assigned to officer--</option>
                                <option value="M4">M4</option>
                                <option value="M107">M107</option>
                                <option value="Smith and Wesson M&P">Smith and Wesson M&P</option>
                                <option value="Glock Pistol">Glock Pistol</option>
                                <option value="Pistol Auto 9mm 1A">Pistol Auto 9mm 1A</option>
                                <option value="MP5 German Automatic Sub-machine Gun">MP5 German Automatic Sub-machine Gun</option>
                            </select>                                                   
                        </td>
                    </tr>
                    <tr>
                                                    <td>
                            <br>
                        </td>
                        </tr>
                    <tr>
                        <td>
                            Select Role
                        </td>
                        <td>
                                
                            <select name="role" required>
                                <option value="">--Select role of officer--</option>
                                <option value="Sr.PI">Sr.PI(Senior Police Inspector)</option>
                                <option value="API">API(Asst. Police Inspector)</option>
                                <option value="PSI">PSI(Police Sub-Inspector)</option>
                                <option value="HC">HC(Head Constable)</option>
                                <option value="C">Constable</option>
                            </select>        
                        </td>
                    </tr>
                    </tr>
                    <tr>
                                               <td>
                            <br>
                        </td>
                    </tr>
                    
                </table>
                <button type="submit" class="submitBtn1"><b>Add Officer</b></button>
                
            </form>
        </div>
    </div>
    </div>

    </body>
    </html>

    <?php 
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