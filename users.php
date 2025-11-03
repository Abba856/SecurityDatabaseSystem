<?php
   include("config.php");
   session_start();
   
   // Regenerate session ID periodically to prevent session fixation
   if (!isset($_SESSION['login_time'])) {
       session_regenerate_id(true);
       $_SESSION['login_time'] = time();
   } elseif (time() - $_SESSION['login_time'] > 3600) { // 1 hour timeout
       session_regenerate_id(true);
       $_SESSION['login_time'] = time();
   }
   
   // Check if user is logged in and is admin
   if(!isset($_SESSION['login_user']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
       header("location: index.php");
       exit();
   }
   
   // Handle user management actions
   if(isset($_POST['add_user'])) {
       $new_username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
       $new_password = $_POST['password'];
       $new_role = $_POST['role'];
       
       // Basic validation
       if(empty($new_username) || empty($new_password) || empty($new_role)) {
           $error_message = "All fields are required.";
       } else {
           // Hash the password
           $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
           
           // Check if username already exists
           $check_sql = "SELECT * FROM users WHERE uname = ?";
           $check_stmt = mysqli_prepare($db, $check_sql);
           mysqli_stmt_bind_param($check_stmt, "s", $new_username);
           mysqli_stmt_execute($check_stmt);
           $check_result = mysqli_stmt_get_result($check_stmt);
           
           if(mysqli_num_rows($check_result) > 0) {
               $error_message = "Username already exists.";
           } else {
               // Insert new user
               $insert_sql = "INSERT INTO users (uname, pass, role) VALUES (?, ?, ?)";
               $insert_stmt = mysqli_prepare($db, $insert_sql);
               mysqli_stmt_bind_param($insert_stmt, "sss", $new_username, $hashed_password, $new_role);
               
               if(mysqli_stmt_execute($insert_stmt)) {
                   $success_message = "User added successfully!";
               } else {
                   $error_message = "Error adding user: " . mysqli_error($db);
               }
               mysqli_stmt_close($insert_stmt);
           }
           mysqli_stmt_close($check_stmt);
       }
   }
   
   // Handle delete user action
   if(isset($_POST['delete_user'])) {
       $delete_username = $_POST['delete_username'];
       if($delete_username !== 'admin') { // Prevent deleting main admin account
           $delete_sql = "DELETE FROM users WHERE uname = ?";
           $delete_stmt = mysqli_prepare($db, $delete_sql);
           mysqli_stmt_bind_param($delete_stmt, "s", $delete_username);
           
           if(mysqli_stmt_execute($delete_stmt)) {
               $success_message = "User deleted successfully!";
           } else {
               $error_message = "Error deleting user: " . mysqli_error($db);
           }
           mysqli_stmt_close($delete_stmt);
       } else {
           $error_message = "Cannot delete the main admin account.";
       }
   }
   
   // Get all users
   $users_sql = "SELECT uname, role FROM users";
   $users_result = mysqli_query($db, $users_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Admin Panel - User Management</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style_1.css'>
</head>
<body>
    <button name="logout" style="margin-left: 1424px;"><img src="logout.png" style="width:10px"><a href = "logout.php">Log out</a></button>
    <div class="container" style="height: auto; min-height: 800px;">
        <div class="finaldiv">
            <span class="head1"><img src="police_logo.png" width="16.2%"></span>
            <span class="head_txt">Security Database System</span>
            <span class="head2"><img src="police_logo.png" width="38%"></span>
            
            <br>
            <div class="navbar">
                <ul style="margin-left:20px">
                    <li><a href="home.php"><b>Information</b></a></li>
                    <li><a href="Officers/addOfficer.php"><b>Officers</b></a></li>
                    <li><a href="admin_users.php" class="active"><b>Users Panel</b></a></li>
                    <li><a href="analysis.php"><b>Analytics</b></a></li>
                    <li><a href="logout.php"><b>Logout</b></a></li>
                </ul>
            </div>
            
            <h2 style="text-align: center; margin: 20px 0;">User Management</h2>
            
            <?php if(isset($error_message)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px; text-align: center;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($success_message)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px; text-align: center;">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div style="max-width: 600px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
                <h3>Add New User</h3>
                <form method="post">
                    <div style="margin: 10px 0;">
                        <label for="username">Username:</label>
                        <input type="text" name="username" required style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin: 10px 0;">
                        <label for="password">Password:</label>
                        <input type="password" name="password" required style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin: 10px 0;">
                        <label for="role">Role:</label>
                        <select name="role" required style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="add_user" class="submitBtn" style="width: 100%;">Add User</button>
                </form>
            </div>
            
            <div style="margin: 30px auto; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
                <h3 style="text-align: center;">Current Users</h3>
                <table style="margin: 0 auto; width: 80%;">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['uname'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if($user['uname'] !== 'admin'): ?>
                                <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_username" value="<?php echo htmlspecialchars($user['uname'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" name="delete_user" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Delete</button>
                                </form>
                                <?php else: ?>
                                <span style="color: #6c757d;">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>