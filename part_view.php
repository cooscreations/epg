<?php
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
/*//////*/      session_start();        /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//  now check the user is OK to view this page  //
/*//////*/ require ('page_access.php'); /*//////*/
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
	
	// now check to make sure the part exists:

	// check for revisions. If there are none, we will create one!
	$count_parts_sql 	= "SELECT COUNT( ID ) FROM  `parts` WHERE  `ID` = " . $record_id;
	// echo $count_parts_sql;
	$count_parts_query 	= mysqli_query($con, $count_parts_sql);
	$count_parts_row 	= mysqli_fetch_row($count_parts_query);
	$total_parts 		= $count_parts_row[0];

	if ($total_parts == 0) {
		// no id = nothing to see here!
		// echo '<h1>exception triggered!</h1>';
		header("Location: parts.php?msg=NG&action=view&error=no_id&exception=part_not_found&id=" . $record_id . "");
		exit();
	}
}
else if (isset($_REQUEST['rev_id'])) { 
	// for whatever reason, we have a REVISION ID but no part ID - we can get the part ID from the revision ID!
	$find_rev_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $_REQUEST['rev_id'] . "'";
	// echo "<h1>SQL: " . $find_rev_part_ID_SQL . "</h1>";
	$result_find_rev_part_ID = mysqli_query($con,$find_rev_part_ID_SQL);

	// while loop
	while($row_find_rev_part_ID = mysqli_fetch_array($result_find_rev_part_ID)) {
		$rev_part_ID = $row_find_rev_part_ID['part_ID'];
	}
	// echo "<h1>REDIRECTING TO part_view.php?id=" . $rev_part_ID . "&rev_id=" . $_REQUEST['rev_id'] ."</h1>";
	// now re-load the page:
	header("Location: part_view.php?id=" . $rev_part_ID . "&rev_id=" . $_REQUEST['rev_id'] ."");
	exit();
}
else { // no id = nothing to see here!
	header("Location: parts.php?msg=NG&action=view&error=no_id");
	exit();
}



if (isset($_REQUEST['rev_id'])) {
	// DEFAULT REVISION IS SPECIFIED - show this one!
	$rev_to_show = $_REQUEST['rev_id'];
}
else { $rev_to_show = 0; }

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
    $count_revs_sql 	= "SELECT COUNT( ID ) FROM  `part_revisions` WHERE  `part_ID` = '" . $record_id . "' AND `record_status` = '2'";
    $count_revs_query 	= mysqli_query($con, $count_revs_sql);
    $count_revs_row 	= mysqli_fetch_row($count_revs_query);
    $total_revs 		= $count_revs_row[0];

	if ($total_revs == 0) {
	
		if (isset($_REQUEST['first_rev_number'])) {
			$first_rev_number = checkaddslashes($_REQUEST['first_rev_number']);
		}
		else {
			$first_rev_number = "A";
		}
	
	
		$add_rev_SQL = "INSERT INTO `part_revisions`(`ID`, `part_ID`, `revision_number`, `remarks`, `date_approved`, `user_ID`, `price_USD`, `weight_g`, `status_ID`, `material_ID`, `treatment_ID`, `treatment_notes`, `record_status`) VALUES (NULL,'".$record_id."','" . $first_rev_number . "','No revisions found, so we auto-generated Revision A','" . date("Y-m-d H:i:s") . "','2','0.0100','0.0100','1','0','0','No treatment notes','2')";

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
			echo "<h4>Failed to update existing part revision record with SQL: <br />" . $add_rev_SQL . "</h4>";
		}
	}

} // end get part info WHILE loop


// pull the header and template stuff:
pagehead($page_id);

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Part Profile - <?php part_num($part_ID, 0); ?> - <?php part_name($part_ID, 0); ?></h2>

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

							$get_j_parts_SQL = "SELECT * FROM `parts` WHERE `record_status` = '2'";
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
								$get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE  `part_ID` ='" . $part_ID . "' AND `record_status` = '2' ORDER BY `revision_number` DESC";

								$loop_count = 0;

								$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
								// while loop
								while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {

									$loop_count = $loop_count + 1;

									// now print what we need:
									$rev_id 	= $row_get_part_rev['ID'];
									$rev_number = $row_get_part_rev['revision_number'];

									?>

							<li class="<?php if ((($loop_count == 1)&&($rev_to_show == 0))||($rev_to_show == $rev_id)) { ?>active<?php } ?>">
								<a href="#rev_<?php echo $rev_id; ?>" data-toggle="tab" aria-expanded="true" title="Rev. #: <?php echo $rev_id; ?>"><i class="fa fa-star<?php if ($loop_count != 1) { ?>-o<?php } ?>"></i> Rev. <?php echo $rev_number; ?></a>
							</li>

								<?php
								} // END GET PART REVISIONS TO BUILD TAB LIST...
								?>
								
							<li>
								<a href="part_revision_add.php?part_ID=<?php echo $part_ID; ?>" class="text-success" title="Add a New Revision"><i class="fa fa-plus"></i> NEW REV.</a>
							</li>

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
									
	
									// check for critical dimensions.
									$count_crit_dims_sql 	= "SELECT COUNT( ID ) FROM `part_rev_critical_dimensions` WHERE `part_revision_ID` = '" . $rev_body_id . "'";
									$count_crit_dims_query 	= mysqli_query($con, $count_crit_dims_sql);
									$count_crit_dims_row 	= mysqli_fetch_row($count_crit_dims_query);
									$total_crit_dims 		= $count_crit_dims_row[0];

									?>

							<div id="rev_<?php echo $rev_body_id; ?>" class="tab-pane <?php if ((($loop_body_count == 1)&&($rev_to_show == 0))||($rev_to_show == $rev_body_id)) { ?>active<?php } ?>">
																

								<div class="row">

									<div class="col-md-4 col-lg-3">



									<section class="panel">
								<div class="panel-body">
									<div class="thumb-info mb-md">
										<!--<img src="<?php echo $rev_photo_location; ?>" class="rounded img-responsive" alt="<?php echo $part_code; ?> - <?php echo $name_EN; if (($name_CN!='')&&($name_CN!='中文名')) { ?> / <?php echo $name_CN; } ?>">-->
										<?php part_img($rev_body_id, 0, 250); ?>
										<div class="thumb-info-title">
											<span class="thumb-info-inner"><?php echo $part_code; ?></span>
											<span class="thumb-info-type">Rev. <?php echo $rev_body_number; ?></span>
										</div>
									</div>


									<h6 class="text-muted">About</h6>
									<ul>
									  <li><strong>Type:</strong> <?php echo $part_type_EN; if (($part_type_CN!='')&&($part_type_CN!='中文名')) { echo " / " . $part_type_CN; } ?></li>
									  <li><strong>Release Date:</strong> <?php echo date("Y-m-d", strtotime($rev_body_date)); ?></li>
									  <li><strong>Released By:</strong> <?php get_creator($rev_body_user); ?></li>
									  <li><strong># Critical Dimensions:</strong> <a href="part_revision_critical_dimensions.php?part_rev_id=<?php echo $rev_body_id; ?>" title="VIEW CRITICAL DIMENSIONS FOR THIS REVISION" class="btn btn-xs btn-primary"><?php echo $total_crit_dims; ?></a> <a href="part_revision_critical_dimension_add.php?part_rev_id=<?php echo $rev_body_id; ?>" class="btn btn-xs btn-success" title="ADD NEW CRITICAL DIMENSION TO THIS REVISION"><i class="fa fa-plus"></i></a></li>
									</ul>
									
									<hr />

									<!-- ********************************************************* -->
									<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
									<!-- SPECIAL NOTE | SPECIAL NOTE | SPECIAL NOTE | SPECIAL NOTE -->
									
									<!--  PLEASE NOTE: THIS ADMIN POP-UP IS DIFFERENT IN THAT IT 
									             CONTAINS OPTIONS FOR REVISION AND PART!!!         -->
									
									<!-- ********************************************************* -->
			
									<?php 
			
									// VARS YOU NEED TO WATCH / CHANGE:
									$add_to_form_name 	= 'part_rev_';						// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
									$form_ID 			= $rev_body_id;						// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
									$edit_URL 			= 'part_revision_edit'; 			// REQUIRED - specify edit page URL
									$add_URL 			= 'part_revision_add.php'; 			// REQURED - specify add page URL
									$table_name 		= 'part_revisions';					// REQUIRED - which table are we updating?
									$src_page 			= $this_file;						// REQUIRED - this SHOULD be coming from page_functions.php
									$add_VAR 			= 'part_ID=' . $record_id; 			// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
									?>
									<div class="row text-center">
										<a class="modal-with-form btn btn-default tet-center" href="#modalForm_<?php 
				
											echo $add_to_form_name; 
											echo $form_ID; 
				
										?>"><i class="fa fa-gear"></i> ADMIN OPTIONS</a>
									</div>

										<!-- Modal Form -->
										<div id="modalForm_<?php 
				
											echo $add_to_form_name; 
											echo $form_ID; 
					
										?>" class="modal-block modal-block-primary mfp-hide">
											<section class="panel">
												<header class="panel-heading">
													<h2 class="panel-title">Admin Options</h2>
												</header>
												<div class="panel-body">
				
													<div class="table-responsive">
													 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
													 <thead>
														<tr>
															<th class="text-left" colspan="2">Action</th>
															<th>Decsription</th>
														</tr>
													  </thead>
													  <tbody>
													  
													  
													  <tr>
														  <td>Add Photo</td>
														  <td>
															<a class="mb-xs mt-xs mr-xs btn btn-primary" href="upload_file.php?lookup_ID=<?php echo $form_ID; ?>&table=part_revisions" target="_blank" title="Click here to add a new photo (New Window)"><i class="fa fa-file-photo-o"></i></a>
														  </td>
														  <td>Add a new photo</td>
														</tr>
														<tr>
														  <td>&nbsp;</td>
														  <td>&nbsp;</td>
														  <td>&nbsp;</td>
														</tr>
														<tr>
														  <td>EDIT REV. # <?php echo $rev_body_number; ?></td>
														  <td>
															<a href="<?php 
																echo $edit_URL; 
															?>.php?id=<?php 
																echo $form_ID; 
															?>" class="mb-xs mt-xs mr-xs btn btn-warning">
																<i class="fa fa-pencil" stlye="color: #999"></i>
															</a>
														  </td>
														  <td>Edit this revision</td>
														</tr>
														<tr>
														  <td>DELETE REV. # <?php echo $rev_body_number; ?></td>
														  <td>
															<a href="record_delete_do.php?table_name=<?php 
																echo $table_name; 
															?>&src_page=<?php 
																echo $src_page; 
															?>&id=<?php 
																echo $form_ID;
																echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
															?>" class="mb-xs mt-xs mr-xs btn btn-danger">
																<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
															</a>
														  </td>
														  <td>Delete this revision</td>
														</tr>
														<tr>
														  <td>ADD PART REVISION</td>
														  <td>
															<a href="<?php 
																echo $add_URL; 
																echo '?' . $add_VAR;  // NOTE THE LEADING '?' <<<
															?>" class="mb-xs mt-xs mr-xs btn btn-success">
																<i class="fa fa-plus" stlye="color: #999"></i>
															</a>
														  </td>
														  <td>Add a new part revision to this part record</td>
														</tr>
														<tr>
														  <td>&nbsp;</td>
														  <td>&nbsp;</td>
														  <td>&nbsp;</td>
														</tr>
														<tr>
														  <td>EDIT PART # <?php echo $part_code; ?></td>
														  <td>
															<a href="part_edit.php?id=<?php echo $record_id ?>" class="mb-xs mt-xs mr-xs btn btn-warning">
																<i class="fa fa-pencil" stlye="color: #999"></i>
															</a>
														  </td>
														  <td>Edit this part record</td>
														</tr>
														<tr>
														  <td>DELETE PART # <?php echo $part_code; ?></td>
														  <td>
															<a href="record_delete_do.php?table_name=parts&src_page=<?php echo $src_page; ?>&id=<?php echo $record_id ?>" class="mb-xs mt-xs mr-xs btn btn-danger">
																<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
															</a>
														  </td>
														  <td>Delete this part record <em class="text-danger">(not recommended!)</em></td>
														</tr>
														<tr>
														  <td>ADD NEW PART</td>
														  <td>
															<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success">
																<i class="fa fa-plus" stlye="color: #999"></i>
															</a>
														  </td>
														  <td>Add a new part to the system</td>
														</tr>
													  </tbody>
													  <tfoot>
														<tr>
														  <td>&nbsp;</td>
														  <td>&nbsp;</td>
														  <td>&nbsp;</td>
														</tr>
													  </tfoot>
													  </table>
													</div><!-- end of responsive table -->	
				
												</div><!-- end panel body -->
												<footer class="panel-footer">
													<div class="row">
														<div class="col-md-12 text-left">
															<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
														</div>
													</div>
												</footer>
											</section>
										</div>
		
									<!-- ********************************************************* -->
									<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
									<!-- ********************************************************* -->

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
									
									<?php 
									if ( ( $part_default_supplier_ID != 0 ) && ( $part_default_supplier_ID != '' ) ) {
									?>
											<a href="supplier_view.php?id=<?php echo $sup_ID; ?>" title="Click to view this vendor profile" class="text-uppercase text-muted">
												(View Details)
											</a>
									<?php 
									}
									else {
									?>
											<a href="part_edit.php?id=<?php echo $record_id; ?>" title="Click to add a default vendor to this part profile now" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
									<?php
									}
									
									?>
										</div>
								  </div>
								</div>
							</section>


							<!-- END DEFAULT SUPPLIER PANEL -->



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
													<span class="va-middle">Material<?php if ($total_part_to_mat_maps > 1) { echo 's'; } ?></span>
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
																<span class="message truncate"><a href="material_view.php?id=<?php echo $material_ID; ?>&rev_id=<?php echo $rev_body_id; ?>&part_id=<?php echo $record_id; ?>">View Material Record</a></span>
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
																<span class="message truncate"><a href="material_to_part_map.php?part_id=<?php echo $record_id; ?>&rev_id=<?php echo $rev_body_id; ?>" class="btn btn-xs btn-success"><iclass="fa fa-plus-square"></i> Add Now</a></span>
															</li>
															<?php
														} // end of no records found 'total_part_to_mat_maps' = 0
													  ?>
													</ul>
												</div>
											  <div class="panel-footer">
												<div class="text-right">
														<a class="btn btn-warning btn-xs" href="material_to_part_map.php?part_id=<?php echo $record_id; ?>&rev_id=<?php echo $rev_body_id; ?>" title="Click here to view / edit all materials"><i class="fa fa-pencil" title="EDIT / 改变"></i></a>
													</div>
											  </div>
											</div>
										</section>

							<?php
							} // end of check for if 'type_ID' != 10
							else {
								?>
								<section class="panel">
											<header class="panel-heading">
												<div class="panel-actions">
													<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
													<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
												</div>

												<h2 class="panel-title">
													<span class="va-middle">Supplier / Material Note</span>
												</h2>
											</header>
											<div class="panel-body">
												<div class="content">
													<p class="text-danger">For supplier & material info, please refer to each individual component.</p>
												</div>
											  <div class="panel-footer">
												<div class="text-right">
														->
													</div>
											  </div>
											</div>
										</section>
								<?php
							}
							?>
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

								  <?php add_button($rev_body_id, 'upload_file', 'lookup_ID', 'Add a new document to this part revision', '&table=part_revisions'); ?>
										<div class="table-responsive">
										 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
										   <thead>
											<tr>
												<th class="text-center">Type</th>
												<th class="text-center">Name</th>
												<th class="text-center">Rev.</th>
											 </tr>
											</thead>
											<tbody>
											<?php 
											$total_docs = 0;
											$get_doc_SQL = "SELECT * FROM  `documents` WHERE `record_status` = '2' AND `lookup_table` = 'part_revisions' AND `lookup_ID` = '" . $rev_body_id . "'";
											$result_get_doc = mysqli_query($con,$get_doc_SQL);
											// while loop
											while($row_get_doc = mysqli_fetch_array($result_get_doc)) {

													// now print each record:
													$doc_id 				= $row_get_doc['ID']; // same as $record_id
													$doc_name_EN 			= $row_get_doc['name_EN'];
													$doc_name_CN 			= $row_get_doc['name_CN'];
													$doc_filename 			= $row_get_doc['filename'];
													$doc_filetype_ID 		= $row_get_doc['filetype_ID'];
													$doc_file_location 		= $row_get_doc['file_location'];
													$doc_lookup_table 		= $row_get_doc['lookup_table'];
													$doc_lookup_ID 			= $row_get_doc['lookup_ID'];
													$doc_document_category 	= $row_get_doc['document_category'];
													$doc_record_status 		= $row_get_doc['record_status'];
													$doc_created_by 		= $row_get_doc['created_by'];
													$doc_date_created 		= $row_get_doc['date_created'];
													$doc_filesize_bytes 	= $row_get_doc['filesize_bytes'];
													$doc_document_icon 		= $row_get_doc['document_icon'];
													$doc_document_remarks 	= $row_get_doc['document_remarks'];
													$doc_doc_revision 		= $row_get_doc['doc_revision'];
		
													// SPECIFY FULL FILE LOCATION:
		
													if ($doc_document_category == 5) {
														// this is a part photo -  let's link to it
														$file_path = '';
													}
													else {
														// DEFAULT?
														$file_path = '';
													}
													// now build the link:
													$full_file_path = 'http://120.24.71.207/' . $file_path .  $doc_file_location . '/' . $doc_filename;
													// echo '<h4>Path: ' . $full_file_path . '</h4>';
		
													// GET DOC CATEGORY
		
													$get_this_doc_cat_SQL = "SELECT * FROM `document_categories` WHERE `ID` = '" . $doc_document_category . "'";
													$result_get_this_doc_cat = mysqli_query($con,$get_this_doc_cat_SQL);
													// while loop
													while($row_get_this_doc_cat = mysqli_fetch_array($result_get_this_doc_cat)) {

															// now print each record:
															$doc_cat_id 			= $row_get_this_doc_cat['ID'];
															$doc_cat_name_EN 		= $row_get_this_doc_cat['name_EN'];
															$doc_cat_name_CN 		= $row_get_this_doc_cat['name_CN'];
															$doc_cat_record_status 	= $row_get_this_doc_cat['record_status'];
				
													}
		
													// GET FILETYPE
		
													$get_this_filetype_SQL = "SELECT * FROM `document_filetype` WHERE `ID` = '" . $doc_filetype_ID . "'";
													$result_get_this_filetype = mysqli_query($con,$get_this_filetype_SQL);
													// while loop
													while($row_get_this_filetype = mysqli_fetch_array($result_get_this_filetype)) {

															// now print each record:
															$filetype_id 			= $row_get_this_filetype['ID'];
															$filetype_type_name_EN 	= $row_get_this_filetype['type_name_EN'];
															$filetype_type_name_CN 	= $row_get_this_filetype['type_name_CN'];
															$filetype_default_icon 	= $row_get_this_filetype['default_icon'];
															$filetype_record_status = $row_get_this_filetype['record_status'];
															$filetype_created_by 	= $row_get_this_filetype['created_by'];
															$filetype_created_date 	= $row_get_this_filetype['created_date'];
				
													}
											?>
											<tr>
											  <td class="text-center"><i class="fa fa-<?php echo $doc_document_icon; ?>"></i></td>
											  <td><a href="document_view.php?id=<?php echo $doc_id; ?>"><?php 
											  	echo $doc_name_EN; 
											  	if (($doc_name_CN!='')&&($doc_name_CN!='中文名')) {
											  		echo $doc_name_CN;
											  	}
											  	?></a></td>
											  <td class="text-center"><?php echo $doc_doc_revision; ?></td>
											</tr>
											<?php 
												$total_docs = $total_docs + 1;
											} // END GET DOCS
											?>


											<tr>
											  <th colspan="3">TOTAL DOCUMENTS: <?php echo $total_docs; ?></th>
											</tr>
										  </tbody>
										</table>
									   </div>
								  <?php add_button($rev_body_id, 'upload_file', 'lookup_ID', 'Add a new document to this part revision', '&table=part_revisions'); ?>

							</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="documents.php">(View All)</a>
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
										<span class="va-middle">Batch History</span>
										<a name="batch_history_<?php echo $rev_body_id; ?>"></a>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

					<?php
					// firstly, let's make sure we have some batches to display...

					// count variants for this purchase order
        			$count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_rev` = '" . $rev_body_id . "' AND `record_status` = '2'";
        			$count_batches_query 	= mysqli_query($con, $count_batches_sql);
        			$count_batches_row 		= mysqli_fetch_row($count_batches_query);
        			$total_batches 			= $count_batches_row[0];

					if ($total_batches == 0) {
						?><center>No batches found. <a href="part_batch_add.php?new_record_id=<?php echo $rev_body_id; ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add one? <i class="fa fa-plus"></i></a></center><?php
					}
					else { // FOUND BATCHES - SHOW THEM!

					?>


					<?php add_button($rev_body_id, 'part_batch_add', 'new_record_id'); ?>

					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					   <thead>
						  <tr>
						  	<th class="text-center"><i class="fa fa-cog"></i></th>
							<th class="text-center">Batch #</th>
							<th class="text-center">P.O. #</th>
							<th class="text-center">P.O. Date</th>
							<th class="text-center">QTY In</th>
							<th class="text-center">QTY Out</th>
							<th class="text-center">Batch Balance</th>
							<th class="text-center">Status</th>
							<th class="text-center">Remarks</th>
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

					  $get_batch_SQL = "SELECT * FROM  `part_batch` WHERE `record_status` = '2' AND `part_rev` = ".$rev_body_id."" . $sort_SQL;
						$result_get_batch = mysqli_query($con,$get_batch_SQL);
						// while loop
						while($row_get_batch = mysqli_fetch_array($result_get_batch)) {

								// now print each record:
								$batch_id 		= $row_get_batch['ID'];
								$PO_ID 			= $row_get_batch['PO_ID'];
								$part_ID 		= $row_get_batch['part_ID'];
								$batch_number 	= $row_get_batch['batch_number'];
								$part_rev 		= $row_get_batch['part_rev'];
								
								// get first INCOMING Batch amount & status:
								$get_first_movement_SQL = "SELECT * FROM `part_batch_movement` WHERE `part_batch_ID` = '" . $batch_id . "' AND `amount_in` > 0 ORDER BY `date` ASC LIMIT 0,1";
								$result_get_first_movement = mysqli_query($con,$get_first_movement_SQL);
								// while loop
								while($row_get_first_movement = mysqli_fetch_array($result_get_first_movement)) {

										// now print each record:
										$first_movement_batch_movement_id 		= $row_get_first_movement['ID'];
										$first_movement_amount_in 				= $row_get_first_movement['amount_in'];
										$first_movement_amount_out 				= $row_get_first_movement['amount_out'];
										$first_movement_part_batch_status_ID 	= $row_get_first_movement['part_batch_status_ID'];
										$first_movement_remarks 				= $row_get_first_movement['remarks'];
										$first_movement_user_ID 				= $row_get_first_movement['user_ID'];
										$first_movement_date 					= $row_get_first_movement['date'];
										$first_movement_record_status 			= $row_get_first_movement['record_status'];
										
										// now get the movement status

										$get_mvmnt_status_SQL = "SELECT * FROM  `part_batch_status` WHERE  `ID` =" . $first_movement_part_batch_status_ID;

										$result_get_mvmnt_status = mysqli_query($con,$get_mvmnt_status_SQL);
										// while loop
										while($row_get_mvmnt_status = mysqli_fetch_array($result_get_mvmnt_status)) {

												// now print each record:
												$mvmnt_status_name_EN 	= $row_get_mvmnt_status['name_EN'];
												$mvmnt_status_name_CN 	= $row_get_mvmnt_status['name_CN'];
												$mvmnt_status_desc 		= $row_get_mvmnt_status['desc'];
												$mvmnt_status_icon 		= $row_get_mvmnt_status['icon'];
												$mvmnt_status_color 	= $row_get_mvmnt_status['color'];
										}
								
								} // end get first batch movement
								

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
							<td class="text-center">
					
							<!-- ********************************************************* -->
							<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
							<!-- ********************************************************* -->
			
							<?php 
			
							// VARS YOU NEED TO WATCH / CHANGE:
							$add_to_form_name 	= 'batch_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
							$form_ID 			= $batch_id;				// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
							$edit_URL 			= 'part_batch_edit'; 			// REQUIRED - specify edit page URL
							$add_URL 			= 'part_batch_add'; 				// REQURED - specify add page URL
							$table_name 		= 'part_batch';				// REQUIRED - which table are we updating?
							$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
							$add_VAR 			= ''; 						// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
							?>
	 
								<a class="modal-with-form btn btn-default" href="#modalForm_<?php 
				
									echo $add_to_form_name; 
									echo $form_ID; 
				
								?>"><i class="fa fa-gear"></i></a>

								<!-- Modal Form -->
								<div id="modalForm_<?php 
				
									echo $add_to_form_name; 
									echo $form_ID; 
					
								?>" class="modal-block modal-block-primary mfp-hide">
									<section class="panel">
										<header class="panel-heading">
											<h2 class="panel-title">Admin Options</h2>
										</header>
										<div class="panel-body">
				
											<div class="table-responsive">
											 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
											 <thead>
												<tr>
													<th class="text-left" colspan="2">Action</th>
													<th>Decsription</th>
												</tr>
											  </thead>
											  <tbody>
												<tr>
												  <td>EDIT</td>
												  <td>
													<a href="<?php 
														echo $edit_URL; 
													?>.php?id=<?php 
														echo $form_ID; 
													?>" class="mb-xs mt-xs mr-xs btn btn-warning">
														<i class="fa fa-pencil" stlye="color: #999"></i>
													</a>
												  </td>
												  <td>Edit this record</td>
												</tr>
												<tr>
												  <td>DELETE</td>
												  <td>
													<a href="record_delete_do.php?table_name=<?php 
														echo $table_name; 
													?>&src_page=<?php 
														echo $src_page; 
													?>&id=<?php 
														echo $form_ID;
														echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
													?>" class="mb-xs mt-xs mr-xs btn btn-danger">
														<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
													</a>
												  </td>
												  <td>Delete this record</td>
												</tr>
												<tr>
												  <td>ADD</td>
												  <td>
													<a href="<?php 
														echo $add_URL; 
														echo '.php?' . $add_VAR;  // NOTE THE LEADING '?' <<<
													?>" class="mb-xs mt-xs mr-xs btn btn-success">
														<i class="fa fa-plus" stlye="color: #999"></i>
													</a>
												  </td>
												  <td>Add a similar item to this table</td>
												</tr>
											  </tbody>
											  <tfoot>
												<tr>
												  <td>&nbsp;</td>
												  <td>&nbsp;</td>
												  <td>&nbsp;</td>
												</tr>
											  </tfoot>
											  </table>
											</div><!-- end of responsive table -->	
				
										</div><!-- end panel body -->
										<footer class="panel-footer">
											<div class="row">
												<div class="col-md-12 text-left">
													<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
												</div>
											</div>
										</footer>
									</section>
								</div>
		
							<!-- ********************************************************* -->
							<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
							<!-- ********************************************************* -->
						</td>
					    <td class="text-center"><a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $batch_number; ?></a></td>
					    <td class="text-center"><a href="purchase_order_view.php?id=<?php echo $PO_id; ?>"><?php echo $PO_number; ?></a></td>
					    <td class="text-center"><?php echo date("Y-m-d", strtotime($PO_created_date)); ?></td>
					    <td class="text-right"><?php echo number_format($total_qty_in); ?></td>
					    <td class="text-right"><?php echo number_format($total_qty_out); ?></td>
					    <td class="text-right"><?php echo number_format($qty_remaining); ?></td>
					    <td><span class="button btn-xs btn-<?php echo $mvmnt_status_color; ?>"><i class="fa <?php echo $mvmnt_status_icon; ?>"></i> <?php echo $mvmnt_status_name_EN; ?> / <?php echo $mvmnt_status_name_CN; ?></span></td>
					    <td><?php echo $first_movement_remarks; ?></td>
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
							<th colspan="4">Total batches for rev. <?php echo $rev_number; ?>: <?php echo $total_batches ;


												// now count the total batches for ALL revisions:
												$count_j_batches_sql 	= "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_ID` = " . $part_ID . " AND `record_status` = '2'";
												$count_j_batches_query 	= mysqli_query($con, $count_j_batches_sql);
												$count_j_batches_row 	= mysqli_fetch_row($count_j_batches_query);
												$total_j_batches 		= $count_j_batches_row[0];

												if ($total_j_batches > $total_batches) {

													// found even more batches - link to the entire list!
													echo ' <span style="font-weight:normal;">(<a href="batch_log.php?part_id='.$part_ID.'">VIEW ALL ' . $total_j_batches . ' BATCHES</a>)</span>';
												 } ?>

							</th>
							<th class="text-right"><?php echo number_format($grand_total_in); ?></th>
							<th class="text-right"><?php echo number_format($grand_total_out); ?></th>
							<th class="text-right"><?php echo number_format($grand_total_remaining); ?></th>
					    	<th>&nbsp;</th>
					    	<th>&nbsp;</th>
						  </tr>
					  </tfoot>

					 </table>
					</div>
					
					<?php add_button($rev_body_id, 'part_batch_add', 'new_record_id'); ?>

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

					  // FIRST, let's try to get the BOM it IS or it is ON:

					  // count BOMs made for this revision
						$count_BOMs_sql = "SELECT COUNT( ID ) FROM  `product_BOM` WHERE `record_status` = 2 AND `part_rev_ID` =  '".$rev_body_id."'";
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

									$combine_part_and_rev_SQL = "SELECT 
									`parts`.`part_code`, 
									`parts`.`name_EN`, 
									`parts`.`name_CN`, 
									`parts`.`type_ID`, 
									`part_revisions`.`revision_number`, 
									`part_revisions`.`part_ID` 
									FROM  `part_revisions` 
									LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` 
									WHERE `part_revisions`.`ID` =" . $BOM_part_rev_ID . " 
									AND `part_revisions`.`record_status` = 2 
									AND `parts`.`record_status` = 2";

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
									  <a href="BOM_view.php?id=<?php echo $BOM_ID; ?>" class="btn btn-xs btn-primary">
									  	<i class="fa fa-gears"></i> BOM # <?php echo $BOM_ID; ?>
									  </a>
									</td>
									<td>
									  <?php part_num($rev_part_join_part_ID); ?>
									  
									  <?php part_name($rev_part_join_part_ID); ?>
									</td>
									<td class="text-center">
									  <?php part_rev($BOM_part_rev_ID); ?>
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


								  $get_BOM_ID_SQL = "SELECT * FROM `product_BOM_items` WHERE `record_status` = 2 AND `part_rev_ID` ='" . $rev_body_id . "'";
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
													// echo $combine_this_part_and_rev_SQL;

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
													<td class="text-center">
													  <a href="BOM_view.php?id=<?php echo $this_BOM_ID; ?>" class="btn btn-xs btn-primary"><i class="fa fa-gears"></i> BOM # <?php echo $this_BOM_ID; ?></a>
													</td>
													<td>
					     							  <?php part_num($this_rev_part_join_part_ID); ?>
									  
									  				  <?php part_name($this_rev_part_join_part_ID); ?>
													</td>
													<td class="text-center">
													  <?php part_rev($this_BOM_part_rev_ID); ?>
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
					  				<th class="text-center"><i class="fa fa-cog" title="Actions"></i></th>
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

					 			 $get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $find_BOM . " AND  `record_status` = '2' ORDER BY `entry_order` ASC";

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

									$combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $components_part_rev_ID . " AND `part_revisions`.`record_status` = '2' AND `parts`.`record_status` = '2'" . $order_by;
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
					
										<!-- ********************************************************* -->
										<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
										<!-- ********************************************************* -->
		
										<?php 
		
										// VARS YOU NEED TO WATCH / CHANGE:
										$add_to_form_name 	= 'part_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
										$form_ID 			= $rev_part_join_part_ID;	// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
										$edit_URL 			= 'part_edit'; 				// REQUIRED - specify edit page URL
										$add_URL 			= 'part_add'; 				// REQURED - specify add page URL
										$table_name 		= 'parts';					// REQUIRED - which table are we updating?
										$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
										$add_VAR 			= ''; 						// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
		
										?>
 
											<a class="modal-with-form btn btn-default" href="#modalForm_<?php 
			
												echo $add_to_form_name; 
												echo $form_ID; 
			
											?>"><i class="fa fa-gear"></i></a>

											<!-- Modal Form -->
											<div id="modalForm_<?php 
			
												echo $add_to_form_name; 
												echo $form_ID; 
				
											?>" class="modal-block modal-block-primary mfp-hide">
												<section class="panel">
													<header class="panel-heading">
														<h2 class="panel-title">Admin Options</h2>
													</header>
													<div class="panel-body">
			
														<div class="table-responsive">
														 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
														 <thead>
															<tr>
																<th class="text-left" colspan="2">Action</th>
																<th>Decsription</th>
															</tr>
														  </thead>
														  <tbody>
															<tr>
															  <td>EDIT</td>
															  <td>
																<a href="<?php 
																	echo $edit_URL; 
																?>.php?id=<?php 
																	echo $form_ID; 
																?>" class="mb-xs mt-xs mr-xs btn btn-warning">
																	<i class="fa fa-pencil" stlye="color: #999"></i>
																</a>
															  </td>
															  <td>Edit this record</td>
															</tr>
															<tr>
															  <td>DELETE</td>
															  <td>
																<a href="record_delete_do.php?table_name=<?php 
																	echo $table_name; 
																?>&src_page=<?php 
																	echo $src_page; 
																?>&id=<?php 
																	echo $form_ID;
																	echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
																?>" class="mb-xs mt-xs mr-xs btn btn-danger">
																	<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
																</a>
															  </td>
															  <td>Delete this record</td>
															</tr>
															<tr>
															  <td>ADD</td>
															  <td>
																<a href="<?php 
																	echo $add_URL; 
																	echo '.php?' . $add_VAR;  // NOTE THE LEADING '?' <<<
																?>" class="mb-xs mt-xs mr-xs btn btn-success">
																	<i class="fa fa-plus" stlye="color: #999"></i>
																</a>
															  </td>
															  <td>Add a similar item to this table</td>
															</tr>
														  </tbody>
														  <tfoot>
															<tr>
															  <td>&nbsp;</td>
															  <td>&nbsp;</td>
															  <td>&nbsp;</td>
															</tr>
														  </tfoot>
														  </table>
														</div><!-- end of responsive table -->	
			
													</div><!-- end panel body -->
													<footer class="panel-footer">
														<div class="row">
															<div class="col-md-12 text-left">
																<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
															</div>
														</div>
													</footer>
												</section>
											</div>
	
										<!-- ********************************************************* -->
										<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
										<!-- ********************************************************* -->
									</td>
					 			  <td class="text-center">
								  <?php part_img($components_part_rev_ID); ?>
								  </td>
					 			  <td class="text-center">
					 			  	<?php part_num($rev_part_join_part_ID); ?>
					 			  </td>
					 			  <td>
					 			  	<?php part_name($rev_part_join_part_ID); ?>
					 			  </td>
					 			  <td class="text-center">
					 			  	<?php part_rev($components_part_rev_ID); ?>
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
												// echo $get_child_BOM_SQL;

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
					 			  <th colspan="6">
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
