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
	$part_ID 					= $row_get_part['ID'];
	$part_code 					= $row_get_part['part_code'];
	$name_EN 					= $row_get_part['name_EN'];
	$name_CN 					= $row_get_part['name_CN'];
	$description 				= $row_get_part['description'];
	$type_ID 					= $row_get_part['type_ID'];
	$classification_ID 			= $row_get_part['classification_ID'];
	$part_default_suppler_ID 	= $row_get_part['default_suppler_ID'];
	$part_record_status 		= $row_get_part['record_status'];
	$part_product_type_ID 		= $row_get_part['product_type_ID'];
	$part_creator_ID 			= $row_get_part['created_by'];
	$is_finished_product		= $row_get_part['is_finished_product'];


	// GET PART CLASSIFICATION:

	$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $row_get_part['classification_ID'] . "'";
	// echo $get_part_class_SQL;

	$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
	// while loop
	while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
		$part_class_EN 				= $row_get_part_class['name_EN'];
		$part_class_CN 				= $row_get_part_class['name_CN'];
		$part_class_description 	= $row_get_part_class['description'];
		$part_class_color 			= $row_get_part_class['color'];
	}

	// check for revisions. If there are none, we will create one!
    $count_revs_sql 	= "SELECT COUNT( ID ) FROM  `part_revisions` WHERE  `part_ID` = " . $record_id;
    $count_revs_query 	= mysqli_query($con, $count_revs_sql);
    $count_revs_row 	= mysqli_fetch_row($count_revs_query);
    $total_revs 		= $count_revs_row[0];

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
								<li><a href="part_view.php?id=<?php echo $record_id; ?>">Part Profile</a></li>
								<li><span>Edit Part</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">
						 <!-- START THE FORM! -->
            			  <form class="form-horizontal form-bordered" action="part_edit_do.php" method="post">
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
									
								<div class="form-group">
									<label class="col-md-3 control-label">Name (EN):</label>
									<div class="col-md-5">
										<input type="text" name="name_EN" class="form-control" value="<?php echo $name_EN; ?>">
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Name (中文):</label>
									<div class="col-md-5">
										<input type="text" name="name_CN" class="form-control" value="<?php echo $name_CN; ?>" placeholder="中文名">
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
									
								<div class="form-group">
									<label class="col-md-3 control-label">Part Number:</label>
									<div class="col-md-5">
										<input type="text" name="part_code" class="form-control" value="<?php echo $part_code; ?>">
									</div>

									<div class="col-md-1">
										<a href="#existing_part_codes_popup" class="mb-xs mt-xs mr-xs btn btn-info pull-right"><i class="fa fa-question"></i></a>
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Description:</label>
									<div class="col-md-5">
										<textarea name="part_desc" class="form-control populate"><?php echo $description; ?></textarea>
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Type:</label>
									<div class="col-md-5">
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

									<div class="col-md-1">
										<a href="part_type_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>
									
								<div class="form-group">
									<label class="col-md-3 control-label">Classification:</label>
									<div class="col-md-5">
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

									<div class="col-md-1">
										<a href="part_classification_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Product Type:</label>
									<div class="col-md-5">
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

									<div class="col-md-1">
										<a href="product_type_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Finished Product?:</label>
									<div class="col-md-5">
										<input type="checkbox" name="is_finished_product" id="is_finished_product" data-plugin-ios-switch<?php echo $is_finished_product == '1' ? ' checked="checked"' : '' ?> value="1" />
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Default Supplier:</label>
									<div class="col-md-5">
										<?php supplier_drop_down($part_default_suppler_ID); ?>
									</div>

									<div class="col-md-1">
										<a href="supplier_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>
									
								<div class="form-group">
									<label class="col-md-3 control-label">Created By:</label>
									<div class="col-md-5">
										<?php creator_drop_down($part_creator_ID); ?>
									</div>

									<div class="col-md-1">
										<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Record Status:</label>
									<div class="col-md-5">
										<?php record_status_drop_down($part_record_status); ?>
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								
								</div>
								<footer class="panel-footer">
									<!-- ADD ANY OTHER HIDDEN VARS HERE -->
									<?php form_buttons('part_view', $record_id); ?>
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
