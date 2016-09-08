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

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: batch_log.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	$get_po_SQL = "SELECT *  FROM  `part_batch` WHERE `ID` = " . $record_id;
	$result_get_part_batch = mysqli_query($con,$get_po_SQL);
	// while loop
	while($row_get_part_batch = mysqli_fetch_array($result_get_part_batch)) {

			// now print each record:
			$part_batch_id = $row_get_part_batch['ID'];
			$part_batch_po_id = $row_get_part_batch['PO_ID'];
			$part_batch_part_id = $row_get_part_batch['part_ID'];
			$part_batch_batch_number = $row_get_part_batch['batch_number'];
			$part_batch_part_rev_id = $row_get_part_batch['part_rev'];
			$part_batch_supplier_id = $row_get_part_batch['supplier_ID'];

	} // end while loop
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Purchase Order<?php if ($record_id != 0) { ?> : <? echo $po_number; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="batch_log.php">Part Batch</a>
									</li>
								<li><span>Edit Part Batch</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="part_batch_edit_do.php" method="post">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Edit Part Batch Details:</h2>
								</header>
								<div class="panel-body">
									<div class="form-group">
										<label class="col-md-3 control-label">PO #:<span class="required">*</label>
										<div class="col-md-5">
											<?php purchase_orders_drop_down($part_batch_po_id); ?>
										</div>
										<div class="col-md-1">
											<a href="purchase_order_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">Part / Revision #:<span class="required">*</span></label>
										<div class="col-md-5">
											<?php part_rev_drop_down($part_batch_part_rev_id); ?>
										</div>
										<div class="col-md-1">
											<a href="part_revision_add.php<?php if ($record_id != 0) { ?>?PO_ID=<?php echo $record_id; } ?>" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
										</div>
									</div>


									<div class="form-group">
										<label class="col-md-3 control-label">Batch #:<span class="required">*</span></label>
										<div class="col-md-5">
											<input type="text" class="form-control" placeholder="" name="batch_number" value="<?php echo $part_batch_batch_number; ?>" required ></input>
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

												<div class="form-group">
													<label class="col-md-3 control-label">Supplier:<span class="required">*</span></label>
													<div class="col-md-5">
														<select data-plugin-selectTwo class="form-control populate" name="sup_ID" required>
														<?php
														// get batch list
														$order_by = " ORDER BY `record_status` DESC";
														$get_sup_list_SQL = "SELECT * FROM `suppliers` WHERE `record_status` = 2 " . $order_by; // SHOWING APPROVED VENDORS ONLY!
														echo "<!-- DEBUG: " . $get_sup_list_SQL . " -->";
														$result_get_sup_list = mysqli_query($con,$get_sup_list_SQL);
														// while loop
														while($row_get_sup_list = mysqli_fetch_array($result_get_sup_list)) {

																// now print each record:
																$sup_id = $row_get_sup_list['ID'];
																$sup_epg_supplier_ID = $row_get_sup_list['epg_supplier_ID'];
																$sup_name_EN = $row_get_sup_list['name_EN'];
																$sup_name_CN = $row_get_sup_list['name_CN'];
																$sup_website = $row_get_sup_list['website'];
																$sup_record_status = $row_get_sup_list['record_status'];
																$sup_part_classification = $row_get_sup_list['part_classification'];
																$sup_items_supplied = $row_get_sup_list['items_supplied'];
																$sup_part_type_ID = $row_get_sup_list['part_type_ID'];
																$sup_certifications = $row_get_sup_list['certifications'];
																$sup_certification_expiry_date = $row_get_sup_list['certification_expiry_date'];
																$sup_evaluation_date = $row_get_sup_list['evaluation_date'];
																$sup_address_EN = $row_get_sup_list['address_EN'];
																$sup_address_CN = $row_get_sup_list['address_CN'];
																$sup_country_ID = $row_get_sup_list['country_ID'];
																$sup_contact_person = $row_get_sup_list['contact_person'];
																$sup_mobile_phone = $row_get_sup_list['mobile_phone'];
																$sup_telephone = $row_get_sup_list['telephone'];
																$sup_fax = $row_get_sup_list['fax'];
																$sup_email_1 = $row_get_sup_list['email_1'];
																$sup_email_2 = $row_get_sup_list['email_2'];

																?>
																<option value="<?php echo $sup_id; ?>" <?php if ($sup_id == $part_batch_supplier_id) { ?> selected="selected"<?php } ?>>
																	<?php echo $sup_name_EN; if (($sup_name_CN!='')&&($sup_name_CN!='中文名')) { echo " / " . $sup_name_CN; } ?>
																</option>
																<?php
															}
															?>
														</select>
													</div>





													<div class="col-md-1">
														&nbsp;
													</div>
												</div>

								</div>


								<footer class="panel-footer">
										<input type="hidden" value="<?php echo $part_batch_id; ?>" name="part_batch_id" />
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
