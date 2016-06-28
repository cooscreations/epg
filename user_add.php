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
    while($row_get_user = mysqli_fetch_array($result_get_user)) {
        $user_ID = $row_get_user['ID'];
        $user_fn = $row_get_user['first_name'];
        $user_mn = $row_get_user['middle_name'];
        $user_ln = $row_get_user['last_name'];
        $user_name_cn = $row_get_user['name_CN'];
        $user_email = $row_get_user['email'];
        $user_pwd = $row_get_user['password'];
        $user_level = $row_get_user['user_level'];
        $user_position = $row_get_user['position'];
        $user_last_login_date = $row_get_user['last_login_date'];	
        
    } // end get part info WHILE loop
}

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Add A New User<?php if ($record_id != 0) { ?> <?php echo $user_fn; ?> <?php echo $user_mn; ?> <?php echo $user_ln; ?> / <?php echo $user_name_cn;
                                } ?></h2>

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
							<label class="col-md-3 control-label">Mobile Number:<span class="required">*</span></label>
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-phone"></i>
									</span>
									<input id="inputDefault" name="mobile_number" data-plugin-masked-input data-input-mask="(999) 999-9999" placeholder="(123) 123-1234" class="form-control" required />
								</div>
							</div>
						</div>
					 
                        <div class="form-group">
                            <label class="col-md-3 control-label">Level:<span class="required">*</span></label>
                            <div class="col-md-5">
                              <!--   <input type="number" class="form-control" id="inputDefault" name="level_text" min="10" max="100" required />  -->
                            <select data-plugin-selectTwo class="form-control populate" name="level_text" required>
													
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
                            <label class="col-md-3 control-label">Position:<span class="required">*</span></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="pos_text" required />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <footer class="panel-footer">
                        <?php 
										if (isset($_REQUEST['id'])) {
											?>
                        <input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="user_id" />
                        <?php
										}
										?>
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