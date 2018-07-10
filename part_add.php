<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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

// pull the header and template stuff:
pagehead();

// SPECIFY DEFAULTS - these may be over-written during validation

if (isset($_REQUEST['name_EN'])) { 				$part_name_EN 				= $_REQUEST['name_EN']; 				} else { $part_name_EN 				= ''; }
if (isset($_REQUEST['name_CN'])) { 				$part_name_CN 				= $_REQUEST['name_CN']; 				} else { $part_name_CN 				= '中文名'; }
if (isset($_REQUEST['part_desc'])) { 			$part_description 			= $_REQUEST['part_desc']; 				} else { $part_description 			= 'Please help to update this record.'; }
if (isset($_REQUEST['part_type_ID'])) { 		$type_ID 					= $_REQUEST['part_type_ID']; 			} else { $type_ID 					= 0; }
if (isset($_REQUEST['classification_ID'])) { 	$classification_ID 			= $_REQUEST['classification_ID']; 		} else { $classification_ID 			= '0'; }
if (isset($_REQUEST['product_type_ID'])) { 		$part_product_type_ID 		= $_REQUEST['product_type_ID']; 		} else { $part_product_type_ID 		= '0'; }
if (isset($_REQUEST['is_finished_product'])) { 	$is_finished_product 		= $_REQUEST['is_finished_product']; 	} else { $is_finished_product 		= '0'; }
if (isset($_REQUEST['sup_ID'])) { 				$part_default_suppler_ID 	= $_REQUEST['sup_ID']; 					} else { $part_default_suppler_ID 	= '0'; }
if (isset($_REQUEST['created_by'])) { 			$part_creator_ID 			= $_REQUEST['created_by']; 				} else { $part_creator_ID 			= $_SESSION['user_ID']; }
if (isset($_REQUEST['record_status'])) { 		$part_record_status 		= $_REQUEST['record_status']; 			} else { $part_record_status 			= '2'; }

?>
					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">
						 <!-- START THE FORM! -->
            			  <form class="form-horizontal form-bordered" action="part_add_do.php" method="post">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Create a New Part Record</h2>

									<p class="panel-subtitle">
										This information affects ALL part revisions (which you can create next...)
									</p>
								</header>
								<div class="panel-body">
									
								<div class="form-group">
									<label class="col-md-3 control-label">Name (EN):<span class="required">*</span></label>
									<div class="col-md-5">
										<input type="text" name="name_EN" class="form-control" value="<?php echo $part_name_EN; ?>" required>
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Name (中文):</label>
									<div class="col-md-5">
										<input type="text" name="name_CN" class="form-control" value="<?php echo $part_name_CN; ?>" placeholder="中文名">
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
									
								<div class="form-group">
									<label class="col-md-3 control-label">Part Number:<span class="required">*</span></label>
									<div class="col-md-5">
										<input type="text" name="part_code" class="form-control" value="" required>
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label"><em class="text-muted">Existing Parts (for reference only):</em></label>
									<div class="col-md-5">
										<?php part_rev_drop_down(0, 0, 'part_rev_ID_reference'); ?>
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
									
								<div class="form-group">
									<label class="col-md-3 control-label">First Revision Number:</label>
									<div class="col-md-5">
										<input type="text" name="part_rev_number" class="form-control" value="A">
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Description:</label>
									<div class="col-md-5">
										<textarea name="part_desc" class="form-control populate"><?php echo $part_description; ?></textarea>
									</div>

									<div class="col-md-1">
										&nbsp;
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Type:<span class="required">*</span></label>
									<div class="col-md-5">
										<select class="form-control populate" name="part_type_ID" id="part_type_ID">
											<option value="0" selected="selected" style="display:none">Select part type:</option>
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
									<label class="col-md-3 control-label">Classification:<span class="required">*</span></label>
									<div class="col-md-5">
										<select class="form-control populate" name="classificiation_ID" id="classificiation_ID">
											<option value="0" selected="selected" style="display:none">Select part classification:</option>
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
									<label class="col-md-3 control-label">Product Type:<span class="required">*</span></label>
									<div class="col-md-5">
										<select class="form-control populate" name="product_type_ID" id="product_type_ID">
											<option value="0" selected="selected" style="display:none">Select product type:</option>
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
									<label class="col-md-3 control-label">Created By:<span class="required">*</span></label>
									<div class="col-md-5">
										<?php creator_drop_down($part_creator_ID); ?>
									</div>

									<div class="col-md-1">
										<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
									</div>
								</div>
								
								
								<div class="form-group">
									<label class="col-md-3 control-label">Record Status:<span class="required">*</span></label>
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
									<?php form_buttons('parts'); ?>
								</footer>
							</section>

							</form>

						</div>


									</div>
									<!-- ********************************************************* -->
									<!-- ********************************************************* -->


					<!-- end: page -->

<?php
// now close the page out:
pagefoot($page_id);
?>
