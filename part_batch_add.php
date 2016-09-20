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
					if (isset($_REQUEST['new_record_id'])) {
						$change_record_id = $_REQUEST['new_record_id'];
						$part_rev_ID = $_REQUEST['new_record_id']; // comes from part_view.php. Fix for bug#
					}
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
									    <?php purchase_orders_drop_down($record_id); ?>
									</div>
									<div class="col-md-1">
										<a href="purchase_order_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">Part / Revision #:<span class="required">*</span></label>
									<div class="col-md-5">
										<?php part_rev_drop_down($part_rev_ID); ?>
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
