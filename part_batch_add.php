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

$session_user_id = $_SESSION['user_ID'];
// quick hack for Mark to load data faster:
if ($session_user_id == 1) { $session_user_id = 6; } // set Mark to ROBIN as default for Mark whilst entering data...

$page_id = 13;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else if (isset($_REQUEST['PO_ID'])) {
	$record_id = $_REQUEST['PO_ID'];
}

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:

if ($record_id != 0) {
	$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $record_id;
	$result_get_PO = mysqli_query($con,$get_PO_SQL);
	// while loop
	while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

			// now print each record:
			$PO_id = $row_get_PO['ID'];
			$PO_number = $row_get_PO['PO_number'];
			$PO_created_date = $row_get_PO['created_date'];
			$PO_description = $row_get_PO['description'];

			// count variants for this purchase order
	   	 	$count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $record_id . " AND `record_status` = 2";
	   	 	$count_batches_query = mysqli_query($con, $count_batches_sql);
	   	 	$count_batches_row = mysqli_fetch_row($count_batches_query);
	   	 	$total_batches = $count_batches_row[0];

} // end while loop
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Add A New Batch<?php if ($record_id != 0) { ?> to PO# <? echo $PO_number; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="purchase_orders.php">All P.O.s</a>
									</li>
								<?php
								if ($record_id != 0) {
									?>
									<li>
										<a href="purchase_order_view.php?id=<?php echo $record_id; ?>">P.O. Record</a>
									</li>
									<?php
								} ?>
								<li><span>Add New Batch Record</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

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

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form id="form" class="form-horizontal form-bordered"  action="part_batch_add_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Add Batch Record Details:</h2>
								</header>
								<div class="panel-body">

								<div class="form-group">
									<label class="col-md-3 control-label">PO #:<span class="required">*</label>
									<div class="col-md-5">
										<select data-plugin-selectTwo class="form-control populate" name="PO_ID" required>
													<?php
													$get_PO_SQL = "SELECT * FROM `purchase_orders` WHERE `record_status` = 2";
													$result_get_PO = mysqli_query($con,$get_PO_SQL);
													// while loop
													while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

															// now print each record:
															$PO_id = $row_get_PO['ID'];
															$PO_number = $row_get_PO['PO_number'];
															$PO_created_date = $row_get_PO['created_date'];
															$PO_description = $row_get_PO['description'];

															// count variants for this purchase order
													        $count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE `PO_ID` = " . $_REQUEST['id'] . " AND `record_status` = 2";
													        $count_batches_query = mysqli_query($con, $count_batches_sql);
													        $count_batches_row = mysqli_fetch_row($count_batches_query);
													        $total_batches = $count_batches_row[0];

													?>
											<option value="<?php echo $PO_id; ?>"<?php if ($PO_id == $record_id) { ?> selected=""<?php } ?>><?php echo $PO_number; ?></option>

													<?php
													} // END WHILE LOOP
													?>
										</select>
									</div>
									<div class="col-md-1">
										<a href="purchase_order_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>

								<div class="form-group">
												<label class="col-md-3 control-label">Part / Revision #:<span class="required">*</span></label>
												<div class="col-md-5">
													<select data-plugin-selectTwo class="form-control populate" name="part_rev_ID" required>
													<option value=""></option>
													<?php
													// get parts list
													$get_parts_SQL = "SELECT * FROM `parts` WHERE `record_status` = 2 ORDER BY `part_code` ASC";
													// echo $get_parts_SQL;

													$part_count = 0;

													$result_get_parts = mysqli_query($con,$get_parts_SQL);
													// while loop
													while($row_get_parts = mysqli_fetch_array($result_get_parts)) {

														// GET PART TYPE:

														$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $row_get_parts['type_ID'] . "'";
														// echo $get_part_type_SQL;

														$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
														// while loop
														while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
															$part_type_EN = $row_get_part_type['name_EN'];
															$part_type_CN = $row_get_part_type['name_CN'];
														}

														// GET PART CLASSIFICATION:

														$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $row_get_parts['classification_ID'] . "'";
														// echo $get_part_class_SQL;

														$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
														// while loop
														while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
															$part_class_EN = $row_get_part_class['name_EN'];
															$part_class_CN = $row_get_part_class['name_CN'];
														}
													?>

													<optgroup label="<?php echo $row_get_parts['part_code']; ?> - <?php echo $row_get_parts['name_EN']; ?> / <?php echo $row_get_parts['name_CN']; ?>">

													<?php

													// now list the revisions for this part:

													$get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE `part_ID` =" . $row_get_parts['ID'] . " AND `record_status`=2";
													$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);

													// while loop
													while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

														// now print each record:
														$rev_id = $row_get_part_rev['ID'];
														$rev_part_id = $row_get_part_rev['part_ID'];
														$rev_number = $row_get_part_rev['revision_number'];
														$rev_remarks = $row_get_part_rev['remarks'];
														$rev_date = $row_get_part_rev['date_approved'];
														$rev_user = $row_get_part_rev['user_ID'];

													?>
													<option value="<?php echo $rev_id; ?>"><?php echo $row_get_parts['part_code']; ?> - <?php echo $rev_number; ?></option>
													<?php

													} // end revision look-up loop



													?>

													</optgroup>

													<?php
													} // END WHILE LOOP

													?>
													</select>
												</div>
												<div class="col-md-1">
													<a href="part_revision_add.php<?php if ($record_id != 0) { ?>?PO_ID=<?php echo $record_id; } ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>
											</div>


								<div class="form-group">
									<label class="col-md-3 control-label">Batch #:<span class="required">*</span></label>
									<div class="col-md-5">
										<input type="text" class="form-control" placeholder="" name="batch_number" required />
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>

								<div class="form-group">
												<label class="col-md-3 control-label">User:<span class="required">*</label>
												<div class="col-md-5">
													<?php creator_drop_down($session_user_id); ?>
												</div>

												<div class="col-md-1">
													<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>

											</div>


											<div class="form-group">
												<label class="col-md-3 control-label">Date:<span class="required">*</span></label>
												<div class="col-md-5">
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_added" required>
													</div>
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




								<!-- now close the panel --><!-- end row! -->

					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
