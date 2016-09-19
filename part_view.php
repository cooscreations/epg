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
// echo $get_parts_SQL;

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
	$part_created_by 			= $row_get_part['created_by'];
	$part_is_finished_product 	= $row_get_part['is_finished_product'];


	// GET PART TYPE:
	$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $type_ID;
	// echo $get_part_type_SQL;
	$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
	// while loop
	while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
		$part_type_EN = $row_get_part_type['name_EN'];
		$part_type_CN = $row_get_part_type['name_CN'];
	}

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
						<h2>Part Profile - <?php echo $part_code; ?> - <?php echo $name_EN; if (($name_CN!='')&&($name_CN!='中文名')) { ?> / <?php echo $name_CN; } ?></h2>

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
						<!-- PART JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">JUMP TO ANOTHER PART / 看别的:</option>
                              <option value="parts.php">View All / 看全部</option>
                              <?php

							$get_j_parts_SQL = "SELECT * FROM `parts`";
					  		// echo $get_parts_SQL;

					  		$result_get_j_parts = mysqli_query($con,$get_j_parts_SQL);
					  		// while loop
					  		while($row_get_j_parts = mysqli_fetch_array($result_get_j_parts)) {

								$j_part_ID 					= $row_get_j_parts['ID'];
								$j_part_code 				= $row_get_j_parts['part_code'];
								$j_part_name_EN 			= $row_get_j_parts['name_EN'];
								$j_part_name_CN 			= $row_get_j_parts['name_CN'];
								$j_part_description 		= $row_get_j_parts['description'];
								$j_part_type_ID 			= $row_get_j_parts['type_ID'];
								$j_part_classification_ID 	= $row_get_j_parts['classification_ID'];

							   ?>
                              <option value="part_view.php?id=<?php echo $j_part_ID; ?>"><?php echo $j_part_code; ?> - <?php echo $j_part_name_EN; if (($j_part_name_CN != '')&&($j_part_name_CN != '中文名')) { ?> / <?php echo $j_part_name_CN; } ?></option>
                              <?php
							  } // end get part list
							  ?>
                              <option value="parts.php">View All / 看全部</option>
                             </select>
                            <!-- / PART JUMPER -->
						</div>
					</div>


					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-md-12">
						<!-- SHOW PART POSITION WITHIN PRODUCT TREE HERE: -->

						<!-- END PART POSITION WITHIN TREE -->
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
									$rev_id 	= $row_get_part_rev['ID'];
									$rev_number = $row_get_part_rev['revision_number'];

									?>

							<li class="<?php if ($loop_count == 1) { ?>active<?php } ?>">
								<a href="#rev_<?php echo $rev_id; ?>" data-toggle="tab" aria-expanded="true" title="Rev. #: <?php echo $rev_id; ?>"><i class="fa fa-star<?php if ($loop_count != 1) { ?>-o<?php } ?>"></i> Rev. <?php echo $rev_number; ?></a>
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
									$rev_body_id 					= $row_get_part_rev_body['ID'];
									$rev_body_part_id 				= $row_get_part_rev_body['part_ID'];
									$rev_body_number 				= $row_get_part_rev_body['revision_number'];
									$rev_body_remarks 				= $row_get_part_rev_body['remarks'];
									$rev_body_date 					= $row_get_part_rev_body['date_approved'];
									$rev_body_user 					= $row_get_part_rev_body['user_ID'];
									$rev_body_price_USD 			= $row_get_part_rev_body['price_USD'];
									$rev_body_weight_g 				= $row_get_part_rev_body['weight_g'];

									// get user
					  				$get_rev_user_SQL = "SELECT * FROM  `users` WHERE  `ID` =" . $rev_body_user;
									$result_get_rev_user = mysqli_query($con,$get_rev_user_SQL);
									// while loop
									while($row_get_rev_user = mysqli_fetch_array($result_get_rev_user)) {
											// now print each record:
											$rev_user_first_name 	= $row_get_rev_user['first_name'];
											$rev_user_last_name 	= $row_get_rev_user['last_name'];
											$rev_user_name_CN 		= $row_get_rev_user['name_CN'];
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
										$rev_photo_id 					= $row_get_part_rev_photo['ID'];
										$rev_photo_name_EN 				= $row_get_part_rev_photo['name_EN'];
										$rev_photo_name_CN 				= $row_get_part_rev_photo['name_CN'];
										$rev_photo_filename 			= $row_get_part_rev_photo['filename'];
										$rev_photo_filetype_ID 			= $row_get_part_rev_photo['filetype_ID'];
										$rev_photo_location 			= $row_get_part_rev_photo['file_location'];
										$rev_photo_lookup_table 		= $row_get_part_rev_photo['lookup_table'];
										$rev_photo_lookup_id 			= $row_get_part_rev_photo['lookup_ID'];
										$rev_photo_document_category 	= $row_get_part_rev_photo['document_category'];
										$rev_photo_record_status 		= $row_get_part_rev_photo['record_status'];
										$rev_photo_created_by 			= $row_get_part_rev_photo['created_by'];
										$rev_photo_date_created 		= $row_get_part_rev_photo['date_created'];
										$rev_photo_filesize_bytes 		= $row_get_part_rev_photo['filesize_bytes'];
										$rev_photo_document_icon 		= $row_get_part_rev_photo['document_icon'];
										$rev_photo_document_remarks 	= $row_get_part_rev_photo['document_remarks'];
										$rev_photo_doc_revision 		= $row_get_part_rev_photo['doc_revision'];

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

									<hr />

									<?php
									// now run the admin bar function:
									admin_bar('part');
									?>

									<hr />
									<a class="btn btn-warning" href="rev_edit.php?id=<?php echo $rev_body_id; ?>" title="Click here to edit the part revision record (ID#: <?php echo $rev_body_id; ?>)"><i class="fa fa-pencil"></i> EDIT REVISION <?php echo $rev_body_number; ?></a>


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


							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="label label-primary label-sm text-normal va-middle mr-sm">3</span>
										<span class="va-middle">Products</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">
									  <p class="text-muted"><small>This section is not yet live</small></p>
										<ul class="simple-user-list">
											<li>
												<figure class="image rounded">
													<img src="assets/images/parts/01012.PNG" alt="3.6" class="img-circle">
												</figure>
												<span class="title">InsuJet&trade; 3.6</span>
												<span class="message truncate">123 batches</span>
											</li>
											<li>
												<figure class="image rounded">
													<img src="assets/images/parts/01012.PNG" alt="3.5" class="img-circle">
												</figure>
												<span class="title">InsuJet&trade; 3.5</span>
												<span class="message truncate">123 batches</span>
											</li>
											<li>
												<figure class="image rounded">
													<img src="assets/images/parts/01012.PNG" alt="3.4" class="img-circle">
												</figure>
												<span class="title">InsuJet&trade; 3.4</span>
												<span class="message truncate">123 batches</span>
											</li>
										</ul>
									</div>
								  <div class="panel-footer">
									<div class="text-right">
										<a class="text-uppercase text-muted" href="parts.php?show=products">
										  (View All)
										</a>
									</div>
								  </div>
								</div>
							</section>



							<?php if ($type_ID!=10) { // DON'T SHOW SUPPLIER INFO FOR ASSEMBLIES! ?>


							<!-- SHOW DEFAULT SUPPLIER PANEL: -->





							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="va-middle">Default Supplier</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

									  <h5>
							<?php
							// run the function to show supplier or error
							get_supplier($part_default_suppler_ID);

							?>
									  </h5>
								</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a href="supplier_view.php?id=<?php echo $sup_ID; ?>" title="Click to view this vendor profile" class="text-uppercase text-muted">
												(View Details)
											</a>
										</div>
								  </div>
								</div>
							</section>


							<!-- END DEFAULT SUPPLIER PANEL -->


							<?php
							} // end of check for if 'type_ID' != 10
							?>

							<?php

							// COUNT FOR '0 RECORDS' EXCEPTION, AS WELL AS FOR ICON MARKER

							// get the part_to_mat_maps count:
							$count_part_to_mat_maps_SQL = "SELECT COUNT('ID') FROM `part_to_material_map` WHERE `part_rev_ID` = '" . $rev_body_id . "' AND `record_status` = '2'"; // maps for this part revision
							// echo "<br />" . $count_part_to_mat_maps_SQL . "<br />";
							$count_part_to_mat_maps_query = mysqli_query($con, $count_part_to_mat_maps_SQL);
							$count_part_to_mat_maps_row = mysqli_fetch_row($count_part_to_mat_maps_query);
							// Here we have the total row count
							$total_part_to_mat_maps = $count_part_to_mat_maps_row[0];

							if ($total_part_to_mat_maps == 0) {
								$mat_count_label = 'label-danger';
							}
							else {
								$mat_count_label = 'label-primary';
							}

							?>
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="label <?php echo $mat_count_label; ?> label-sm text-normal va-middle mr-sm"><?php echo $total_part_to_mat_maps; ?></span>
										<span class="va-middle">Material(s)</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">
									  <ul class="simple-user-list">
										<?php

										if ($total_part_to_mat_maps > 0) {

										// get the materials from the part_to_material_map table, as some parts may contain 2 or more materials:
										$get_part_to_material_map_SQL = "SELECT * FROM `part_to_material_map` WHERE `part_rev_ID` = '" . $rev_body_id . "' AND `record_status` = 2";
										// echo '<br />' . $get_part_to_material_map_SQL . '<br />';

											$result_get_part_to_material_map = mysqli_query($con,$get_part_to_material_map_SQL);
											// while loop
											while($row_get_part_to_material_map = mysqli_fetch_array($result_get_part_to_material_map)) {

												$part_to_material_map_ID 			= $row_get_part_to_material_map['ID'];
												$part_to_material_map_part_rev_ID 	= $row_get_part_to_material_map['part_rev_ID']; // should match 'rev_body_id'
												$part_to_material_map_material_ID 	= $row_get_part_to_material_map['material_ID']; // look this up!
												$part_to_material_map_variant_ID 	= $row_get_part_to_material_map['variant_ID'];
												$part_to_material_map_record_status = $row_get_part_to_material_map['record_status']; // should be 2 (active / published) only

												// look up material:

												$get_material_SQL = "SELECT * FROM `material` WHERE `ID` = '" . $part_to_material_map_material_ID . "' AND `record_status` = 2";
												// echo $get_material_SQL;
												$result_get_material = mysqli_query($con,$get_material_SQL);
												// while loop
												while($row_get_material = mysqli_fetch_array($result_get_material)) {
													$material_ID 			= $row_get_material['ID'];
													$material_name_EN 		= $row_get_material['name_EN'];
													$material_name_CN 		= $row_get_material['name_CN'];
													$material_description 	= $row_get_material['description'];
													$material_record_status = $row_get_material['record_status']; // should be 2 (published / active)

													$material_name_to_show = $material_name_EN;
													if (($material_name_CN!='')&&($material_name_CN!='中文名')) {
														$material_name_to_show .= " / " . $material_name_CN;
													}

												} // end get material info loop

												// NOW OUTPUT!
												?>
												<li>
													<figure class="image rounded">
													  <span class="fa-stack fa-lg">
														<i class="fa fa-circle fa-stack-2x text-info"></i>
														<i class="fa fa-question fa-stack-1x fa-inverse"></i>
													  </span>
													</figure>
													<span class="title"><?php echo $material_name_to_show; ?></span>
													<span class="message truncate"><a href="material_view.php?id=<?php echo $material_ID; ?>">View Material Record</a></span>
												</li>
											<?php
										  		} // end get material info loop
											} // end of RECORDS FOUND
											else { // NO RECORDS FOUND!
											?>
											<li>
												<figure class="image rounded">
												  <span class="fa-stack fa-lg">
													<i class="fa fa-circle fa-stack-2x text-danger"></i>
													<i class="fa fa-exclamation-triangle fa-stack-1x fa-inverse"></i>
												  </span>
												</figure>
												<span class="title text-danger">NO MATERIAL SET</span>
												<span class="message truncate"><a href="part_to_material_patch.php">Add Now</a></span>
											</li>
											<?php
											} // end of no records found 'total_part_to_mat_maps' = 0
										  ?>
										</ul>
									</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="part_material_map.php?part_id=<?php echo $record_id; ?>&rev_id=<?php echo $rev_body_id; ?>" title="Click here to view all materials">(View All)</a>
										</div>
								  </div>
								</div>
							</section>

									<!-- END OF LEFT COLUMN: -->
									</div>

									<!-- START MAIN BODY COLUMN: -->
									<div class="col-md-8 col-lg-8">


<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->



						<div class="row">


						<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="label label-primary label-sm text-normal va-middle mr-sm">5</span>
										<span class="va-middle">Documents</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

										<div class="table-responsive">
										 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
											<tr>
												<th>Type</th>
												<th>Name</th>
												<th>Rev.</th>
											 </tr>


											<tr>
											  <td><i class="fa fa-file-excel-o"></i></td>
											  <td><a href="#">ICQ Form</a></td>
											  <td><a href="#"><?php echo $rev_body_number; ?>1</a></td>
											</tr>


											<tr>
											  <td><i class="fa fa-file-word-o"></i></td>
											  <td><a href="#">Technical Specifications</a></td>
											  <td><a href="#"><?php echo $rev_body_number; ?>1</a></td>
											</tr>


											<tr>
											  <td><i class="fa fa-file-pdf-o"></i></td>
											  <td><a href="#">Technical Drawing</a></td>
											  <td><a href="#"><?php echo $rev_body_number; ?>1</a></td>
											</tr>


											<tr>
											  <td><i class="fa fa-file-pdf-o"></i></td>
											  <td><a href="#">Technical Drawing</a></td>
											  <td><a href="#"><?php echo $rev_body_number; ?>2</a></td>
											</tr>


											<tr>
											  <td><i class="fa fa-file-pdf-o"></i></td>
											  <td><a href="#">Technical Drawing</a></td>
											  <td><a href="#"><?php echo $rev_body_number; ?>3</a></td>
											</tr>


											<tr>
											  <th colspan="3">TOTAL DOCUMENTS: 5</th>
											</tr>

										</table>
									   </div>

							</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="#">(View All)</a>
										</div>
								  </div>
								</div>
							</section>

						</div>

						<div class="clearfix">&nbsp;</div>


<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->
<!-- ******************************************************************************************************** -->

						<div class="row">



						<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="va-middle">Bill of Materials (BOM)</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

							<p class="lead">This part appears in the following Bill(s) Of Materials</p>

							<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 <thead>
					  <tr>
					  	<th><abbr title="Bill Of Materials">BOM</abbr></th>
					    <th>Part Info</th>
					    <th>Revision</th>
					    <th>Date Entered</th>
					    <th>Type</th>
					  </tr>
					  </thead>

					  <tbody>
					  <?php

					  // FIRST, let's try to get the BOM it IS or it is on:

					  // count BOMs made for this revision
						$count_BOMs_sql = "SELECT COUNT( ID ) FROM  `product_BOM` WHERE  `part_rev_ID` =  '".$rev_body_id."'";
						$count_BOMs_query = mysqli_query($con, $count_BOMs_sql);
						$count_BOMs_row = mysqli_fetch_row($count_BOMs_query);
						$total_BOMs = $count_BOMs_row[0];

								  $WHERE_SQL = " WHERE `record_status` = 2 AND `part_rev_ID` = " . $rev_body_id;
								  $order_by = " ORDER BY `date_entered` DESC";

								  $get_BOM_list_SQL = "SELECT * FROM `product_BOM`" . $WHERE_SQL . $order_by;
								  // echo $get_BOM_list_SQL;

								  $BOM_count = 0;

								  $result_get_BOM_list = mysqli_query($con,$get_BOM_list_SQL);
								  // while loop
								  while($row_get_BOM_list = mysqli_fetch_array($result_get_BOM_list)) {

									// GET BOM LIST:

									$BOM_ID 			= $row_get_BOM_list['ID'];
									$BOM_part_rev_ID 	= $row_get_BOM_list['part_rev_ID']; // use this to look up
									$BOM_date_entered 	= $row_get_BOM_list['date_entered'];
									$BOM_record_status 	= $row_get_BOM_list['record_status'];
									$BOM_created_by 	= $row_get_BOM_list['created_by'];
									$BOM_type 			= $row_get_BOM_list['BOM_type'];
									$BOM_parent_BOM_ID 	= $row_get_BOM_list['parent_BOM_ID'];


									/* JOIN PLANNING:
									PARTS:

									`parts`.`ID` AS `part_ID`,
									`parts`.`part_code`,
									`parts`.`name_EN`,
									`parts`.`name_CN`,
									`parts`.`description`,
									`parts`.`type_ID`,
									`parts`.`classification_ID`,
									`parts`.`record_status`,
									`parts`.`product_type_ID`

									PART REVISIONS:

									`part_revisions`.`ID` AS `rev_revision_ID`,
									`part_revisions`.`part_ID`,
									`part_revisions`.`revision_number`,
									`part_revisions`.`remarks`,
									`part_revisions`.`date_approved`,
									`part_revisions`.`user_ID`,
									`part_revisions`.`price_USD`,
									`part_revisions`.`weight_g`,
									`part_revisions`.`status_ID`,
									`part_revisions`.`material_ID`,
									`part_revisions`.`treatment_ID`,
									`part_revisions`.`treatment_notes`,
									`part_revisions`.`record_status`

									SO WE NEED:

									`parts`.`part_code`,
									`parts`.`name_EN`,
									`parts`.`name_CN`,
									`parts`.`type_ID`,
									`part_revisions`.`revision_number`,
									`part_revisions`.`part_ID`,
									`part_revisions`.`part_ID`

									*/

									$combine_part_and_rev_SQL = "SELECT `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $BOM_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2";

									$result_get_rev_part_join = mysqli_query($con,$combine_part_and_rev_SQL);
									// while loop
									while($row_get_rev_part_join = mysqli_fetch_array($result_get_rev_part_join)) {

										// GET BOM LIST:

										$rev_part_join_part_code 	= $row_get_rev_part_join['part_code'];
										$rev_part_join_name_EN 		= $row_get_rev_part_join['name_EN'];
										$rev_part_join_name_CN 		= $row_get_rev_part_join['name_CN'];
										$rev_part_join_type_ID 		= $row_get_rev_part_join['type_ID'];
										$rev_part_join_rev_num 		= $row_get_rev_part_join['revision_number'];
										$rev_part_join_part_ID 		= $row_get_rev_part_join['part_ID'];

										} // end get BOM part / part rev data
								  ?>

								  <tr>
									<td>
									  <a href="BOM_view.php?id=<?php echo $BOM_ID; ?>" class="btn btn-xs btn-primary"><i class="fa fa-gears"></i> BOM # <?php echo $BOM_ID; ?></a>
									</td>
									<td>

									  <a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>" class="btn btn-xs btn-info"><?php echo $rev_part_join_part_code; ?></a>

									  <a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>">
										<?php echo $rev_part_join_name_EN; if (($rev_part_join_name_CN!='')&&($rev_part_join_name_CN!='中文名')) { echo " / " . $rev_part_join_name_CN; }?>
									  </a>
									</td>
									<td>
									  <a class="btn btn-xs btn-warning">
										<?php echo $rev_part_join_rev_num; ?>
									  </a>
									</td>
									<td><?php echo date("Y-m-d", strtotime($BOM_date_entered)); ?></td>
									<td>
									<?php
											if ($BOM_type == 'sub') {
												echo '<abbr title="Sub-assembly">';
											}
											else if ($BOM_type == 'final') {
												echo '<abbr title="Final product">';
											}
											else {
												echo '<abbr title="error?">';
											}
											echo $BOM_type . '</abbr>';
									?></td>
								  </tr>
								  <?php

								  $BOM_count = $BOM_count + 1;

								  } // end while loop




								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */
								  /* ********************************************************* */


								  $get_BOM_ID_SQL = "SELECT * FROM `product_BOM_items` WHERE `part_rev_ID` ='" . $rev_body_id . "'";
								  $result_get_BOM_ID = mysqli_query($con,$get_BOM_ID_SQL);
								  // while loop
								  while($row_get_BOM_ID = mysqli_fetch_array($result_get_BOM_ID)) {

										// RESULTS:
										$get_BOM_item_ID = $row_get_BOM_ID['ID'];
										$get_BOM_ID = $row_get_BOM_ID['product_BOM_ID'];

												  $WHERE_SQL = " WHERE `record_status` = 2 AND `ID` = " . $get_BOM_ID;
												  $order_by = " ORDER BY `date_entered` DESC";

												  $get_this_BOM_list_SQL = "SELECT * FROM `product_BOM`" . $WHERE_SQL . $order_by;
												  // echo $get_this_BOM_list_SQL;

												  $this_BOM_count = 0;

												  $result_get_this_BOM_list = mysqli_query($con,$get_this_BOM_list_SQL);
												  // while loop
												  while($row_get_this_BOM_list = mysqli_fetch_array($result_get_this_BOM_list)) {

													// GET BOM LIST:

													$this_BOM_ID 				= $row_get_this_BOM_list['ID'];
													$this_BOM_part_rev_ID 		= $row_get_this_BOM_list['part_rev_ID']; // use this to look up
													$this_BOM_date_entered 		= $row_get_this_BOM_list['date_entered'];
													$this_BOM_record_status 	= $row_get_this_BOM_list['record_status'];
													$this_BOM_created_by 		= $row_get_this_BOM_list['created_by'];
													$this_BOM_type 				= $row_get_this_BOM_list['BOM_type'];
													$this_BOM_parent_BOM_ID 	= $row_get_this_BOM_list['parent_BOM_ID'];


													/* JOIN PLANNING:
													PARTS:

													`parts`.`ID` AS `part_ID`,
													`parts`.`part_code`,
													`parts`.`name_EN`,
													`parts`.`name_CN`,
													`parts`.`description`,
													`parts`.`type_ID`,
													`parts`.`classification_ID`,
													`parts`.`record_status`,
													`parts`.`product_type_ID`

													PART REVISIONS:

													`part_revisions`.`ID` AS `rev_revision_ID`,
													`part_revisions`.`part_ID`,
													`part_revisions`.`revision_number`,
													`part_revisions`.`remarks`,
													`part_revisions`.`date_approved`,
													`part_revisions`.`user_ID`,
													`part_revisions`.`price_USD`,
													`part_revisions`.`weight_g`,
													`part_revisions`.`status_ID`,
													`part_revisions`.`material_ID`,
													`part_revisions`.`treatment_ID`,
													`part_revisions`.`treatment_notes`,
													`part_revisions`.`record_status`

													SO WE NEED:

													`parts`.`part_code`,
													`parts`.`name_EN`,
													`parts`.`name_CN`,
													`parts`.`type_ID`,
													`part_revisions`.`revision_number`,
													`part_revisions`.`part_ID`,
													`part_revisions`.`part_ID`

													*/

													$combine_this_part_and_rev_SQL = "SELECT `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $this_BOM_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2";

													$result_get_this_rev_part_join = mysqli_query($con,$combine_this_part_and_rev_SQL);
													// while loop
													while($row_get_this_rev_part_join = mysqli_fetch_array($result_get_this_rev_part_join)) {

														// GET BOM LIST:

														$this_rev_part_join_part_code 	= $row_get_this_rev_part_join['part_code'];
														$this_rev_part_join_name_EN 	= $row_get_this_rev_part_join['name_EN'];
														$this_rev_part_join_name_CN 	= $row_get_this_rev_part_join['name_CN'];
														$this_rev_part_join_type_ID 	= $row_get_this_rev_part_join['type_ID'];
														$this_rev_part_join_rev_num 	= $row_get_this_rev_part_join['revision_number'];
														$this_rev_part_join_part_ID 	= $row_get_this_rev_part_join['part_ID'];

														} // end get BOM part / part rev data
												  ?>

												  <tr>
													<td>
													  <a href="BOM_view.php?id=<?php echo $this_BOM_ID; ?>" class="btn btn-xs btn-primary"><i class="fa fa-gears"></i> BOM # <?php echo $this_BOM_ID; ?></a>
													</td>
													<td>
					     							  <a href="part_view.php?id=<?php echo $this_rev_part_join_part_ID; ?>" class="btn btn-xs btn-info"><?php echo $this_rev_part_join_part_code; ?></a>
													  <a href="part_view.php?id=<?php echo $this_rev_part_join_part_ID; ?>">
														<?php echo $this_rev_part_join_name_EN; if (($this_rev_part_join_name_CN!='')&&($this_rev_part_join_name_CN!='中文名')) { echo " / " . $this_rev_part_join_name_CN; }?>
													  </a>
													</td>
													<td>
													  <a class="btn btn-xs btn-warning">
													    <?php echo $this_rev_part_join_rev_num; ?>
													  </a>
													</td>
													<td><?php echo date("Y-m-d", strtotime($this_BOM_date_entered)); ?></td>
													<td><?php
															if ($this_BOM_type == 'sub') {
																echo '<abbr title="Sub-assembly">';
															}
															else if ($this_BOM_type == 'final') {
																echo '<abbr title="Final product">';
															}
															else {
																echo '<abbr title="error?">';
															}
															echo $this_BOM_type . '</abbr>';
													?></td>
												  </tr>
												  <?php

												  $BOM_count = $BOM_count + 1;

												  } // end while loop
					  			} // END FOUND BOMS ASSOCIATED BY product_BOM_item


					  if ($BOM_count == 0) {
					  		?>
					  		<tr>
					  		  <td colspan="5" class="text-danger">No <acronym title="Bill Of Materials">BOM</acronym> records found</td>
					  		</tr>
					  		<?php
					  }


					  ?>
					  </tbody>

					  <tfoot>
					  <tr>
					    <th colspan="5">TOTAL: <?php echo $BOM_count; ?></th>
					  </tr>
					  </tfoot>

					 </table>
					</div>


<?php
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */
/* ********************************************************* */

		?>
		<br />
		<p class="lead">Other parts in this Assembly</p>
		<div class="table-responsive">
							 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  		  <thead>
					  			<tr>
					    			<th>Photo</th>
					    			<th>Code</th>
					    			<th>Name / 名字</th>
					    			<th><abbr title="Revision">Rev.</abbr></th>
					    			<th>Type</th>
					 			 </tr>
					 		  </thead>


					 		  <tbody>
					 			 <?php

					 			 if ($this_BOM_ID != 0) {
					 			 	$find_BOM = $this_BOM_ID;
					 			 }
					 			 else {
					 			 	$find_BOM = $BOM_ID;
					 			 }

					 			 // GET THE ASSOCIATED PARTS
					 			 $grand_total_components = 0;
								 $total_components = 0;
								 $total_assemblies = 0;

					 			 $get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $find_BOM . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

					 			 // DEBUG:
					 			 // echo $get_components_SQL;

					 			 $result_get_components = mysqli_query($con,$get_components_SQL);
								 // while loop
								 while($row_get_components 		= mysqli_fetch_array($result_get_components)) {
									$components_BOM_item_ID 	= $row_get_components['ID'];
									$components_product_BOM_ID 	= $row_get_components['product_BOM_ID']; // should be same as $record_ID
									$components_part_rev_ID 	= $row_get_components['part_rev_ID'];
									$components_parent_ID 		= $row_get_components['parent_ID'];
									$components_created_by 		= $row_get_components['created_by'];
									$components_date_entered 	= $row_get_components['date_entered'];
									$components_record_status 	= $row_get_components['record_status']; // should be 2 (published)
									// echo 'OK';
									// now get the rev and part info:

									/* JOIN PLANNING:
									PARTS:

									`parts`.`ID` AS `part_ID`,
									`parts`.`part_code`,
									`parts`.`name_EN`,
									`parts`.`name_CN`,
									`parts`.`description`,
									`parts`.`type_ID`,
									`parts`.`classification_ID`,
									`parts`.`record_status`,
									`parts`.`product_type_ID`

									PART REVISIONS:

									`part_revisions`.`ID` AS `rev_revision_ID`,
									`part_revisions`.`part_ID`,
									`part_revisions`.`revision_number`,
									`part_revisions`.`remarks`,
									`part_revisions`.`date_approved`,
									`part_revisions`.`user_ID`,
									`part_revisions`.`price_USD`,
									`part_revisions`.`weight_g`,
									`part_revisions`.`status_ID`,
									`part_revisions`.`material_ID`,
									`part_revisions`.`treatment_ID`,
									`part_revisions`.`treatment_notes`,
									`part_revisions`.`record_status`

									SO WE NEED:

									`parts`.`part_code`,
									`parts`.`name_EN`,
									`parts`.`name_CN`,
									`parts`.`type_ID`,
									`part_revisions`.`revision_number`,
									`part_revisions`.`part_ID`,
									`part_revisions`.`part_ID`

									*/

									$order_by = " ORDER BY `parts`.`type_ID` ASC";

									$combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $components_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2" . $order_by;
					    			// DEBUG:
					    			// echo 'DEBUG: ' . $combine_part_and_rev_SQL . '<br />';

					    			$result_get_rev_part_join = mysqli_query($con,$combine_part_and_rev_SQL);
									// while loop
									while($row_get_rev_part_join = mysqli_fetch_array($result_get_rev_part_join)) {

										// GET BOM LIST:

										$rev_part_join_part_ID 			= $row_get_rev_part_join['part_ID'];
										$rev_part_join_revision_ID 		= $row_get_rev_part_join['rev_revision_ID'];
										$rev_part_join_part_type_ID 	= $row_get_rev_part_join['type_ID']; // look this up!
										$rev_part_join_part_code 		= $row_get_rev_part_join['part_code'];
										$rev_part_join_name_EN 			= $row_get_rev_part_join['name_EN'];
										$rev_part_join_name_CN 			= $row_get_rev_part_join['name_CN'];
										$rev_part_join_type_ID 			= $row_get_rev_part_join['type_ID'];
										$rev_part_join_rev_num 			= $row_get_rev_part_join['revision_number'];
										$rev_part_join_part_ID 			= $row_get_rev_part_join['part_ID'];

											// GET COMPONENT PART TYPE:
											$get_component_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $rev_part_join_part_type_ID;
											// echo $get_component_type_SQL;
											$result_get_component_type = mysqli_query($con,$get_component_type_SQL);
											// while loop
											while($row_get_component_type = mysqli_fetch_array($result_get_component_type)) {
												$component_type_EN = $row_get_component_type['name_EN'];
												$component_type_CN = $row_get_component_type['name_CN'];
											}


										// now get the part revision photo!
										$num_component_photos_found = 0;
										$component_photo_location = "assets/images/no_image_found.jpg";

										$get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $rev_part_join_revision_ID;
										// echo "<h1>".$get_part_component_photo_SQL."</h1>";
										$result_get_part_component_photo = mysqli_query($con,$get_part_component_photo_SQL);
										// while loop
										while($row_get_part_component_photo = mysqli_fetch_array($result_get_part_component_photo)) {

											$num_component_photos_found = $num_component_photos_found + 1;

											// now print each record:
											$component_photo_id 				= $row_get_part_component_photo['ID'];
											$component_photo_name_EN 			= $row_get_part_component_photo['name_EN'];
											$component_photo_name_CN 			= $row_get_part_component_photo['name_CN'];
											$component_photo_filename 			= $row_get_part_component_photo['filename'];
											$component_photo_filetype_ID 		= $row_get_part_component_photo['filetype_ID'];
											$component_photo_location 			= $row_get_part_component_photo['file_location'];
											$component_photo_lookup_table 		= $row_get_part_component_photo['lookup_table'];
											$component_photo_lookup_id 			= $row_get_part_component_photo['lookup_ID'];
											$component_photo_document_category 	= $row_get_part_component_photo['document_category'];
											$component_photo_record_status 		= $row_get_part_component_photo['record_status'];
											$component_photo_created_by 		= $row_get_part_component_photo['created_by'];
											$component_photo_date_created 		= $row_get_part_component_photo['date_created'];
											$component_photo_filesize_bytes 	= $row_get_part_component_photo['filesize_bytes'];
											$component_photo_document_icon 		= $row_get_part_component_photo['document_icon'];
											$component_photo_document_remarks 	= $row_get_part_component_photo['document_remarks'];
											$component_photo_doc_revision 		= $row_get_part_component_photo['doc_revision'];

											if ($component_photo_filename!='') {
												// now apply filename
												$component_photo_location = "assets/images/" . $component_photo_location . "/" . $component_photo_filename;
											}
											else {
												$component_photo_location = "assets/images/no_image_found.jpg";
											}

										} // end get part rev photo


									if ($rev_part_join_part_type_ID == 10) {
										$total_assemblies = $total_assemblies + 1;
									}
									else {
										$total_components = $total_components + 1;
									}

									$grand_total_components = $grand_total_components + 1; // in theory we should be able to add these mathmatically rather than needing to count them...

								} // end get BOM part / part rev data

					 			 ?>

					 			<tr>
					 			  <td class="text-center">
									<img src="<?php
										echo $component_photo_location;
									?>" class="rounded img-responsive" alt="<?php
										echo $rev_part_join_part_code;
									?> - <?php
										echo $rev_part_join_name_EN;
										if (($rev_part_join_name_CN!='')&&($rev_part_join_name_CN!='中文名')) {
											echo " / " . $rev_part_join_name_CN;
										}
									?>" style="width:100px;" />
								  </td>
					 			  <td>
					 			  	<a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>">
					 			  		<?php echo $rev_part_join_part_code; ?>
					 			  	</a>
					 			  </td>
					 			  <td>
					 			  	<a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>">
					 			  		<?php
					 			  			echo $rev_part_join_name_EN;
					 			  			if (($rev_part_join_name_CN!='')&&($rev_part_join_name_CN!='中文名')) {
					 			  				echo " / " . $rev_part_join_name_CN;
					 			  			}
					 			  		?>
					 			  	</a>
					 			  </td>
					 			  <td>
					 			  	<a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>" class="btn btn-xs btn-warning" title="Rev #: <?php echo $rev_part_join_revision_ID; ?>">
					 			  		<?php echo $rev_part_join_rev_num; ?>
					 			  	</a>
					 			  </td>
					 			  <td>
					 			  	<?php

					 			  		echo $component_type_EN;
					 			  		if (($component_type_CN!='')&&($component_type_EN!='中文名')) {
					 			  			echo " / " . $component_type_CN;
					 			  		}

					 			  		// echo 'type ID = ' . $rev_part_join_part_type_ID . '<br />';

					 			  		// NOW FIND OUT IF IT'S AN ASSEMBLY, IN WHICH CASE LINK TO THE NEXT BOM!
					 			  		if ($rev_part_join_part_type_ID == 10) {

												// GO GET THE CHILD INFO!
												$get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $record_id . " AND `part_rev_ID` = " . $rev_part_join_revision_ID . "  ORDER BY `entry_order` ASC";

												$result_get_child_BOM = mysqli_query($con,$get_child_BOM_SQL);

												// while loop
												while($row_get_child_BOM = mysqli_fetch_array($result_get_child_BOM)) {
													$child_BOM_ID 				= $row_get_child_BOM['ID'];
													$child_BOM_part_rev_ID 		= $row_get_child_BOM['part_rev_ID'];
													$child_BOM_date_entered 	= $row_get_child_BOM['date_entered'];
													$child_BOM_record_status 	= $row_get_child_BOM['record_status'];
													$child_BOM_created_by 		= $row_get_child_BOM['created_by'];
													$child_BOM_type 			= $row_get_child_BOM['BOM_type'];
													$child_BOM_parent_BOM_ID 	= $row_get_child_BOM_list['parent_BOM_ID'];

												}

												// LINK TO BOM?!
												echo '<br /><a href="BOM_view.php?id=' . $child_BOM_ID . '">VIEW BOM</a>';


					 			  		 }

					 			  	?>
					 			  </td>
					 			</tr>
					 			<?php

					 			} // close the loop...

					 			?>
					 		  </tbody>

					 		  <tfoot>
					 			<tr>
					 			  <th colspan="5">
					 			  	TOTAL COMPONENTS: <?php echo $total_components; ?><br />
					 			  	TOTAL SUB-ASSEMBLIES: <?php echo $total_assemblies; ?><br />
					 			  	TOTAL ITEMS: <?php echo $grand_total_components; ?>
					 			  </th>
					 			</tr>
 				 			  </tfoot>
					 		</table>
					 	   </div>



					</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="BOM.php">(View All)</a>
										</div>
								  </div>
								</div>
							</section>

						</div>

						<div class="clearfix">&nbsp;</div>




									<div class="row">

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="va-middle">Batch History</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

					<?php
					// firstly, let's make sure we have some batches to display...

					// count variants for this purchase order
        			$count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_rev` = " . $rev_body_id;
        			$count_batches_query 	= mysqli_query($con, $count_batches_sql);
        			$count_batches_row 		= mysqli_fetch_row($count_batches_query);
        			$total_batches 			= $count_batches_row[0];

					if ($total_batches == 0) {
						?><center>No batches found. <a href="part_batch_add.php?new_record_id=<?php echo $rev_body_id; ?>">Add one?</a></center><?php
					}
					else { // FOUND BATCHES - SHOW THEM!

					?>



					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					   <thead>
						  <tr>
							<th class="text-center">Batch #</th>
							<th class="text-center">P.O. #</th>
							<th class="text-center">P.O. Date</th>
							<th class="text-center">QTY In</th>
							<th class="text-center">QTY Out</th>
							<th class="text-center">Batch Balance</th>
						  </tr>
					  </thead>
					  <tbody>

					  <!-- START DATASET -->
					  <?php

					  // get batch list

					  $grand_total_in 			= 0; // default
					  $grand_total_out 			= 0; // default
					  $grand_total_remaining 	= 0; // default
					  $total_batches 			= 0; // default

					  if ($sort == '') {
					  	$sort_SQL = " ORDER BY `PO_ID` ASC";
					  }
					  else if ($sort == 'batch_num') {
					  	$sort_SQL = " ORDER BY `batch_number` " . $sort_dir;
					  }
					  else if ($sort == 'PO_ID') {
					  	$sort_SQL = " ORDER BY `PO_ID` " . $sort_dir;
					  }
					  else if ($sort == 'part_ID') {
					  	$sort_SQL = " ORDER BY `part_ID` " . $sort_dir;
					  }

					  $get_batch_SQL = "SELECT * FROM  `part_batch` WHERE `part_rev` = ".$rev_body_id."" . $sort_SQL;
						$result_get_batch = mysqli_query($con,$get_batch_SQL);
						// while loop
						while($row_get_batch = mysqli_fetch_array($result_get_batch)) {

								// now print each record:
								$batch_id 		= $row_get_batch['ID'];
								$PO_ID 			= $row_get_batch['PO_ID'];
								$part_ID 		= $row_get_batch['part_ID'];
								$batch_number 	= $row_get_batch['batch_number'];
								$part_rev 		= $row_get_batch['part_rev'];

								// GET PART DETAILS:
								$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_ID;
								$result_get_part = mysqli_query($con,$get_part_SQL);
								// while loop
								while($row_get_part = mysqli_fetch_array($result_get_part)) {

									// now print each result to a variable:
									$part_id 		= $row_get_part['ID'];
									$part_code 		= $row_get_part['part_code'];
									$part_name_EN 	= $row_get_part['name_EN'];
									$part_name_CN 	= $row_get_part['name_CN'];

								}


								// GET P.O. DETAILS:
								$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $PO_ID;
								$result_get_PO = mysqli_query($con,$get_PO_SQL);
								// while loop
								while($row_get_PO = mysqli_fetch_array($result_get_PO)) {

									// now print each record:
									$PO_id 				= $row_get_PO['ID'];
									$PO_number 			= $row_get_PO['PO_number'];
									$PO_created_date 	= $row_get_PO['created_date'];
									$PO_description 	= $row_get_PO['description'];

								} // end while loop

								// get part revision info:
								$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev;
								$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
								// while loop
								while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

									// now print each record:
									$rev_id 		= $row_get_part_rev['ID'];
									$rev_part_id 	= $row_get_part_rev['part_ID'];
									$rev_number 	= $row_get_part_rev['revision_number'];
									$rev_remarks 	= $row_get_part_rev['remarks'];
									$rev_date 		= $row_get_part_rev['date_approved'];
									$rev_user 		= $row_get_part_rev['user_ID'];

								}
								
								// UPDATE: Let's calculate total in-bound QTY over all time
								
								$get_in_and_out_totals_SQL = "SELECT sum(`amount_in`), sum(`amount_out`) FROM `part_batch_movement` WHERE `part_batch_ID` = '" . $batch_id . "' AND `record_status` = 2";
								$result_get_in_and_out_totals = mysqli_query($con,$get_in_and_out_totals_SQL);
								// while loop
								while($row_get_in_and_out_totals = mysqli_fetch_array($result_get_in_and_out_totals)) {

									// now print each result:
									$total_qty_in 	= $row_get_in_and_out_totals['sum(`amount_in`)'];
									$total_qty_out 	= $row_get_in_and_out_totals['sum(`amount_out`)'];

								}
								
								// THEN let's calculate how many are in stock at present
								$qty_remaining = $total_qty_in - $total_qty_out;

					// NOW LET'S DO THIS!

					  ?>
					  <tr<?php if ($batch_id == $_REQUEST['new_record_id']) { ?> class="success"<?php } ?>>
					    <td class="text-center"><a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $batch_number; ?></a></td>
					    <td class="text-center"><a href="purchase_order_view.php?id=<?php echo $PO_id; ?>"><?php echo $PO_number; ?></a></td>
					    <td class="text-center"><?php echo date("Y-m-d", strtotime($PO_created_date)); ?></td>
					    <td class="text-center"><?php echo number_format($total_qty_in); ?></td>
					    <td class="text-center"><?php echo number_format($total_qty_out); ?></td>
					    <td class="text-center"><?php echo number_format($qty_remaining); ?></td>
					  </tr>
					  <?php
					  
					  $grand_total_in 			= $grand_total_in + $total_qty_in;
					  $grand_total_out 			= $grand_total_out + $total_qty_out;
					  $grand_total_remaining 	= $grand_total_remaining + $qty_remaining;
					  // $grand_total_remaining	= $grand_total_in - $grand_total_out; // should be the same as line above! :)
					  $total_batches 			= $total_batches + 1;
					  } // END GET BATCH 'WHILE' LOOP

					  ?>
					  <!-- END DATASET -->

					  </tbody>

					  <tfoot>
						  <tr>
							<th colspan="3">Total batches for rev. <?php echo $rev_number; ?>: <?php echo $total_batches ;


												// now count the total batches for ALL revisions:
												$count_j_batches_sql 	= "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_ID` = " . $part_ID;
												$count_j_batches_query 	= mysqli_query($con, $count_j_batches_sql);
												$count_j_batches_row 	= mysqli_fetch_row($count_j_batches_query);
												$total_j_batches 		= $count_j_batches_row[0];

												if ($total_j_batches > $total_batches) {

													// found even more batches - link to the entire list!
													echo ' <span style="font-weight:normal;">(<a href="batch_log.php?part_id='.$part_ID.'">VIEW ALL ' . $total_j_batches . ' BATCHES</a>)</span>';
												 } ?>

							</th>
							<th class="text-center"><?php echo number_format($grand_total_in); ?></th>
							<th class="text-center"><?php echo number_format($grand_total_out); ?></th>
							<th class="text-center"><?php echo number_format($grand_total_remaining); ?></th>
						  </tr>
					  </tfoot>

					 </table>
					</div>

					<?php

					} // END FOUND BATCHES ELSE STATEMENT

					?>


					</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="batch_log.php?part_id=<?php echo $part_ID; ?>">(View All)</a>
										</div>
								  </div>
								</div>
							</section>

						</div>
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
					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
