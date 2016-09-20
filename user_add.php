<meta content="text/html; charset=utf-8" http-equiv="content-type" /><?php
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

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}


if ($record_id != 0) {
    $get_user_SQL = "SELECT * FROM `users` WHERE `ID` = " . $record_id;
	$result_get_user = mysqli_query($con,$get_user_SQL);

    // while loop
    while($row_get_users = mysqli_fetch_array($result_get_user)) {
		$user_ID 				= $row_get_users['ID'];					// N/A
		$user_first_name 		= $row_get_users['first_name'];			//
		$user_middle_name 		= $row_get_users['middle_name'];		//
		$user_last_name 		= $row_get_users['last_name'];			//
		$user_name_CN 			= $row_get_users['name_CN'];			//
		$user_email 			= $row_get_users['email'];				//	
		$user_password 			= $row_get_users['password'];			//
		$user_level 			= $row_get_users['user_level'];			//
		$user_position 			= $row_get_users['position'];			//	
		$user_last_login_date 	= $row_get_users['last_login_date'];	// N/A
		$user_facebook_profile 	= $row_get_users['facebook_profile'];	//
		$user_twitter_profile 	= $row_get_users['twitter_profile'];	//
		$user_linkedin_profile 	= $row_get_users['linkedin_profile'];	//
		$user_skype_profile 	= $row_get_users['skype_profile'];		//
		$user_wechat_profile 	= $row_get_users['wechat_profile'];		//
		$user_record_status 	= $row_get_users['record_status'];		// N/A
		$user_mobile_number 	= $row_get_users['mobile_number'];		// 

    } // end get part info WHILE loop
}

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Add A New User</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="users.php">All Users</a></li>
                <li><span>Add New User</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form id="form" class="form-horizontal form-bordered" action="user_add_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Add User Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">First Name:<span class="required">*</span></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control"  name="fn_text" required />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Middle Name:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="mn_text" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Last Name:<span class="required">*</span></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="ln_text" required />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">名字:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="cn_text" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">E-mail:<span class="required">*</span></label>
                            <div class="col-md-5">
                                <input type="email" class="form-control" name="email_text" required />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Password:<span class="required">*</span></label>
                            <div class="col-md-5">
                                <input type="password" class="form-control" name="pwd_text" required />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

											<div class="form-group">
												<label class="col-md-3 control-label">Mobile Number:</label>
												<div class="col-md-5">
													<div class="input-group">
														<input id="phone_number" type="tel"  class="form-control" placeholder="+8612312312312" />
														<input id="mobile_number" type="hidden" name="mobile_number"/>
													</div>

													<label id="error-msg" class="hide">Invalid number</label>
												</div>

											</div>
											<script src="assets/vendor/intl-tel-input/js/intlTelInput.min.js"></script>
											<script type="text/javascript">
													var telInput = $("#phone_number"),
													errorMsg = $("#error-msg"),
													validMsg = $("#valid-msg");

													// initialise plugin
													telInput.intlTelInput({
													nationalMode: true,
													utilsScript: "assets/vendor/intl-tel-input/js/utils.js"
													});

													var reset = function() {
													telInput.closest('.form-group').removeClass("has-error");
													errorMsg.addClass("hide");
													validMsg.addClass("hide");
													};

													var output = $("#mobile_number");

													// listen to "keyup", but also "change" to update when the user selects a country
													telInput.on("keyup change", function() {
													  var intlNumber = telInput.intlTelInput("getNumber");
													    output.val(intlNumber);
													});

													// on blur: validate
													telInput.blur(function() {
													reset();
													if ($.trim(telInput.val())) {
													 if (telInput.intlTelInput("isValidNumber")) {
														 validMsg.removeClass("hide");
													 } else {
														 telInput.closest('.form-group').addClass("has-error");
														 errorMsg.removeClass("hide");
														 errorMsg.addClass("error");
													 }
													}
													});

													// on keyup / change flag: reset
													telInput.on("keyup change", reset);
											</script>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Level:</label>
                            <div class="col-md-5">
                            	<select data-plugin-selectTwo class="form-control populate" name="level_text">
									<option value="10">10</option>
									<option value="20">20</option>
									<option value="30">30</option>
									<option value="40">40</option>
									<option value="50">50</option>
									<option value="60">60</option>
									<option value="70">70</option>
									<option value="80">80</option>
								</select>
                            </div>


                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Position:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="pos_text" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        
                        <!-- SOCIAL INFO (optional) -->
                        
                        

                        <div class="form-group">
                            <label class="col-md-3 control-label">Facebook:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="facebook" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        

                        <div class="form-group">
                            <label class="col-md-3 control-label">Twitter:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="twitter" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        

                        <div class="form-group">
                            <label class="col-md-3 control-label">LinkedIn:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="linkedin" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        

                        <div class="form-group">
                            <label class="col-md-3 control-label">WeChat:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="wechat" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        

                        <div class="form-group">
                            <label class="col-md-3 control-label">Skype:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="skype" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                        
                        
                        
                    </div>
                    <footer class="panel-footer">
                        <button type="submit" class="btn btn-success">Submit </button>
                        <button type="reset" class="btn btn-default">Reset</button>
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
