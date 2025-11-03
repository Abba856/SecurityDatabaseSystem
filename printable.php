

 
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
                  
                  // Allow access to users with role 'admin' or 'user' for printing
                  $allowed_roles = array('admin', 'user');
                  if(!in_array($_SESSION['user_role'], $allowed_roles)) {
                      header("location: index.php");
                      exit();
                  }
                  
                  include("config.php");
                      $data=$_SESSION['data'];
                      // Use prepared statement to prevent SQL injection
                      $q1 = "SELECT * FROM info WHERE name = ?";
                      $stmt = mysqli_prepare($db, $q1);
                      mysqli_stmt_bind_param($stmt, "s", $data);
                      mysqli_stmt_execute($stmt);
                      $result = mysqli_stmt_get_result($stmt);
                      
                      if($result && mysqli_num_rows($result) > 0)
                      {
                  echo '<center><table border="5" style="position:relative;top: 65px;display:block" id="table">
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
                         
                      }
                      mysqli_stmt_close($stmt);
                  ?>
                  <script type="text/javascript">window.print();</script>
               </table>
           </center>

