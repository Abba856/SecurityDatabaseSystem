<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Security Database System - Admin Panel</title>
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
            <li><a href="offList.php"><i class="fas fa-users"></i> Officers</a></li>
            <li><a href="analysis.php"><i class="fas fa-chart-bar"></i> Analytics</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-cog"></i> User Panel</a></li>
            <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
         </ul>
      </nav>
   </header>

   <div class="container">
      <div class="main-content">
         <div class="card fade-in-up">
            <h2 class="card-title"><i class="fas fa-users-cog"></i> User Management Panel</h2>
            <p>Manage system users and their access permissions</p>
            
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
            
            <?php if(isset($error_message)): ?>
                <div class="error mb-20 text-center p-15">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($success_message)): ?>
                <div class="success mb-20 text-center p-15">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="card mt-20">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> Add New User</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role" class="form-label"><i class="fas fa-briefcase"></i> Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" name="add_user" class="btn"><i class="fas fa-plus"></i> Add User</button>
                    </div>
                </form>
            </div>
            
            <div class="card mt-20">
                <h3 class="card-title"><i class="fas fa-users"></i> Current Users</h3>
                <div class="table-responsive mt-20">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Username</th>
                                <th><i class="fas fa-briefcase"></i> Role</th>
                                <th><i class="fas fa-cogs"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['uname'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php 
                                        if($user['role'] === 'admin') {
                                            echo '<span class="badge badge-admin"><i class="fas fa-crown"></i> Admin</span>';
                                        } else {
                                            echo '<span class="badge badge-user"><i class="fas fa-user"></i> User</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php if($user['uname'] !== 'admin'): ?>
                                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="delete_username" value="<?php echo htmlspecialchars($user['uname'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-shield-alt"></i> Protected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
         </div>
      </div>
   </div>
   
   <style>
       .badge {
           padding: 5px 10px;
           border-radius: 20px;
           font-size: 0.85rem;
           font-weight: bold;
       }
       
       .badge-admin {
           background: linear-gradient(to right, #e74c3c, #c0392b);
           color: white;
       }
       
       .badge-user {
           background: linear-gradient(to right, #3498db, #2980b9);
           color: white;
       }
       
       .btn-sm {
           padding: 6px 12px;
           font-size: 0.9rem;
       }
   </style>
</body>
</html>