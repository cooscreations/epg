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
// echo $get_parts_SQL;

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
	
	/* -- don't need for the SELECT form below
	// GET PART TYPE:
	$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $type_ID;
	// echo $get_part_type_SQL;
	$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
	// while loop
	while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
		$part_type_EN = $row_get_part_type['name_EN'];
		$part_type_CN = $row_get_part_type['name_CN'];
	}
	*/
					  	
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
	
	if ($total_revs == 0) {
		$add_rev_SQL = "INSERT INTO `part_revisions`(`ID`, `part_ID`, `revision_number`, `remarks`, `date_approved`, `user_ID`, `price_USD`) VALUES (NULL,'".$record_id."','A','No revisions found, so we auto-generated Revision A','" . date("Y-m-d H:i:s") . "','2','0.0000')";
	
		if (mysqli_query($con, $add_rev_SQL)) {
	
			$record_id = mysqli_insert_id($con);
	
			// echo "INSERT # " . $record_id . " OK";
	
			// AWESOME! We added the record
			$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_revisions','" . $record_id . "','2','Could not find part revision, so we auto-generated a Rev. A','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
			// echo $record_edit_SQL;
		
			if (mysqli_query($con, $record_edit_SQL)) {	
				// AWESOME! We added the change record to the database
				// NO ACTION NEEDED - CONTINUE WITH THE PAGE...
			}
			else {
				echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
			}
		}
		else {
			echo "<h4>Failed to update existing user with SQL: <br />" . $add_rev_SQL . "</h4>";
		}
	}
	
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
												<label class="control-label">Status</label>
												<select class="form-control populate" name="part_record_status" id="part_record_status">
												  <option value="0"<?php if ($part_record_status == 0) { ?> selected="selected"<?php } ?>>DELETED</option>
												  <option value="1"<?php if ($part_record_status == 1) { ?> selected="selected"<?php } ?>>PENDING</option>
												  <option value="2"<?php if ($part_record_status == 2) { ?> selected="selected"<?php } ?>>PUBLISHED</option>
												</select>  
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Description</label>
												<textarea name="part_desc" class="form-control populate"><?php echo $description; ?></textarea>
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
												<label class="control-label">Default Supplier</label>
												<select class="form-control populate" name="sup_ID" id="sup_ID">
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
												<select class="form-control populate" name="sup_ID" id="sup_ID">
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
									<button class="btn btn-success"><i class="fa fa-save"></i> SAVE PART</button>
								</footer>
							</section>
						</div>
						
						
									</div>
									<!-- ********************************************************* -->
									<!-- ********************************************************* -->
									<div class="row">
						
						<!-- NOW START PART REVISION FORM -->
						
						
						
						<div class="col-md-12">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Select a Revision To Edit <span class="btn btn-xs btn-info" title="Use tabs to display forms for each revision. Warn of unsaved changes when selecting another tab? Auto-select latest revision if none specified"><i class="fa fa-lightbulb-o"></i></a></h2>

									<p class="panel-subtitle">
										This information affects ONLY REVISION <span class="btn btn-warning">A</span>
										<br />
										<em>(EDIT A DIFFERENT REVISION ... ...)</em>
									</p>
								</header>
								<div class="panel-body">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Change Part<sup class="text-danger">*</sup></label>
												<!-- CODE FROM PART JUMPER -->
												<select class="form-control populate" name="part_ID" id="part_ID">
												  <?php 	
										
												$get_j_parts_SQL = "SELECT * FROM `parts`";
												// echo $get_parts_SQL;
	
												$result_get_j_parts = mysqli_query($con,$get_j_parts_SQL);
												// while loop
												while($row_get_j_parts = mysqli_fetch_array($result_get_j_parts)) {
							
													$j_part_ID = $row_get_j_parts['ID'];
													$j_part_code = $row_get_j_parts['part_code'];
													$j_part_name_EN = $row_get_j_parts['name_EN'];
													$j_part_name_CN = $row_get_j_parts['name_CN'];
													$j_part_description = $row_get_j_parts['description'];
													$j_part_type_ID = $row_get_j_parts['type_ID'];
													$j_part_classification_ID = $row_get_j_parts['classification_ID'];
										
												   ?>
												  <option value="<?php echo $j_part_ID; ?>"<?php if ($record_id == $j_part_ID) { ?> selected="selected"<?php } ?>><?php echo $j_part_code; ?> - <?php echo $j_part_name_EN; if (($j_part_name_CN != '')&&($j_part_name_CN != '中文名')) { ?> / <?php echo $j_part_name_CN; } ?></option>
												  <?php 
												  } // end get part list 
												  ?>
												 </select>
												<!-- / PART JUMPER -->
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Rev. #</label>
												<input type="text" name="lastname" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Remarks</label>
												<input type="email" name="email" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Date Approved</label>
												<input type="url" name="website" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Created By</label>
												<input type="email" name="email" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Record Status</label>
												<input type="url" name="website" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Weight (g)</label>
												<input type="email" name="email" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Price ($USD)</label>
												<input type="url" name="website" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">Part Status</label>
												<input type="email" name="email" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="control-label">?</label>
												<input type="url" name="website" class="form-control">
											</div>
										</div>
									</div>
								</div>
								<footer class="panel-footer">
									<button class="btn btn-success"><i class="fa fa-save"></i> SAVE REVISION</button>
								</footer>
							</section>
						</div>
						
						
					</div>
					 
					<div class="clearfix">&nbsp;</div>
					
					
					
					<div class="tabs tabs-vertical tabs-left">
						<ul class="nav nav-tabs col-sm-1 col-xs-1">
							<?php 
							// show list of part revisions in reverse order, making the most recent one the expanded tab:
							
							// get part revision info:
							// NOTE: I use this query again lower down for the panel body. It's a little sloppy, but it should work :)
								$get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE  `part_ID` ='" . $part_ID . "' ORDER BY `revision_number` DESC";

								$loop_count = 0;

								$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
								// while loop
								while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {
								
									$loop_count = $loop_count + 1;
									
									// now print what we need:  
									$rev_id = $row_get_part_rev['ID'];
									$rev_number = $row_get_part_rev['revision_number'];
									
									?>
									
							<li class="<?php if ($loop_count == 1) { ?>active<?php } ?>">
								<a href="#rev_<?php echo $rev_id; ?>" data-toggle="tab" aria-expanded="true"><i class="fa fa-star<?php if ($loop_count != 1) { ?>-o<?php } ?>"></i> Rev. <?php echo $rev_number; ?></a>
							</li>
							
								<?php													
								} // END GET PART REVISIONS TO BUILD TAB LIST...
								?>
							
						</ul>
						<div class="tab-content">
						
						
						
						
						<!-- START LOOP ITERATION HERE: -->
						
						<?php 
						
						$loop_body_count = 0;

								// I'm using the same SQL query from above... 
								$result_get_part_rev_body = mysqli_query($con,$get_part_rev_SQL);
								// while loop
								while($row_get_part_rev_body = mysqli_fetch_array($result_get_part_rev_body)) {
								
									$loop_body_count = $loop_body_count + 1;
									
									// now print each record:  
									$rev_body_id = $row_get_part_rev_body['ID'];
									$rev_body_part_id = $row_get_part_rev_body['part_ID'];
									$rev_body_number = $row_get_part_rev_body['revision_number'];
									$rev_body_remarks = $row_get_part_rev_body['remarks'];
									$rev_body_date = $row_get_part_rev_body['date_approved'];
									$rev_body_user = $row_get_part_rev_body['user_ID'];
									$rev_body_price_USD = $row_get_part_rev_body['price_USD'];
									$rev_body_weight_g = $row_get_part_rev_body['weight_g'];
									
									// get user
					  				$get_rev_user_SQL = "SELECT * FROM  `users` WHERE  `ID` =" . $rev_body_user;
									$result_get_rev_user = mysqli_query($con,$get_rev_user_SQL);
									// while loop
									while($row_get_rev_user = mysqli_fetch_array($result_get_rev_user)) {
											// now print each record:  
											$rev_user_first_name = $row_get_rev_user['first_name'];
											$rev_user_last_name = $row_get_rev_user['last_name'];
											$rev_user_name_CN = $row_get_rev_user['name_CN'];
									}
									
									// now get the part revision photo!
									
									$num_rev_photos_found = 0;
									
									$get_part_rev_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $rev_body_id;
									// echo "<h1>".$get_part_rev_photo_SQL."</h1>";
									$result_get_part_rev_photo = mysqli_query($con,$get_part_rev_photo_SQL);
									// while loop
									while($row_get_part_rev_photo = mysqli_fetch_array($result_get_part_rev_photo)) {
									
										$num_rev_photos_found = $num_rev_photos_found + 1;
									
										// now print each record:  
										$rev_photo_id = $row_get_part_rev_photo['ID'];
										$rev_photo_name_EN = $row_get_part_rev_photo['name_EN'];
										$rev_photo_name_CN = $row_get_part_rev_photo['name_CN'];
										$rev_photo_filename = $row_get_part_rev_photo['filename'];
										$rev_photo_filetype_ID = $row_get_part_rev_photo['filetype_ID'];
										$rev_photo_location = $row_get_part_rev_photo['file_location'];
										$rev_photo_lookup_table = $row_get_part_rev_photo['lookup_table'];
										$rev_photo_lookup_id = $row_get_part_rev_photo['lookup_ID'];
										$rev_photo_document_category = $row_get_part_rev_photo['document_category'];
										$rev_photo_record_status = $row_get_part_rev_photo['record_status'];
										$rev_photo_created_by = $row_get_part_rev_photo['created_by'];
										$rev_photo_date_created = $row_get_part_rev_photo['date_created'];
										$rev_photo_filesize_bytes = $row_get_part_rev_photo['filesize_bytes'];
										$rev_photo_document_icon = $row_get_part_rev_photo['document_icon'];
										$rev_photo_document_remarks = $row_get_part_rev_photo['document_remarks'];
										$rev_photo_doc_revision = $row_get_part_rev_photo['doc_revision'];
										
									}
									
									// echo "<h1>Revs Found: " . $num_rev_photos_found . "</h1>";
										
									if ($num_rev_photos_found != 0) {
										$rev_photo_location = "assets/images/" . $rev_photo_location . "/" . $rev_photo_filename;
									}
									else {
										$rev_photo_location = "assets/images/no_image_found.jpg";
									}
									
									
									?>
									
						
							<div id="rev_<?php echo $rev_body_id; ?>" class="tab-pane <?php if ($loop_body_count == 1) { ?>active<?php } ?>">
								
								
								<div class="row">
								
									<div class="col-md-4 col-lg-3">
									
									
									
									<section class="panel">
								<div class="panel-body">
									<div class="thumb-info mb-md">
										<img src="<?php echo $rev_photo_location; ?>" class="rounded img-responsive" alt="<?php echo $part_code; ?> - <?php echo $name_EN; if (($name_CN!='')&&($name_CN!='中文名')) { ?> / <?php echo $name_CN; } ?>">
										<div class="thumb-info-title">
											<span class="thumb-info-inner"><?php echo $part_code; ?></span>
											<span class="thumb-info-type">Rev. <?php echo $rev_body_number; ?></span>
										</div>
									</div>
									
									
									<h6 class="text-muted">About</h6>
									<ul>
									  <li><strong>Type:</strong> <?php echo $part_type_EN; if (($part_type_CN!='')&&($part_type_CN!='中文名')) { echo " / " . $part_type_CN; } ?></li>
									  <li><strong>Release Date:</strong> <?php echo date("Y-m-d", strtotime($rev_body_date)); ?></li>
									  <li><strong>Released By:</strong> <a href="user_view.php?id=<?php echo $rev_body_user; ?>"><?php echo $rev_user_first_name . " " . $rev_user_last_name; if (($rev_user_name_CN != '')&&($rev_user_name_CN != '中文名')) { echo " / " . $rev_user_name_CN; } ?></a></li>
									</ul>
									
								</div>
							</section>
							
							<ul class="simple-card-list mb-xlg">
							<?php if ($part_product_type_ID!= 0) {  ?>
								<li class="warning">
									<h3>FINAL ASSEMBLY</h3>
									<p>InsuJet Basic</p>
								</li>
							<?php }
								  else { ?>
								<li class="<?php echo $part_class_color; ?>">
									<h3><?php echo $part_class_EN; ?> / <?php echo $part_class_CN; ?></h3>
									<p><?php echo $part_class_description; ?></p>
								</li>
							<?php } ?>
								<li class="primary">
									<h3>$<?php echo $rev_body_price_USD; ?> USD</h3>
									<p>Purchase Target Price</p>
								</li>
								<li class="success">
									<h3><?php echo $rev_body_weight_g; ?> g</h3>
									<p>Total part weight</p>
								</li>
							</ul>


							
							
							
							
							
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
									
									
									
									
									<!-- END OF MAIN BODY COLUMN -->
									</div>
								
								</div>
								
								
								
								<!-- END OF PART REVISION PROFILE (numbered tab) -->
							</div>
							
							
							
							<!-- END OF LOOP ITERATION -->
							
							<?php
																							
								} // END GET REVISION DATA WHILE LOOP
						
						
							?>
							
						<!-- close TAB CONTENT -->	
						</div>
					<!-- close TABS -->
					</div>	
					
					-->
					
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>