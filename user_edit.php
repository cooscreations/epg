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


$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: users.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	$get_user_SQL = "SELECT * FROM `users` WHERE `ID` = " . $record_id;
	// echo $get_user_SQL;
	$result_get_user = mysqli_query($con,$get_user_SQL);

    // while loop
    while($row_get_user = mysqli_fetch_array($result_get_user)) {
        
		$user_ID 				= $row_get_user['ID'];					// N/A
		$user_fn 				= $row_get_user['first_name'];			//
		$user_mn 				= $row_get_user['middle_name'];			//
		$user_ln 				= $row_get_user['last_name'];			//
		$user_cn 				= $row_get_user['name_CN'];				//
		$user_email 			= $row_get_user['email'];				//	
		$user_pwd 				= $row_get_user['password'];			//
		// $user_pwd	 			= _base64_decrypt($row_get_user['password']);
		$user_level 			= $row_get_user['user_level'];			//
		$user_pos	 			= $row_get_user['position'];			//	
		$user_last_login_date 	= $row_get_user['last_login_date'];		// N/A
		$user_facebook_profile 	= $row_get_user['facebook_profile'];	//
		$user_twitter_profile 	= $row_get_user['twitter_profile'];		//
		$user_linkedin_profile 	= $row_get_user['linkedin_profile'];	//
		$user_skype_profile 	= $row_get_user['skype_profile'];		//
		$user_wechat_profile 	= $row_get_user['wechat_profile'];		//
		$user_record_status 	= $row_get_user['record_status'];		// N/A
		$user_mobile_number 	= $row_get_user['mobile_number'];		// 
        
        
        
        
    } // end get user info WHILE loop
}

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit User - <?php echo $user_fn; ?> <?php echo $user_mn; ?> <?php echo $user_ln; ?> / <?php echo $user_name_cn; ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="users.php">All Users</a></li>
                <li><span>Edit User</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="user_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit User Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="col-md-4 col-lg-3">
                            <section class="panel">
                                <div class="panel-body">
                                    <div class="thumb-info mb-md">
                                        <img src="assets/images/users/user_<?php echo $user_ID; ?>.png" title="<?php echo $user_fn; echo ' ' . $user_mn; echo ' ' . $user_ln; ?>" style="width:100px;" />
                                    </div>
                                </div>
                            </section>
                        </div>
                        
                        
                        <!-- <div class="upload_div text-center"><a href="add_file.php?file_type=user&ID=<?php echo $user_ID; ?>&history=new" onclick="NewWindow(this.href,'Upload a File','600','400','no','center');return false" onfocus="this.blur()" title="Click to upload a new image"><i class="fa fa-cloud-upload"></i><i class="fa fa-file-photo-o"></i></a></div>
                             --> 
                        
                        <div class="col-md-4 col-lg-9">
                            <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">First Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="fn_text" value="<?php echo $user_fn; ?>"/>
                                    <input type="hidden" name="user_id" value="<?php echo $user_ID; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Middle Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="mn_text" value="<?php echo $user_mn; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Last Name:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="ln_text" value="<?php echo $user_ln; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">名字:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="cn_text" value="<?php echo $user_cn; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">E-mail:</label>
                                <div class="col-md-5">
                                    <input type="email" class="form-control" id="inputDefault" name="email_text" value="<?php echo $user_email; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label text-danger">New Password:</label>
                                <div class="col-md-5">
                                    <input type="password" class="form-control" id="inputDefault" name="pwd_text" value="" />
                                    <input type="hidden" value="<?php echo $user_pwd; ?>" name="existing_password" id="existing_password" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
								<label class="col-md-3 control-label">Mobile Number:<span class="required">*</span></label>
								<div class="col-md-5">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-phone"></i>
										</span>
										<input id="inputDefault" name="mobile_number" value="<?php echo $user_mobile_number; ?>" class="form-control" required />
									</div>
								</div>
							</div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Level:</label>
                                <div class="col-md-5">
                                    <input type="number" class="form-control" id="inputDefault" name="level_text" min="10" max="100" step="10" value="<?php echo $user_level; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Position:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="pos_text" value="<?php echo $user_pos; ?>"/>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-group">
								<label class="col-md-3 control-label">Record Status:</label>
								<div class="col-md-5">
									<?php echo record_status_drop_down($user_record_status); ?>
								</div>
							
								<div class="col-md-1">
									&nbsp;
								</div>
							</div>
                            
                            
                                </div>
                        </div>

                    </div>
                    
                    <footer class="panel-footer">
                    
                    <div class="row">
                    
						<!-- ADD ANY OTHER HIDDEN VARS HERE -->
					  <div class="col-md-5 text-left">	
						<?php form_buttons('user_view', $record_id); ?>
					  </div>
					  
					  
					   <!-- NEXT STEP SELECTION -->
							
							<?php 
							if ($_REQUEST['next_step'] == 'add') {
								$next_step_selected = 'add';
							}
							else {
								$next_step_selected = 'view';
							}
							?>
							
							<label class="col-md-1 control-label text-right">...and then...</label>
							
							<div class="col-md-6 text-left">
								<div class="radio-custom radio-success">
									<input type="radio" id="next_step" name="next_step" value="view_record"<?php if ($next_step_selected == 'view') { ?> checked="checked"<?php } ?>>
									<label for="radioExample9">View User</label>
								</div>

								<div class="radio-custom radio-warning">
									<input type="radio" id="next_step" name="next_step" value="view_list"<?php if ($next_step_selected == 'add') { ?> checked="checked"<?php } ?>>
									<label for="radioExample10">View ALL Users</label>
								</div>
							</div>
							
							<!-- END OF NEXT STEP SELECTION -->
					    </div><!-- END ROW -->
					  
					</footer>
                </section>
                <!-- now close the form -->
            </form>
        </div>
    </div>
    <!-- now close the panel -->
    <!-- end row! -->

    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
