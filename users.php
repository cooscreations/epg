<?php 
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
/*//////*/      session_start();        /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//  now check the user is OK to view this page  //
/*//////// require ('page_access.php'); /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php'); 
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 5;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Users</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span>Users</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <?php 
    
    // run notifications function:
    $msg = 0;
    if (isset($_REQUEST['msg'])) { $msg = $_REQUEST['msg']; }
    $action = 0;
    if (isset($_REQUEST['action'])) { $action = $_REQUEST['action']; }
    $change_record_id = 0;
    if (isset($_REQUEST['new_record_id'])) { $change_record_id = $_REQUEST['new_record_id']; }
    $page_record_id = 0;
    if (isset($record_id)) { $page_record_id = $record_id; }
    
    // now run the function:
    notify_me($page_id, $msg, $action, $change_record_id, $page_record_id);
    ?>
    <!-- start: page -->

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-condensed mb-none">
            <tr>
                <th>Name</th>
                <th>名字</th>
                <th>E-mail</th>
                <th>Position</th>
                <th>Last Log In</th>
                <th>Level</th>
                <th>Photo</th>
                <th class="text-center">Actions</th>
            </tr>

            <?php 
					  $get_users_SQL = "SELECT * FROM  `users`";
					  // echo $get_ratings_SQL;
					  
					  $user_count = 0;
	
					  $result_get_users = mysqli_query($con,$get_users_SQL);
					  // while loop
					  while($row_get_users = mysqli_fetch_array($result_get_users)) {
					  
					  ?>

            <tr>
                <td><a href="user_view.php?id=<?php echo $row_get_users['ID']; ?>"><?php echo $row_get_users['first_name']; echo ' ' . $row_get_users['middle_name']; echo ' ' . $row_get_users['last_name']; ?></a></td>
                <td><a href="user_view.php?id=<?php echo $row_get_users['ID']; ?>"><?php 
                if (($row_get_users['name_CN']!='中文名')&&($row_get_users['name_CN']!='')) {
                	echo $row_get_users['name_CN']; 
                }
                ?></a></td>
                <td><a href="mailto:<?php echo $row_get_users['email']; ?>" title="Click to send an email"><?php echo $row_get_users['email']; ?></a></td>
                <td><?php echo $row_get_users['position']; ?></td>
                <td><?php 
                if ($row_get_users['last_login_date']!='0000-00-00 00:00:00') {
                	echo $row_get_users['last_login_date']; 
                }
                else {
                	echo '<span class="text-danger">NEVER</span>';
                }
                ?></td>
                <td><?php echo $row_get_users['user_level']; ?></td>
                <td><a href="user_view.php?id=<?php echo $row_get_users['ID']; ?>">
                    <img src="assets/images/users/user_<?php echo $row_get_users['ID']; ?>.png" title="<?php echo $row_get_users['first_name']; echo ' ' . $row_get_users['middle_name']; echo ' ' . $row_get_users['last_name']; ?>" style="width:100px;" /></a></td>
                <td class="text-center">
                    <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                    <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                    <a href="user_edit.php?id=<?php echo $row_get_users['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
					<a href="record_delete_do.php?table_name=users&src_page=users.php&id=<?php echo $row_get_users['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>

            <?php 
					  
					  $user_count = $user_count + 1;
					  
					  } // end while loop
					  ?>

            <tr>
                <th colspan="7">TOTAL: <?php echo $user_count; ?></th>
                <th class="text-center"><a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a></th>
            </tr>


        </table>
    </div>
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>