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

$page_id = 18;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else { // no id = nothing to see here!
	header("Location: parts.php?msg=NG&action=view&error=no_id");
	exit();
}

// now get the part info:
$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $record_id;
// echo $get_part_SQL;

$result_get_part = mysqli_query($con,$get_part_SQL);

// while loop
while($row_get_part = mysqli_fetch_array($result_get_part)) {
	$part_ID = $row_get_part['ID'];
	$part_code = $row_get_part['part_code'];
	$name_EN = $row_get_part['name_EN'];
	$name_CN = $row_get_part['name_CN'];
	$description = $row_get_part['description'];
	$type_ID = $row_get_part['type_ID'];
	$classification_ID = $row_get_part['classification_ID'];
	$part_default_suppler_ID = $row_get_part['default_suppler_ID'];
	$part_record_status = $row_get_part['record_status'];
	$part_product_type_ID = $row_get_part['product_type_ID'];
	$part_creator_ID = $row_get_part['created_by'];

	// GET PART CLASSIFICATION:

	$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $row_get_part['classification_ID'] . "'";
	// echo $get_part_class_SQL;

	$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
	// while loop
	while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
		$part_class_EN = $row_get_part_class['name_EN'];
		$part_class_CN = $row_get_part_class['name_CN'];
		$part_class_description = $row_get_part_class['description'];
		$part_class_color = $row_get_part_class['color'];
	}

	// check for revisions. If there are none, we will create one!
    $count_revs_sql = "SELECT COUNT( ID ) FROM  `part_revisions` WHERE  `part_ID` = " . $record_id;
    $count_revs_query = mysqli_query($con, $count_revs_sql);
    $count_revs_row = mysqli_fetch_row($count_revs_query);
    $total_revs = $count_revs_row[0];

} // end get part info WHILE loop


// pull the header and template stuff:
pagehead($page_id);

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Edit Part Profile - <?php echo $part_code; ?> - <?php echo $name_EN; if (($name_CN!='')&&($name_CN!='中文名')) { ?> / <?php echo $name_CN; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><a href="parts.php">All Parts</a></li>
								<li><span>Part Profile</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">
						 <!-- START THE FORM! -->
            			  <form class="" action="part_edit_do.php" method="post"><!-- class="form-horizontal form-bordered" ?? -->
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Global Part Record</h2>

									<p class="panel-subtitle">
										This information affects ALL part revisions
									</p>
								</header>
								<div class="panel-body">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Name (EN)</label>
												<input type="text" name="name_EN" class="form-control" value="<?php echo $name_EN; ?>">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">名字（CN）</label>
												<input type="text" name="name_CN" class="form-control" value="<?php echo $name_CN; ?>">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Code</label>
												<input type="text" name="part_code" class="form-control" value="<?php echo $part_code; ?>">
												<span class="btn btn-xs btn-info" title="Dev. Note: DO NOT ALLOW DUPLICATE OF EXISTING PART CODE!"><i class="fa fa-lightbulb-o"></i></span>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Description</label>
												<textarea name="part_desc" class="form-control populate"><?php echo $description; ?></textarea>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Status</label>
												<?php record_status_drop_down($part_record_status); ?>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Type</label>
												<select class="form-control populate" name="part_type_ID" id="part_type_ID">
												<?php
												// GET PART TYPE:
												$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE `record_status` = 2";
												// echo $get_part_type_SQL;
												$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
												// while loop
												while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {

													$part_type_EN = 			$row_get_part_type['name_EN'];
													$part_type_CN = 			$row_get_part_type['name_CN'];
													$part_type_ID = 			$row_get_part_type['ID'];
													$part_type_desc = 			$row_get_part_type['description'];
													$part_type_code = 			$row_get_part_type['code'];
													$part_type_record_status = 	$row_get_part_type['record_status']; // should be 2
												?>
													<option value="<?php echo $part_type_ID; ?>"<?php if ($type_ID == $part_type_ID) { ?> selected="selected"<?php } ?>><?php echo $part_type_EN; if (($part_type_CN != '')&&($part_type_CN != '中文名')) { echo " / " . $part_type_CN; } ?></option>
												<?php
												} // end get part type loop
												?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Classifcation</label>
												<select class="form-control populate" name="classificiation_ID" id="classificiation_ID">
												<?php

												$get_part_classification_SQL = "SELECT * FROM `part_classification` where `record_status` = 2 ORDER BY `name_EN` ASC";

												$part_classification_count = 0;

												$result_get_part_classification = mysqli_query ( $con, $get_part_classification_SQL );
												/* Part Classification Details */
												while ( $row_get_part_classification = mysqli_fetch_array ( $result_get_part_classification ) ) {

														$part_classification_ID = 				$row_get_part_classification['ID'];
														$part_classification_name_EN = 			$row_get_part_classification['name_EN'];
														$part_classification_name_CN = 			$row_get_part_classification['name_CN'];
														$part_classification_desc = 			$row_get_part_classification['description'];
														$part_classification_color = 			$row_get_part_classification['color'];
														$part_classification_record_status = 	$row_get_part_classification['record_status']; // should be 2

															// NOW DISPLAY THE RESULTS!

															?>
																  <option value="<?php echo $part_classification_ID; ?>"<?php if ($part_classification_ID == $classification_ID) { echo ' selected="selected"'; } ?>><?php echo $part_classification_name_EN; if (($part_classification_name_CN!='')&&($part_classification_name_CN!='中文名')){ echo " / " . $part_classification_name_CN; } ?></option>
															<?php

												} // end get record WHILE loop

												/* *************** END GET PART CLASSIFICATION INFO *********************** */

												?>

												</select>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Created by</label>
												<?php creator_drop_down($part_creator_ID); ?>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Default Supplier</label>
												<select data-plugin-selectTwo class="form-control populate" name="sup_ID" id="sup_ID">
												<?php

												/* ***************  GET SUPPLIER INFO ************************** */
												// now get the record info:
												$get_sups_SQL = "SELECT * FROM `suppliers` ORDER BY `record_status` DESC";
												// echo $get_sups_SQL;

												$result_get_sups = mysqli_query($con,$get_sups_SQL);

												// while loop
												while($row_get_sup = mysqli_fetch_array($result_get_sups)) {
													$sup_ID = 						$row_get_sup['ID'];
													$sup_en =						$row_get_sup['name_EN'];
													$sup_cn =						$row_get_sup['name_CN'];
													$sup_web = 						$row_get_sup['website'];
													$sup_internal_ID = 				$row_get_sup['epg_supplier_ID'];
													$sup_status = 					$row_get_sup['record_status'];
													$sup_part_classification = 		$row_get_sup['part_classification']; // look up
													$sup_item_supplied = 			$row_get_sup['items_supplied'];
													$sup_part_type_ID = 			$row_get_sup['part_type_ID']; // look up
													$sup_certs = 					$row_get_sup['certifications'];
													$sup_cert_exp_date = 			$row_get_sup['certification_expiry_date'];
													$sup_evaluation_date = 			$row_get_sup['evaluation_date'];
													$sup_address_EN = 				$row_get_sup['address_EN'];
													$sup_address_CN = 				$row_get_sup['address_CN'];
													$sup_country_ID = 				$row_get_sup['country_ID']; // look up
													$sup_contact_person = 			$row_get_sup['contact_person'];
													$sup_mobile_phone = 			$row_get_sup['mobile_phone'];
													$sup_telephone = 				$row_get_sup['telephone'];
													$sup_fax = 						$row_get_sup['fax'];
													$sup_email_1 = 					$row_get_sup['email_1'];
													$sup_email_2 = 					$row_get_sup['email_2'];

															// VENDOR CLASSIFICATION BY STATUS:

															$get_sup_status_SQL = "SELECT * FROM `supplier_status` WHERE `status_level` ='" . $sup_status . "'";
															// echo $get_vendor_status_SQL;

															$result_get_sup_status = mysqli_query($con,$get_sup_status_SQL);
															// while loop
															while($row_get_sup_status = mysqli_fetch_array($result_get_sup_status)) {
																$sup_status_ID = 			$row_get_sup_status['ID'];
																$sup_status_name_EN = 		$row_get_sup_status['name_EN'];
																$sup_status_name_CN = 		$row_get_sup_status['name_CN'];
																$sup_status_level = 		$row_get_sup_status['status_level'];
																$sup_status_description = 	$row_get_sup_status['status_description'];
																$sup_status_color_code = 	$row_get_sup_status['color_code'];
																$sup_status_icon = 			$row_get_sup_status['icon'];
															}



															// GET PART CLASSIFICATION:
															$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $sup_part_classification . "'";
															// echo $get_part_class_SQL;

															$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
															// while loop
															while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
																$part_class_EN = 			$row_get_part_class['name_EN'];
																$part_class_CN = 			$row_get_part_class['name_CN'];
																$part_class_description = 	$row_get_part_class['description'];
																$part_class_color = 		$row_get_part_class['color'];
															}

															// NOW DISPLAY THE VENDOR DETAILS!

															?>
																  <option value="<?php echo $sup_ID; ?>"><?php echo $sup_internal_ID ;?> - <?php echo $sup_en; if (($sup_cn!='')&&($sup_cn!='中文名')){ echo " / " . $sup_cn; } ?> (Status: <?php echo $sup_status_name_EN; if (($sup_status_name_CN!='')&&($sup_status_name_CN!='中文名')) { echo " / " . $sup_status_name_CN; } ?>), (CLASS: <?php echo $part_class_EN; if (($part_class_CN!='')&&($part_class_CN!='中文名')) { echo " / " . $part_class_CN; } ?>)</option>
															<?php

												} // end get record WHILE loop

												/* *************** END GET SUPPLIER INFO *********************** */

												?>

												</select>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Product Type</label>
												<select class="form-control populate" name="product_type_ID" id="product_type_ID">
												<?php

												$get_product_types_SQL = "SELECT * FROM `product_type` where `record_status` = 2 ORDER BY `product_type`.`product_type_code` ASC";

												$product_type_count = 0;

												$result_get_product_types = mysqli_query ( $con, $get_product_types_SQL );
												/* Product Type Details */
												while ( $row_get_product_types = mysqli_fetch_array ( $result_get_product_types ) ) {

														$product_type_ID = 				$row_get_product_types['ID'];
														$product_type_code = 			$row_get_product_types['product_type_code'];
														$product_type_name_EN = 		$row_get_product_types['name_EN'];
														$product_type_name_CN = 		$row_get_product_types['name_CN'];
														$product_type_record_status = 	$row_get_product_types['record_status']; // should be 2

															// NOW DISPLAY THE RESULTS!

															?>
																  <option value="<?php echo $product_type_ID; ?>"<?php if ($part_product_type_ID == $product_type_ID) { echo ' selected="selected"'; } ?>><?php echo $product_type_code; ?> - <?php echo $product_type_name_EN; if (($product_type_name_CN!='')&&($product_type_name_CN!='中文名')){ echo " / " . $product_type_name_CN; } ?></option>
															<?php

												} // end get record WHILE loop

												/* *************** END GET SUPPLIER INFO *********************** */

												?>

												</select>
											</div>
										</div>
									</div>
								</div>
								<footer class="panel-footer">
									<?php
										if (isset($_REQUEST['id'])) {
											?>
											<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
											<?php
										}
									?>
									<button class="btn btn-danger" href="part_view.php?id=<?php echo $record_id; ?>"><i class="fa fa-arrow-left"></i> CANCEL / BACK</button>
                        			<button type="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> RESET</button>
									<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SAVE CHANGES</button>
								</footer>
							</section>

							</form>

						</div>


									</div>
									<!-- ********************************************************* -->
									<!-- ********************************************************* -->


					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);
?>
