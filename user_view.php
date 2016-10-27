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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 31;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else { // no id = nothing to see here!
	header("Location: users.php?msg=NG&action=view&error=no_id");
	exit();
}

// pull the header and template stuff:
pagehead($page_id);

// now get the user info:
$get_user_SQL = "SELECT * FROM `users` WHERE `ID` = " . $record_id;
$result_get_user = mysqli_query($con,$get_user_SQL);
// while loop
while($row_get_user = mysqli_fetch_array($result_get_user)) {
	$user_ID = $row_get_user['ID'];
	$user_fn = $row_get_user['first_name'];
	$user_mn = $row_get_user['middle_name'];
	$user_ln = $row_get_user['last_name'];
	$user_name_cn = $row_get_user['name_CN'];
	$user_email = $row_get_user['email'];
	//$user_pwd = _base64_decrypt($row_get_user['password']); May not need this. Why would we display the password plain text ?
	$user_level = $row_get_user['user_level'];
	$user_position = $row_get_user['position'];
	$user_last_login_date = $row_get_user['last_login_date'];
	$user_facebook = $row_get_user['facebook_profile'];
	$user_linkedin = $row_get_user['linkedin_profile'];
	$user_twitter = $row_get_user['twitter_profile'];
	$user_wechat = $row_get_user['wechat_profile'];
	$user_skype = $row_get_user['skype_profile'];
	
	// now get the last update time:
	
	$get_last_update_SQL = "SELECT * FROM `update_log` WHERE `user_ID` = '" . $user_ID . "' ORDER BY `update_date` DESC LIMIT 0,1";
	$result_get_last_update = mysqli_query($con,$get_last_update_SQL);
	// while loop
	while($row_get_last_update = mysqli_fetch_array($result_get_last_update)) {
		$last_update_ID 		= $row_get_last_update['ID'];
		$last_update_table_name = $row_get_last_update['table_name'];
		$last_update_update_ID 	= $row_get_last_update['update_ID'];
		$last_update_ID 		= $row_get_last_update['user_ID'];					// same as record ID!
		$last_update_notes 		= $row_get_last_update['notes'];
		$last_update_date 		= $row_get_last_update['update_date'];
		$last_update_type 		= $row_get_last_update['update_type'];
		$last_update_action 	= $row_get_last_update['update_action'];
		
	} // end of get last update

} // end get user info WHILE loop
?>

<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>User Profile - <?php echo $user_fn; ?> <?php echo $user_mn; ?> <?php echo $user_ln; 
        	
        	if (($user_name_cn!='')&&($user_name_cn!='中文名')){
        		echo ' / ' . $user_name_cn; 
        	}	
        	?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="users.php">All Users</a></li>
                <li><span>User Profile</span></li>
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

    <div class="row">
        <div class="col-md-12">
            <!-- User JUMPER -->
            <select onchange="document.location = this.value" data-plugin-selecttwo class="form-control populate">
                <option value="#" selected="selected">JUMP TO ANOTHER USER / 看别的用户:</option>
                <option value="users.php">View All / 看全部</option>
                <?php

							$get_j_users_SQL = "SELECT * FROM `users`";
					  		// echo $get_j_users_SQL;

					  		$result_get_j_users = mysqli_query($con,$get_j_users_SQL);
					  		// while loop
					  		while($row_get_j_users = mysqli_fetch_array($result_get_j_users)) {

								$j_user_ID = $row_get_j_users['ID'];
								$j_user_fn = $row_get_j_users['first_name'];
								$j_user_mn = $row_get_j_users['middle_name'];
								$j_user_ln = $row_get_j_users['last_name'];
								$j_user_name_cn = $row_get_j_users['name_CN'];
								$j_user_email = $row_get_j_users['email'];
								$j_user_pwd = $row_get_j_users['password'];
								$j_user_level = $row_get_j_users['user_level'];
								$j_user_position = $row_get_j_users['position'];
								$j_user_last_login_date = $row_get_j_users['last_login_date'];

							   ?>
                <option value="user_view.php?id=<?php echo $j_user_ID; ?>">
                	<?php echo $j_user_fn . " ";
                	if ($j_user_mn!='') {
                		echo $j_user_mn . " ";
                	}
                	echo $j_user_ln . " ";?> <?php if (($j_user_name_cn != '')&&($j_user_name_cn != '中文名')) { echo " / " . $j_user_name_cn; } ?></option>
                <?php
							  } // end get user list
							  ?>
                <option value="users.php">View All / 看全部</option>
            </select>
            <!-- / USER JUMPER -->
        </div>
    </div>


    <div class="clearfix">&nbsp;</div>


    <!-- START MAIN BODY COLUMN: -->
    <div class="col-md-12">

        <div class="row">
            <header class="panel-heading">
                <h2 class="panel-title">User Details:</h2>
            </header>
            <div class="col-md-4 col-lg-3">

                <section class="panel">
					<div class="panel-body">
					
					
					
						<div class="thumb-info mb-md">
							
							<?php get_img('users', $user_ID, 1, 250); ?>
							<div class="thumb-info-title">
								<span class="thumb-info-inner"><?php echo $user_fn;  if (($user_name_cn!='中文名')&&($user_name_cn!='')) { echo ' / ' . $user_name_cn; } ?></span>
								<span class="thumb-info-type"><?php echo $user_position; ?></span>
							</div>
						</div>

						<hr class="dotted short" />

						<div class="social-icons-list">
						<?php if ($user_facebook != '') { ?>
							<a rel="tooltip" data-placement="bottom" target="_blank" href="<?php echo $user_facebook; ?>" data-original-title="Facebook"><i class="fa fa-facebook"></i><span>Facebook</span></a>
						<?php }

						if ($user_twitter != '') { ?>
							<a rel="tooltip" data-placement="bottom" href="<?php echo $user_twitter; ?>" data-original-title="Twitter"><i class="fa fa-twitter"></i><span>Twitter</span></a>
						<?php }

						if ($user_linkedin != '') { ?>
							<a rel="tooltip" data-placement="bottom" href="<?php echo $user_linkedin; ?>" data-original-title="LinkedIn"><i class="fa fa-linkedin"></i><span>Linkedin</span></a>
						<?php }

						if ($user_skype != '') { ?>
							<a rel="tooltip" data-placement="bottom" href="<?php echo $user_skype; ?>" data-original-title="Skype: <?php echo $user_skype; ?>"><i class="fa fa-skype"></i><span>Skype</span></a>
						<?php }

						if ($user_wechat != '') { ?>
							<a rel="tooltip" data-placement="bottom" href="<?php echo $user_wechat; ?>" data-original-title="WeChat: <?php echo $user_wechat; ?>"><i class="fa fa-wechat"></i><span>WeChat/ 微信</span></a>
						<?php } ?>
						</div>
						
						<hr class="dotted short" />
						
						<?php
							admin_bar('user');
						?>

					</div>
				</section>

            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-condensed mb-none">
                        <tr>
                            <th class="text-right">First Name:</th>
                            <td><?php echo $user_fn; ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">Middle Name:</th>
                            <td><?php echo $user_mn; ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">Last Name:</th>
                            <td><?php echo $user_ln; ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">名字:</th>
                            <td><?php if (($user_name_cn!='中文名')&&($user_name_cn!='')) { echo $user_name_cn; } else { echo "NOT SET"; } ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">E-mail:</th>
                            <td><a href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a></td>
                        </tr>
                        <tr>
                            <th class="text-right">Level:</th>
                            <td><?php echo $user_level; ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">Position:</th>
                            <td><?php echo $user_position; ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">Last Log In:</th>
                            <td><?php
                            if ($user_last_login_date != '0000-00-00 00:00:00') {
                            	echo $user_last_login_date;
                            	
                            	
                            	$now = time(); // or your date as well
								$your_date = strtotime($user_last_login_date);
								$datediff = $now - $your_date;
								$datediffdays = floor($datediff / (60 * 60 * 24));

								echo ' <span class="text-muted">(' . $datediffdays . ' days ago)</span>';
                            	
                            }
                            else {
                            	echo '<span class="text-danger">NEVER</span>';
                            }
                            ?></td>
                        </tr>
                        <tr>
                            <th class="text-right">Last Action:</th>
                            <td><?php
                            if (($last_update_date != '0000-00-00 00:00:00')&&($last_update_date != '')) {
                            	echo $last_update_date;
                            	
                            	
                            	$now_1 = time(); // or your date as well
								$your_date_1 = strtotime($last_update_date);
								$datediff_1 = $now_1 - $your_date_1;
								$datediffdays_1 = floor($datediff_1 / (60 * 60 * 24));

								echo ' <span class="text-muted">(' . $datediffdays_1 . ' days ago)</span>';
                            	
                            	
                            }
                            else {
                            	echo '<span class="text-danger">NEVER</span>';
                            }
                            ?></td>
                        </tr>
                        <tr>
                          <th class="text-right"># Updates This Year</th>
                          <td>
                          		<?php
								// count variants for this purchase order
								$count_updates_sql = "SELECT COUNT( ID ) FROM  `update_log` WHERE  `user_ID` = " . $record_id;
								$count_updates_query = mysqli_query($con, $count_updates_sql);
								$count_updates_row = mysqli_fetch_row($count_updates_query);
								$total_updates = $count_updates_row[0];
                          		echo number_format($total_updates);
                          		?>

                          		 <a href="update_log.php?user_id=<?php echo $record_id; ?>">(View All)</a>
                          </td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>

        <div class="clearfix">&nbsp;</div>
        <!-- END OF user PROFILE (numbered tab) -->
    </div>



    <!-- END OF LOOP ITERATION -->

    <!-- close TAB CONTENT -->



    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
