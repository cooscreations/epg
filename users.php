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

$show_users = '2'; // SHOW ACTIVE USERS ONLY BY DEFAULT

if (isset($_REQUEST['show_users'])) {
	$show_users = $_REQUEST['show_users'];
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
    
    <div class="row">
		<div class="col-md-11">
		<!-- USER TYPE JUMPER -->
			<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
			  <option value="#" selected="selected">SELECT USER TYPE / 选择用户类型:</option>
			  <option value="users.php?show_users=all"<?php if ($show_users == 'all'){ ?> selected="selected"<? } ?>>View All / 看全部</option>
			  <option value="users.php?show_users=2"<?php if ($show_users == '2'){ ?> selected="selected"<? } ?>>Approved / Active</option>
			  <option value="users.php?show_users=1"<?php if ($show_users == '1'){ ?> selected="selected"<? } ?>>Pending</option>
			  <option value="users.php?show_users=0"<?php if ($show_users == '0'){ ?> selected="selected"<? } ?>>Deleted</option>
			  <option value="users.php?show_users=all">View All / 看全部</option>
			 </select>
			<!-- / PART TYPE JUMPER -->
		</div>

		<?php add_button(0, 'user_add'); ?>
	</div>
    

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-condensed mb-none">
          <thead>
            <tr>
                <th class="text-center"><i class="fa fa-cog" title="Actions"></i></th>
                <th class="text-center">Photo <i class="fa fa-file-image-o" title="Photo / 照片"></i></th>
                <th class="text-center">Name</th>
                <th class="text-center">名字</th>
                <th class="text-center"><i class="fa fa-envelope-o" title="E-mail / 邮件地址"></i></th>
                <th class="text-center"><i class="fa fa-phone" title="Phone / 电话"></i></th>
                <th class="text-center">Position</th>
                <th class="text-center">Last Log In</th>
                <th class="text-center">Level</th>
                <th class="text-center">Status</th>
                <th class="text-center"># Updates</th>
            </tr>
          </thead>
          <tbody>

            <?php
            
			if ($show_users == '2') {
				// active users only
				$sql_where = " WHERE `record_status` = '2'";
			}
			else if ($show_users == '1') {
				// pending users only
				$sql_where = " WHERE `record_status` = '1'";
			}
			else if ($show_users == '0') {
				// inactive users only
				$sql_where = " WHERE `record_status` = '0'";
			}
			else {
				// ALL
				$sql_where = "";
			}
            
			  $get_users_SQL = "SELECT * FROM `users`" . $sql_where;
			  // echo $get_ratings_SQL;

			  $user_count = 0;

			  $result_get_users = mysqli_query($con,$get_users_SQL);
			  // while loop
			  while($row_get_users = mysqli_fetch_array($result_get_users)) {
			  
					$user_ID 				= $row_get_users['ID'];
					$user_first_name 		= $row_get_users['first_name'];
					$user_middle_name 		= $row_get_users['middle_name'];
					$user_last_name 		= $row_get_users['last_name'];
					$user_name_CN 			= $row_get_users['name_CN'];
					$user_email 			= $row_get_users['email'];
					$user_password 			= $row_get_users['password'];
					$user_level 			= $row_get_users['user_level'];
					$user_position 			= $row_get_users['position'];
					$user_last_login_date 	= $row_get_users['last_login_date'];
					$user_facebook_profile 	= $row_get_users['facebook_profile'];
					$user_twitter_profile 	= $row_get_users['twitter_profile'];
					$user_linkedin_profile 	= $row_get_users['linkedin_profile'];
					$user_skype_profile 	= $row_get_users['skype_profile'];
					$user_wechat_profile 	= $row_get_users['wechat_profile'];
					$user_record_status 	= $row_get_users['record_status'];
					$user_mobile_number 	= $row_get_users['mobile_number'];
									
	
					// count updates:
					$count_updates_sql 		= "SELECT COUNT( * ) FROM `update_log` WHERE `user_ID` = '" . $user_ID . "'";
					$count_updates_query 	= mysqli_query($con, $count_updates_sql);
					$count_updates_row 		= mysqli_fetch_row($count_updates_query);
					$total_updates 			= $count_updates_row[0];
			  ?>

            <tr<?php 
            if ($user_record_status == 0) {
            	?> class="danger"<?php
            }
            else if ($user_record_status == 1) {
            	?> class="warning"<?php
            }
             ?>>
                <td class="text-center">
                    
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->
			
					<?php 
			
					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= 'user_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $user_ID;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'user_edit'; 				// REQUIRED - specify edit page URL
					$add_URL 			= 'user_add'; 				// REQURED - specify add page URL
					$table_name 		= 'users';					// REQUIRED - which table are we updating?
					$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
					$add_VAR 			= ''; 						// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
					?>
	 
						<a class="modal-with-form btn btn-default" href="#modalForm_<?php 
				
							echo $add_to_form_name; 
							echo $form_ID; 
				
						?>"><i class="fa fa-gear"></i></a>

						<!-- Modal Form -->
						<div id="modalForm_<?php 
				
							echo $add_to_form_name; 
							echo $form_ID; 
					
						?>" class="modal-block modal-block-primary mfp-hide">
							<section class="panel">
								<header class="panel-heading">
									<h2 class="panel-title">Admin Options</h2>
								</header>
								<div class="panel-body">
				
									<div class="table-responsive">
									 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
									 <thead>
										<tr>
											<th class="text-left" colspan="2">Action</th>
											<th>Decsription</th>
										</tr>
									  </thead>
									  <tbody>
										<tr>
										  <td>EDIT</td>
										  <td>
											<a href="<?php 
												echo $edit_URL; 
											?>.php?id=<?php 
												echo $form_ID; 
											?>" class="mb-xs mt-xs mr-xs btn btn-warning">
												<i class="fa fa-pencil" stlye="color: #999"></i>
											</a>
										  </td>
										  <td>Edit this record</td>
										</tr>
										<tr>
										  <td>DELETE</td>
										  <td>
											<a href="record_delete_do.php?table_name=<?php 
												echo $table_name; 
											?>&src_page=<?php 
												echo $src_page; 
											?>&id=<?php 
												echo $form_ID;
												echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
											?>" class="mb-xs mt-xs mr-xs btn btn-danger">
												<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
											</a>
										  </td>
										  <td>Delete this record</td>
										</tr>
										<tr>
										  <td>ADD</td>
										  <td>
											<a href="<?php 
												echo $add_URL; 
												echo '.php?' . $add_VAR;  // NOTE THE LEADING '?' <<<
											?>" class="mb-xs mt-xs mr-xs btn btn-success">
												<i class="fa fa-plus" stlye="color: #999"></i>
											</a>
										  </td>
										  <td>Add a similar item to this table</td>
										</tr>
									  </tbody>
									  <tfoot>
										<tr>
										  <td>&nbsp;</td>
										  <td>&nbsp;</td>
										  <td>&nbsp;</td>
										</tr>
									  </tfoot>
									  </table>
									</div><!-- end of responsive table -->	
				
								</div><!-- end panel body -->
								<footer class="panel-footer">
									<div class="row">
										<div class="col-md-12 text-left">
											<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
										</div>
									</div>
								</footer>
							</section>
						</div>
		
					<!-- ********************************************************* -->
					<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
					<!-- ********************************************************* -->
                </td>
                <td class="text-center"><a href="user_view.php?id=<?php echo $user_ID; ?>">
                <?php 
                
                $find_user_image = "assets/images/users/user_" . $user_ID . ".png";
                
                if (file_exists($find_user_image)) {
					?>
					<img src="<?php echo 
						$find_user_image; 
					?>" title="<?php 
						echo $user_first_name;  
						echo ' ' . $user_last_name; 
					?>" style="width:100px;" />
					<?php
				} 
				else {
					get_img('users', $user_ID, 0, 100);
					/*
					?>
					<img src="assets/images/no_image_found.jpg" title="Failed to locate image: <?php echo $find_user_image; ?>" style="width:100px;" />
					<?php
					*/
				}
                
                ?>
                
                    </a></td>
                <td class="text-center">
                  <a href="user_view.php?id=<?php echo $user_ID; ?>"><?php 
                	echo $user_first_name; 
                	echo ' ' . $user_middle_name; 
                	echo ' ' . $user_last_name; 
                ?>
                  </a>
                </td>
                <td class="text-center"><a href="user_view.php?id=<?php echo $user_ID; ?>"><?php
                
				if (($user_name_CN!='中文名')&&$user_name_CN!='') {
                	echo $user_name_CN;
                }
                else { echo '&nbsp;'; }
                
                ?></a></td>
                <td class="text-center"><a href="mailto:<?php echo $user_email; ?>" title="Click to send an email"><?php echo $user_email; ?></a></td>
                <td class="text-center"><a href="tel:<?php echo $user_mobile_number; ?>"><?php echo $user_mobile_number; ?><a/></td>
                <td class="text-center"><?php echo $user_position; ?></td>
                <td class="text-center"><?php
                if ($user_last_login_date!='0000-00-00 00:00:00') {
                	echo $user_last_login_date;
                }
                else {
                	echo '<span class="text-danger">NEVER</span>';
                }
                ?></td>
                <td class="text-center"><?php echo $user_level; ?></td>
                <td class="text-center"><?php record_status($user_record_status); ?></td>
                <td class="text-right"><?php echo number_format($total_updates); ?></td>
            </tr>

            <?php

					  $user_count = $user_count + 1;

					  } // end while loop
					  ?>
		  </tbody>
		  <tfoot>
            <tr>
            	<th colspan="11">TOTAL: <?php echo $user_count; ?></th>
            </tr>
          </tfoot>
        </table>
    </div>
    
    <?php add_button(0, 'user_add'); ?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
