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

$page_id = 64;

if ((isset($_REQUEST['id'])&&($_REQUEST['id']!=''))) {
	$record_id = $_REQUEST['id'];
}
else { // no id = nothing to see here!
	header("Location: BOM.php?msg=NG&action=view&error=no_id");
	exit();
}

// SHOW THE FULL BOM?
if (isset($_REQUEST['view'])) {
	$view = $_REQUEST['view'];
}
else { $view = ''; }

// now get the part info:
$get_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `ID` = " . $record_id;
// echo $get_parts_SQL;

$result_get_BOM = mysqli_query($con,$get_BOM_SQL);

// while loop
while($row_get_BOM = mysqli_fetch_array($result_get_BOM)) {
	$BOM_ID = $row_get_BOM['ID']; // should be same as record ID...
	$BOM_part_rev_ID = $row_get_BOM['part_rev_ID'];
	$BOM_date_entered = $row_get_BOM['date_entered'];
	$BOM_record_status = $row_get_BOM['record_status'];
	$BOM_created_by = $row_get_BOM['created_by'];
	$BOM_type = $row_get_BOM['BOM_type'];
	$BOM_parent_BOM_ID = $row_get_BOM['parent_BOM_ID'];

		// get user
		$get_rev_user_SQL = "SELECT * FROM  `users` WHERE  `ID` =" . $BOM_created_by;
		$result_get_rev_user = mysqli_query($con,$get_rev_user_SQL);
		// while loop
		while($row_get_rev_user = mysqli_fetch_array($result_get_rev_user)) {
				// now print each record:
				$rev_user_first_name = $row_get_rev_user['first_name'];
				$rev_user_last_name = $row_get_rev_user['last_name'];
				$rev_user_name_CN = $row_get_rev_user['name_CN'];
		}

} // end get BOM info WHILE loop

// 1. NOW GET THE ASSEMBLY  / BOM TOP-LEVEL DATA (WHIH WILL BE A SINGLE PART REV LOOK-UP)

$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $BOM_part_rev_ID;
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
} // END GRAND LOOP


// NOW GET THE PART INFO:

// now get the part info:
$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $rev_body_part_id;
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
		$part_class_EN = $row_get_part_class['name_EN'];
		$part_class_CN = $row_get_part_class['name_CN'];
		$part_class_description = $row_get_part_class['description'];
		$part_class_color = $row_get_part_class['color'];
	}

} // end get part info WHILE loop

// 2. LOWER DOWN, LOOP THROUGH THE ITEMS IN THE BOM_item table...


// pull the header and template stuff:
pagehead($page_id);

?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Bill of Materials (BOM): <?php echo $part_code . " - " . $name_EN; if (($name_CN!='')&&($name_CN!='中文名')) { echo " / " . $name_CN; } ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><a href="BOM.php">All BOMs</a></li>
								<li><span>BOM</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->


					<div class="row">
						<div class="col-md-12">
						<!-- BOM JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">JUMP TO ANOTHER BOM / 看别的:</option>
                              <option value="BOM.php">View All / 看全部</option>
                              <?php

					  $j_WHERE_SQL = " WHERE `record_status` = 2";

					  $j_order_by = " ORDER BY `BOM_type` ASC";

					  $j_get_BOM_list_SQL = "SELECT * FROM `product_BOM`" . $j_WHERE_SQL . $j_order_by;
					  // echo $get_BOM_list_SQL;

					  $j_BOM_count = 0;

					  $j_result_get_BOM_list = mysqli_query($con,$j_get_BOM_list_SQL);
					  // while loop
					  while($j_row_get_BOM_list = mysqli_fetch_array($j_result_get_BOM_list)) {

					  	// GET BOM LIST:

					  	$j_BOM_ID = 				$j_row_get_BOM_list['ID'];
					  	$j_BOM_part_rev_ID = 		$j_row_get_BOM_list['part_rev_ID']; // use this to look up
					  	$j_BOM_date_entered = 		$j_row_get_BOM_list['date_entered'];
					  	$j_BOM_record_status = 		$j_row_get_BOM_list['record_status'];
					  	$j_BOM_created_by = 		$j_row_get_BOM_list['created_by'];
					  	$j_BOM_type = 				$j_row_get_BOM_list['BOM_type'];
					  	$j_BOM_parent_BOM_ID = 		$j_row_get_BOM_list['parent_BOM_ID'];

					  	/* ********* */

					  	$j_combine_part_and_rev_SQL = "SELECT `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $j_BOM_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2";

					    $j_result_get_rev_part_join = mysqli_query($con,$j_combine_part_and_rev_SQL);
					    // while loop
					    while($j_row_get_rev_part_join = mysqli_fetch_array($j_result_get_rev_part_join)) {

					  		// GET BOM LIST:

					  		$j_rev_part_join_part_code = 	$j_row_get_rev_part_join['part_code'];
							$j_rev_part_join_name_EN = 		$j_row_get_rev_part_join['name_EN'];
							$j_rev_part_join_name_CN = 		$j_row_get_rev_part_join['name_CN'];
							$j_rev_part_join_type_ID = 		$j_row_get_rev_part_join['type_ID'];
							$j_rev_part_join_rev_num = 		$j_row_get_rev_part_join['revision_number'];
							$j_rev_part_join_part_ID = 		$j_row_get_rev_part_join['part_ID'];

							} // end get BOM part / part rev data
					  ?>
					  <option value="BOM_view.php?id=<?php echo $j_BOM_ID; ?>">
					    <?php
					    	echo $j_rev_part_join_part_code;
					    ?> - <?php
					    	echo $j_rev_part_join_name_EN;
					    	if (($j_rev_part_join_name_CN != '')&&($j_rev_part_join_name_CN != '中文名')) {
					    		?> / <?php echo $j_rev_part_join_name_CN;
					    	} ?> (<?php echo $j_BOM_type; ?>)
					  </option>
					  <?php
					  } // end while loop
					  ?>
                              <option value="BOM.php">View All / 看全部</option>
                             </select>
                            <!-- / PART JUMPER -->
						</div>
					</div>


					<div class="clearfix">&nbsp;</div>


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
									  <li><strong>System <abbr title="Bill of Materials">BOM</abbr> ID: <?php echo $BOM_ID; ?></strong> </li>
									  <li><strong>Release Date:</strong> <?php echo date("Y-m-d", strtotime($BOM_date_entered)); ?></li>
									  <li><strong>Released By:</strong> <a href="user_view.php?id=<?php echo $rev_body_user; ?>"><?php echo $rev_user_first_name . " " . $rev_user_last_name; if (($rev_user_name_CN != '')&&($rev_user_name_CN != '中文名')) { echo " / " . $rev_user_name_CN; } ?></a></li>
									</ul>

								</div>
							</section>



							<!-- ******************************************************************************** -->

							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="va-middle">Quick Links</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

									  <a href="part_view.php?id=<?php echo $part_ID; ?>" class="btn btn-success">
									  	<i class="fa fa-eye"></i>
									  	PART PROFILE
									  </a>

									  <?php if ($BOM_parent_BOM_ID!=0) { ?>
									  <br /><br />
									  	<a href="BOM_view.php?id=<?php echo $BOM_parent_BOM_ID; ?>" class="btn btn-info">
									  	  <i class="fa fa-arrow-up"></i>
									  	  PARENT BOM
									  	</a>
									  <?php } ?>
									  <br />&nbsp;
									</div>
									<div class="panel-footer">
									    <div class="text-right">
												<a href="#" title="This feature is not yet enabled. Please submit a feedback report if you need it now!" class="text-uppercase text-muted">
													EDIT / DELETE / COPY HERE?
												</a>
									    </div>
									  </div>
									</div>
								</section>

									<!-- END OF LEFT COLUMN: -->
									</div>

									<!-- START MAIN BODY COLUMN: -->
									<div class="col-md-8 col-lg-8">





						<div class="row">

						<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="va-middle">Components / Sub-Assemblies</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">

							<div class="table-responsive">
							 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  		  <thead>
					  			<tr>
					    			<th>ID</th>
					    			<th>Photo</th>
					    			<th>Code</th>
					    			<th>Name / 名字</th>
					    			<th>Usage</th>
					    			<th><abbr title="Revision">Rev.</abbr></th>
					    			<th>Type</th>
					 			 </tr>
					 		  </thead>


					 		  <tbody>
					 			 <?php

					 			 // GET THE ASSOCIATED PARTS
					 			 $grand_total_components = 0;
								 $total_components = 0;
								 $total_assemblies = 0;

					 			 $get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $record_id . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

					 			 // DEBUG:
					 			 // echo $get_components_SQL;

					 			 $result_get_components = mysqli_query($con,$get_components_SQL);
								 // while loop
								 while($row_get_components = mysqli_fetch_array($result_get_components)) {
									$components_BOM_item_ID = $row_get_components['ID'];
									$components_product_BOM_ID = $row_get_components['product_BOM_ID']; // should be same as $record_ID
									$components_part_rev_ID = $row_get_components['part_rev_ID'];
									$components_parent_ID = $row_get_components['parent_ID'];
									$components_created_by = $row_get_components['created_by'];
									$components_date_entered = $row_get_components['date_entered'];
									$components_record_status = $row_get_components['record_status']; // should be 2 (published)
									$components_entry_order = $row_get_components['entry_order'];
									$components_usage_qty = $row_get_components['usage_qty'];
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

										$rev_part_join_part_ID = $row_get_rev_part_join['part_ID'];
										$rev_part_join_revision_ID = $row_get_rev_part_join['rev_revision_ID'];
										$rev_part_join_part_type_ID = $row_get_rev_part_join['type_ID']; // look this up!
										$rev_part_join_part_code = $row_get_rev_part_join['part_code'];
										$rev_part_join_name_EN = $row_get_rev_part_join['name_EN'];
										$rev_part_join_name_CN = $row_get_rev_part_join['name_CN'];
										$rev_part_join_type_ID = $row_get_rev_part_join['type_ID'];
										$rev_part_join_rev_num = $row_get_rev_part_join['revision_number'];
										$rev_part_join_part_ID = $row_get_rev_part_join['part_ID'];

										// echo 'PTID = ' . $rev_part_join_type_ID;

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
											$component_photo_id = $row_get_part_component_photo['ID'];
											$component_photo_name_EN = $row_get_part_component_photo['name_EN'];
											$component_photo_name_CN = $row_get_part_component_photo['name_CN'];
											$component_photo_filename = $row_get_part_component_photo['filename'];
											$component_photo_filetype_ID = $row_get_part_component_photo['filetype_ID'];
											$component_photo_location = $row_get_part_component_photo['file_location'];
											$component_photo_lookup_table = $row_get_part_component_photo['lookup_table'];
											$component_photo_lookup_id = $row_get_part_component_photo['lookup_ID'];
											$component_photo_document_category = $row_get_part_component_photo['document_category'];
											$component_photo_record_status = $row_get_part_component_photo['record_status'];
											$component_photo_created_by = $row_get_part_component_photo['created_by'];
											$component_photo_date_created = $row_get_part_component_photo['date_created'];
											$component_photo_filesize_bytes = $row_get_part_component_photo['filesize_bytes'];
											$component_photo_document_icon = $row_get_part_component_photo['document_icon'];
											$component_photo_document_remarks = $row_get_part_component_photo['document_remarks'];
											$component_photo_doc_revision = $row_get_part_component_photo['doc_revision'];

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

								} // end get BOM part / part rev data

					 			 ?>

					 			<tr>
					 			  <td class="text-center">
					 			  		<h1>
					 			  		    <?php echo $components_entry_order; ?>
					 			  		</h1>
					 			  </td>
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
					 			  	<?php
					 			  			if ($rev_part_join_part_type_ID == 10) {


					 			  			// GO GET THE CHILD INFO!
												$get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $record_id . " AND `part_rev_ID` = " . $rev_part_join_revision_ID . " ORDER BY `entry_order` ASC";
												// debug
												// echo "SQL: " . $get_child_BOM_SQL;

												$result_get_child_BOM = mysqli_query($con,$get_child_BOM_SQL);

												// while loop
												while($row_get_child_BOM = mysqli_fetch_array($result_get_child_BOM)) {
													$child_BOM_ID = $row_get_child_BOM['ID'];
													$child_BOM_part_rev_ID = $row_get_child_BOM['part_rev_ID'];
													$child_BOM_date_entered = $row_get_child_BOM['date_entered'];
													$child_BOM_record_status = $row_get_child_BOM['record_status'];
													$child_BOM_created_by = $row_get_child_BOM['created_by'];
													$child_BOM_type = $row_get_child_BOM['BOM_type'];
													$child_BOM_parent_BOM_ID = $row_get_child_BOM_list['parent_BOM_ID'];

												}

					 			  				?>
					 			  				<br />
					 			  				<a href="BOM_view.php?id=<?php echo $child_BOM_ID; ?>" class="btn btn-info" title="Click here to view this Bill Of Materials">
					 			  					<i class="fa fa-cogs"></i> BOM # <?php echo $child_BOM_ID; ?>
					 			  				</a>
					 			  				<?php
					 			  			}
					 			  	?>
					 			  </td>
					 			  <td class="text-center"><?php echo $components_usage_qty; ?></td>
					 			  <td>
					 			  	<a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>" class="btn btn-warning" title="Rev. #: <?php echo $components_part_rev_ID; ?>">
					 			  		<?php echo $rev_part_join_rev_num; ?>
					 			  	</a>
					 			  </td>
					 			  <td>
					 			  	<?php

					 			  		// if ($rev_part_join_part_type_ID == 10) { echo '10<br />'; }

					 			  		echo $component_type_EN;
					 			  		if (($component_type_CN!='')&&($component_type_EN!='中文名')) {
					 			  			echo " / " . $component_type_CN;
					 			  		}




					 			  	?>
					 			  </td>
					 			</tr>
					 			<?php

													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/*              START A NEW SUB-LEVEL (1)                    */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													if (($view == 'full')&&($rev_part_join_part_type_ID == 10)) {

														// START THE SUB-LEVEL BOM HERE!
														$a_get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $child_BOM_ID . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

														 // DEBUG:
														 // echo $a_get_components_SQL;

														 $a_result_get_components = mysqli_query($con,$a_get_components_SQL);
														 // while loop
														 while($a_row_get_components = mysqli_fetch_array($a_result_get_components)) {
															$a_components_BOM_item_ID = 	$a_row_get_components['ID'];
															$a_components_product_BOM_ID = 	$a_row_get_components['product_BOM_ID']; // should be same as $a_record_ID
															$a_components_part_rev_ID = 	$a_row_get_components['part_rev_ID'];
															$a_components_parent_ID = 		$a_row_get_components['parent_ID'];
															$a_components_created_by = 		$a_row_get_components['created_by'];
															$a_components_date_entered = 	$a_row_get_components['date_entered'];
															$a_components_record_status = 	$a_row_get_components['record_status']; // should be 2 (published)
															$a_components_entry_order = 	$a_row_get_components['entry_order'];
															$a_components_usage_qty = 		$a_row_get_components['usage_qty'];
															// echo 'OK';
															// now get the rev and part info:

															$a_order_by = " ORDER BY `parts`.`type_ID` ASC";

															$a_combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $a_components_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2" . $a_order_by;
															// DEBUG:
															// echo 'DEBUG: ' . $a_combine_part_and_rev_SQL . '<br />';

															$a_result_get_rev_part_join = mysqli_query($con,$a_combine_part_and_rev_SQL);
															// while loop
															while($a_row_get_rev_part_join = mysqli_fetch_array($a_result_get_rev_part_join)) {

																// GET BOM LIST:

																$a_rev_part_join_part_ID = $a_row_get_rev_part_join['part_ID'];
																$a_rev_part_join_revision_ID = $a_row_get_rev_part_join['rev_revision_ID'];
																$a_rev_part_join_part_type_ID = $a_row_get_rev_part_join['type_ID']; // look this up!
																$a_rev_part_join_part_code = $a_row_get_rev_part_join['part_code'];
																$a_rev_part_join_name_EN = $a_row_get_rev_part_join['name_EN'];
																$a_rev_part_join_name_CN = $a_row_get_rev_part_join['name_CN'];
																$a_rev_part_join_type_ID = $a_row_get_rev_part_join['type_ID'];
																$a_rev_part_join_rev_num = $a_row_get_rev_part_join['revision_number'];
																$a_rev_part_join_part_ID = $a_row_get_rev_part_join['part_ID'];

																// echo 'PTID = ' . $a_rev_part_join_type_ID;

																	// GET COMPONENT PART TYPE:
																	$a_get_component_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $a_rev_part_join_part_type_ID;
																	// echo $a_get_component_type_SQL;
																	$a_result_get_component_type = mysqli_query($con,$a_get_component_type_SQL);
																	// while loop
																	while($a_row_get_component_type = mysqli_fetch_array($a_result_get_component_type)) {
																		$a_component_type_EN = $a_row_get_component_type['name_EN'];
																		$a_component_type_CN = $a_row_get_component_type['name_CN'];
																	}


																// now get the part revision photo!
																$a_num_component_photos_found = 0;
																$a_component_photo_location = "assets/images/no_image_found.jpg";

																$a_get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $a_rev_part_join_revision_ID;
																// echo "<h1>".$a_get_part_component_photo_SQL."</h1>";
																$a_result_get_part_component_photo = mysqli_query($con,$a_get_part_component_photo_SQL);
																// while loop
																while($a_row_get_part_component_photo = mysqli_fetch_array($a_result_get_part_component_photo)) {

																	$a_num_component_photos_found = $a_num_component_photos_found + 1;

																	// now print each record:
																	$a_component_photo_id = $a_row_get_part_component_photo['ID'];
																	$a_component_photo_name_EN = $a_row_get_part_component_photo['name_EN'];
																	$a_component_photo_name_CN = $a_row_get_part_component_photo['name_CN'];
																	$a_component_photo_filename = $a_row_get_part_component_photo['filename'];
																	$a_component_photo_filetype_ID = $a_row_get_part_component_photo['filetype_ID'];
																	$a_component_photo_location = $a_row_get_part_component_photo['file_location'];
																	$a_component_photo_lookup_table = $a_row_get_part_component_photo['lookup_table'];
																	$a_component_photo_lookup_id = $a_row_get_part_component_photo['lookup_ID'];
																	$a_component_photo_document_category = $a_row_get_part_component_photo['document_category'];
																	$a_component_photo_record_status = $a_row_get_part_component_photo['record_status'];
																	$a_component_photo_created_by = $a_row_get_part_component_photo['created_by'];
																	$a_component_photo_date_created = $a_row_get_part_component_photo['date_created'];
																	$a_component_photo_filesize_bytes = $a_row_get_part_component_photo['filesize_bytes'];
																	$a_component_photo_document_icon = $a_row_get_part_component_photo['document_icon'];
																	$a_component_photo_document_remarks = $a_row_get_part_component_photo['document_remarks'];
																	$a_component_photo_doc_revision = $a_row_get_part_component_photo['doc_revision'];

																	if ($a_component_photo_filename!='') {
																		// now apply filename
																		$a_component_photo_location = "assets/images/" . $a_component_photo_location . "/" . $a_component_photo_filename;
																	}
																	else {
																		$a_component_photo_location = "assets/images/no_image_found.jpg";
																	}

																} // end get part rev photo


															if ($a_rev_part_join_part_type_ID == 10) {
																$total_assemblies = $total_assemblies + 1;
															}
															else {
																$total_components = $total_components + 1;
															}

														} // end get BOM part / part rev data

														 ?>

														<tr>
														  <td class="text-center">
															<h2>
																<i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
															    echo ' ' . $a_components_entry_order; ?>
															</h2>
														  </td>
														  <td class="text-center">
															<img src="<?php
																echo $a_component_photo_location;
															?>" class="rounded img-responsive" alt="<?php
																echo $a_rev_part_join_part_code;
															?> - <?php
																echo $a_rev_part_join_name_EN;
																if (($a_rev_part_join_name_CN!='')&&($a_rev_part_join_name_CN!='中文名')) {
																	echo " / " . $a_rev_part_join_name_CN;
																}
															?>" style="width:100px;" />
														  </td>
														  <td>
															<a href="part_view.php?id=<?php echo $a_rev_part_join_part_ID; ?>">
																<?php echo $a_rev_part_join_part_code; ?>
															</a>
														  </td>
														  <td>
																<a href="part_view.php?id=<?php echo $a_rev_part_join_part_ID; ?>">
																	<?php
																		echo $a_rev_part_join_name_EN;
																		if (($a_rev_part_join_name_CN!='')&&($a_rev_part_join_name_CN!='中文名')) {
																			echo " / " . $a_rev_part_join_name_CN;
																		}

																	?>
																</a>
															<?php
																	if ($a_rev_part_join_part_type_ID == 10) {


																	// GO GET THE CHILD INFO!
																		$a_get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $child_BOM_ID . " AND `part_rev_ID` = " . $a_rev_part_join_revision_ID . " ORDER BY `entry_order` ASC";
																		// debug
																		// echo "SQL: " . $a_get_child_BOM_SQL;

																		$a_result_get_child_BOM = mysqli_query($con,$a_get_child_BOM_SQL);

																		// while loop
																		while($a_row_get_child_BOM = mysqli_fetch_array($a_result_get_child_BOM)) {
																			$a_child_BOM_ID = $a_row_get_child_BOM['ID'];
																			$a_child_BOM_part_rev_ID = $a_row_get_child_BOM['part_rev_ID'];
																			$a_child_BOM_date_entered = $a_row_get_child_BOM['date_entered'];
																			$a_child_BOM_record_status = $a_row_get_child_BOM['record_status'];
																			$a_child_BOM_created_by = $a_row_get_child_BOM['created_by'];
																			$a_child_BOM_type = $a_row_get_child_BOM['BOM_type'];
																			$a_child_BOM_parent_BOM_ID = $a_row_get_child_BOM_list['parent_BOM_ID'];

																		}

																		?>
																		<br />
																		<a href="BOM_view.php?id=<?php echo $a_child_BOM_ID; ?>" class="btn btn-info" title="Click here to view this Bill Of Materials">
																			<i class="fa fa-cogs"></i> BOM # <?php echo $a_child_BOM_ID; ?>
																		</a>
																		<?php
																	}
															?>
														  </td>
					 			 						  <td class="text-center"><?php echo $a_components_usage_qty; ?></td>
														  <td>
															<a href="part_view.php?id=<?php echo $a_rev_part_join_part_ID; ?>" class="btn btn-warning" title="Rev. #: <?php echo $a_components_part_rev_ID; ?>">
																<?php echo $a_rev_part_join_rev_num; ?>
															</a>
														  </td>
														  <td>
															<?php

																// if ($a_rev_part_join_part_type_ID == 10) { echo '10<br />'; }

																echo $a_component_type_EN;
																if (($a_component_type_CN!='')&&($a_component_type_EN!='中文名')) {
																	echo " / " . $a_component_type_CN;
																}

															?>
														  </td>
														</tr>
														<?php


																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/*              START A NEW SUB-LEVEL (2)                    */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			if (($view == 'full')&&($a_rev_part_join_part_type_ID == 10)) {

																				// echo 'true';

																				// START THE SUB-LEVEL BOM HERE!
																				$b_get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $a_child_BOM_ID . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

																				 // DEBUG:
																				 // echo '<tr><td colspan="6">b_get_components_SQL = ' . $b_get_components_SQL . '</td></tr>';

																				 $b_result_get_components = mysqli_query($con,$b_get_components_SQL);
																				 // while loop
																				 while($b_row_get_components = mysqli_fetch_array($b_result_get_components)) {
																					$b_components_BOM_item_ID = 	$b_row_get_components['ID'];
																					$b_components_product_BOM_ID = 	$b_row_get_components['product_BOM_ID']; // should be same as $b_record_ID
																					$b_components_part_rev_ID = 	$b_row_get_components['part_rev_ID'];
																					$b_components_parent_ID = 		$b_row_get_components['parent_ID'];
																					$b_components_created_by = 		$b_row_get_components['created_by'];
																					$b_components_date_entered = 	$b_row_get_components['date_entered'];
																					$b_components_record_status = 	$b_row_get_components['record_status']; // should be 2 (published)
																					$b_components_entry_order = 	$b_row_get_components['entry_order'];
																					$b_components_usage_qty = 		$b_row_get_components['usage_qty'];
																					// echo 'OK';
																					// now get the rev and part info:

																					$b_order_by = " ORDER BY `parts`.`type_ID` ASC";

																					$b_combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $b_components_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2" . $b_order_by;
																					// DEBUG:
																					// echo 'DEBUG: ' . $b_combine_part_and_rev_SQL . '<br />';

																					$b_result_get_rev_part_join = mysqli_query($con,$b_combine_part_and_rev_SQL);
																					// while loop
																					while($b_row_get_rev_part_join = mysqli_fetch_array($b_result_get_rev_part_join)) {

																						// GET BOM LIST:

																						$b_rev_part_join_part_ID = $b_row_get_rev_part_join['part_ID'];
																						$b_rev_part_join_revision_ID = $b_row_get_rev_part_join['rev_revision_ID'];
																						$b_rev_part_join_part_type_ID = $b_row_get_rev_part_join['type_ID']; // look this up!
																						$b_rev_part_join_part_code = $b_row_get_rev_part_join['part_code'];
																						$b_rev_part_join_name_EN = $b_row_get_rev_part_join['name_EN'];
																						$b_rev_part_join_name_CN = $b_row_get_rev_part_join['name_CN'];
																						$b_rev_part_join_type_ID = $b_row_get_rev_part_join['type_ID'];
																						$b_rev_part_join_rev_num = $b_row_get_rev_part_join['revision_number'];
																						$b_rev_part_join_part_ID = $b_row_get_rev_part_join['part_ID'];

																						// echo 'PTID = ' . $b_rev_part_join_type_ID;

																							// GET COMPONENT PART TYPE:
																							$b_get_component_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $b_rev_part_join_part_type_ID;
																							// echo $b_get_component_type_SQL;
																							$b_result_get_component_type = mysqli_query($con,$b_get_component_type_SQL);
																							// while loop
																							while($b_row_get_component_type = mysqli_fetch_array($b_result_get_component_type)) {
																								$b_component_type_EN = $b_row_get_component_type['name_EN'];
																								$b_component_type_CN = $b_row_get_component_type['name_CN'];
																							}


																						// now get the part revision photo!
																						$b_num_component_photos_found = 0;
																						$b_component_photo_location = "assets/images/no_image_found.jpg";

																						$b_get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $b_rev_part_join_revision_ID;
																						// echo "<h1>".$b_get_part_component_photo_SQL."</h1>";
																						$b_result_get_part_component_photo = mysqli_query($con,$b_get_part_component_photo_SQL);
																						// while loop
																						while($b_row_get_part_component_photo = mysqli_fetch_array($b_result_get_part_component_photo)) {

																							$b_num_component_photos_found = $b_num_component_photos_found + 1;

																							// now print each record:
																							$b_component_photo_id = $b_row_get_part_component_photo['ID'];
																							$b_component_photo_name_EN = $b_row_get_part_component_photo['name_EN'];
																							$b_component_photo_name_CN = $b_row_get_part_component_photo['name_CN'];
																							$b_component_photo_filename = $b_row_get_part_component_photo['filename'];
																							$b_component_photo_filetype_ID = $b_row_get_part_component_photo['filetype_ID'];
																							$b_component_photo_location = $b_row_get_part_component_photo['file_location'];
																							$b_component_photo_lookup_table = $b_row_get_part_component_photo['lookup_table'];
																							$b_component_photo_lookup_id = $b_row_get_part_component_photo['lookup_ID'];
																							$b_component_photo_document_category = $b_row_get_part_component_photo['document_category'];
																							$b_component_photo_record_status = $b_row_get_part_component_photo['record_status'];
																							$b_component_photo_created_by = $b_row_get_part_component_photo['created_by'];
																							$b_component_photo_date_created = $b_row_get_part_component_photo['date_created'];
																							$b_component_photo_filesize_bytes = $b_row_get_part_component_photo['filesize_bytes'];
																							$b_component_photo_document_icon = $b_row_get_part_component_photo['document_icon'];
																							$b_component_photo_document_remarks = $b_row_get_part_component_photo['document_remarks'];
																							$b_component_photo_doc_revision = $b_row_get_part_component_photo['doc_revision'];

																							if ($b_component_photo_filename!='') {
																								// now apply filename
																								$b_component_photo_location = "assets/images/" . $b_component_photo_location . "/" . $b_component_photo_filename;
																							}
																							else {
																								$b_component_photo_location = "assets/images/no_image_found.jpg";
																							}

																						} // end get part rev photo


																					if ($b_rev_part_join_part_type_ID == 10) {
																						$total_assemblies = $total_assemblies + 1;
																					}
																					else {
																						$total_components = $total_components + 1;
																					}

																				} // end get BOM part / part rev data

																				 ?>

																				<tr>
																				  <td class="text-center">
																				    <h3>
																						<i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																					  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																					  echo ' ' . $b_components_entry_order; ?>
																					</h3>
																				  </td>
																				  <td class="text-center">
																					<img src="<?php
																						echo $b_component_photo_location;
																					?>" class="rounded img-responsive" alt="<?php
																						echo $b_rev_part_join_part_code;
																					?> - <?php
																						echo $b_rev_part_join_name_EN;
																						if (($b_rev_part_join_name_CN!='')&&($b_rev_part_join_name_CN!='中文名')) {
																							echo " / " . $b_rev_part_join_name_CN;
																						}
																					?>" style="width:100px;" />
																				  </td>
																				  <td>
																					<a href="part_view.php?id=<?php echo $b_rev_part_join_part_ID; ?>">
																						<?php echo $b_rev_part_join_part_code; ?>
																					</a>
																				  </td>
																				  <td>
																						<a href="part_view.php?id=<?php echo $b_rev_part_join_part_ID; ?>">
																							<?php
																								echo $b_rev_part_join_name_EN;
																								if (($b_rev_part_join_name_CN!='')&&($b_rev_part_join_name_CN!='中文名')) {
																									echo " / " . $b_rev_part_join_name_CN;
																								}

																							?>
																						</a>
																					<?php
																							if ($b_rev_part_join_part_type_ID == 10) {


																							// GO GET THE CHILD INFO!
																								$b_get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $a_child_BOM_ID . " AND `part_rev_ID` = " . $b_rev_part_join_revision_ID . " ORDER BY `entry_order` ASC";
																								// debug
																								// echo "SQL: " . $b_get_child_BOM_SQL;
																				 				// echo '<tr><td colspan="6">b_get_child_BOM_SQL = ' . $b_get_child_BOM_SQL . '</td></tr>';

																								$b_result_get_child_BOM = mysqli_query($con,$b_get_child_BOM_SQL);

																								// while loop
																								while($b_row_get_child_BOM = mysqli_fetch_array($b_result_get_child_BOM)) {
																									$b_child_BOM_ID = $b_row_get_child_BOM['ID'];
																									$b_child_BOM_part_rev_ID = $b_row_get_child_BOM['part_rev_ID'];
																									$b_child_BOM_date_entered = $b_row_get_child_BOM['date_entered'];
																									$b_child_BOM_record_status = $b_row_get_child_BOM['record_status'];
																									$b_child_BOM_created_by = $b_row_get_child_BOM['created_by'];
																									$b_child_BOM_type = $b_row_get_child_BOM['BOM_type'];
																									$b_child_BOM_parent_BOM_ID = $b_row_get_child_BOM_list['parent_BOM_ID'];

																								}

																								?>
																								<br />
																								<a href="BOM_view.php?id=<?php echo $b_child_BOM_ID; ?>" class="btn btn-info" title="Click here to view this Bill Of Materials">
																									<i class="fa fa-cogs"></i> BOM # <?php echo $b_child_BOM_ID; ?>
																								</a>
																								<?php
																							}
																					?>
																				  </td>
					 			 						  						  <td class="text-center"><?php echo $b_components_usage_qty; ?></td>
																				  <td>
																					<a href="part_view.php?id=<?php echo $b_rev_part_join_part_ID; ?>" class="btn btn-warning" title="Rev. #: <?php echo $a_components_part_rev_ID; ?>">
																						<?php echo $b_rev_part_join_rev_num; ?>
																					</a>
																				  </td>
																				  <td>
																					<?php

																						// if ($b_rev_part_join_part_type_ID == 10) { echo '10<br />'; }

																						echo $b_component_type_EN;
																						if (($b_component_type_CN!='')&&($b_component_type_EN!='中文名')) {
																							echo " / " . $b_component_type_CN;
																						}

																					?>
																				  </td>
																				</tr>
																				<?php


																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/*              START A NEW SUB-LEVEL (3)                    */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											if (($view == 'full')&&($b_rev_part_join_part_type_ID == 10)) {

																												// START THE SUB-LEVEL BOM HERE!
																												$c_get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $b_child_BOM_ID . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

																												 // DEBUG:
																												 // echo $c_get_components_SQL;

																												 $c_result_get_components = mysqli_query($con,$c_get_components_SQL);
																												 // while loop
																												 while($c_row_get_components = mysqli_fetch_array($c_result_get_components)) {
																													$c_components_BOM_item_ID = 	$c_row_get_components['ID'];
																													$c_components_product_BOM_ID = 	$c_row_get_components['product_BOM_ID']; // should be same as $c_record_ID
																													$c_components_part_rev_ID = 	$c_row_get_components['part_rev_ID'];
																													$c_components_parent_ID = 		$c_row_get_components['parent_ID'];
																													$c_components_created_by = 		$c_row_get_components['created_by'];
																													$c_components_date_entered = 	$c_row_get_components['date_entered'];
																													$c_components_record_status = 	$c_row_get_components['record_status']; // should be 2 (published)
																													$c_components_entry_order = 	$c_row_get_components['entry_order'];
																													$c_components_usage_qty = 		$c_row_get_components['usage_qty'];
																													// echo 'OK';
																													// now get the rev and part info:

																													$c_order_by = " ORDER BY `parts`.`type_ID` ASC";

																													$c_combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $c_components_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2" . $c_order_by;
																													// DEBUG:
																													// echo 'DEBUG: ' . $c_combine_part_and_rev_SQL . '<br />';

																													$c_result_get_rev_part_join = mysqli_query($con,$c_combine_part_and_rev_SQL);
																													// while loop
																													while($c_row_get_rev_part_join = mysqli_fetch_array($c_result_get_rev_part_join)) {

																														// GET BOM LIST:

																														$c_rev_part_join_part_ID = $c_row_get_rev_part_join['part_ID'];
																														$c_rev_part_join_revision_ID = $c_row_get_rev_part_join['rev_revision_ID'];
																														$c_rev_part_join_part_type_ID = $c_row_get_rev_part_join['type_ID']; // look this up!
																														$c_rev_part_join_part_code = $c_row_get_rev_part_join['part_code'];
																														$c_rev_part_join_name_EN = $c_row_get_rev_part_join['name_EN'];
																														$c_rev_part_join_name_CN = $c_row_get_rev_part_join['name_CN'];
																														$c_rev_part_join_type_ID = $c_row_get_rev_part_join['type_ID'];
																														$c_rev_part_join_rev_num = $c_row_get_rev_part_join['revision_number'];
																														$c_rev_part_join_part_ID = $c_row_get_rev_part_join['part_ID'];

																														// echo 'PTID = ' . $c_rev_part_join_type_ID;

																															// GET COMPONENT PART TYPE:
																															$c_get_component_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $c_rev_part_join_part_type_ID;
																															// echo $c_get_component_type_SQL;
																															$c_result_get_component_type = mysqli_query($con,$c_get_component_type_SQL);
																															// while loop
																															while($c_row_get_component_type = mysqli_fetch_array($c_result_get_component_type)) {
																																$c_component_type_EN = $c_row_get_component_type['name_EN'];
																																$c_component_type_CN = $c_row_get_component_type['name_CN'];
																															}


																														// now get the part revision photo!
																														$c_num_component_photos_found = 0;
																														$c_component_photo_location = "assets/images/no_image_found.jpg";

																														$c_get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $c_rev_part_join_revision_ID;
																														// echo "<h1>".$c_get_part_component_photo_SQL."</h1>";
																														$c_result_get_part_component_photo = mysqli_query($con,$c_get_part_component_photo_SQL);
																														// while loop
																														while($c_row_get_part_component_photo = mysqli_fetch_array($c_result_get_part_component_photo)) {

																															$c_num_component_photos_found = $c_num_component_photos_found + 1;

																															// now print each record:
																															$c_component_photo_id = $c_row_get_part_component_photo['ID'];
																															$c_component_photo_name_EN = $c_row_get_part_component_photo['name_EN'];
																															$c_component_photo_name_CN = $c_row_get_part_component_photo['name_CN'];
																															$c_component_photo_filename = $c_row_get_part_component_photo['filename'];
																															$c_component_photo_filetype_ID = $c_row_get_part_component_photo['filetype_ID'];
																															$c_component_photo_location = $c_row_get_part_component_photo['file_location'];
																															$c_component_photo_lookup_table = $c_row_get_part_component_photo['lookup_table'];
																															$c_component_photo_lookup_id = $c_row_get_part_component_photo['lookup_ID'];
																															$c_component_photo_document_category = $c_row_get_part_component_photo['document_category'];
																															$c_component_photo_record_status = $c_row_get_part_component_photo['record_status'];
																															$c_component_photo_created_by = $c_row_get_part_component_photo['created_by'];
																															$c_component_photo_date_created = $c_row_get_part_component_photo['date_created'];
																															$c_component_photo_filesize_bytes = $c_row_get_part_component_photo['filesize_bytes'];
																															$c_component_photo_document_icon = $c_row_get_part_component_photo['document_icon'];
																															$c_component_photo_document_remarks = $c_row_get_part_component_photo['document_remarks'];
																															$c_component_photo_doc_revision = $c_row_get_part_component_photo['doc_revision'];

																															if ($c_component_photo_filename!='') {
																																// now apply filename
																																$c_component_photo_location = "assets/images/" . $c_component_photo_location . "/" . $c_component_photo_filename;
																															}
																															else {
																																$c_component_photo_location = "assets/images/no_image_found.jpg";
																															}

																														} // end get part rev photo


																													if ($c_rev_part_join_part_type_ID == 10) {
																														$total_assemblies = $total_assemblies + 1;
																													}
																													else {
																														$total_components = $total_components + 1;
																													}

																												} // end get BOM part / part rev data

																												 ?>

																												<tr>

																												  <td class="text-center">
																												    <h4>
																														<i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																													  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																													  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																													  echo ' ' . $c_components_entry_order; ?>
																													</h4>
																												  </td>
																												  <td class="text-center">
																													<img src="<?php
																														echo $c_component_photo_location;
																													?>" class="rounded img-responsive" alt="<?php
																														echo $c_rev_part_join_part_code;
																													?> - <?php
																														echo $c_rev_part_join_name_EN;
																														if (($c_rev_part_join_name_CN!='')&&($c_rev_part_join_name_CN!='中文名')) {
																															echo " / " . $c_rev_part_join_name_CN;
																														}
																													?>" style="width:100px;" />
																												  </td>
																												  <td>
																													<a href="part_view.php?id=<?php echo $c_rev_part_join_part_ID; ?>">
																														<?php echo $c_rev_part_join_part_code; ?>
																													</a>
																												  </td>
																												  <td>
																														<a href="part_view.php?id=<?php echo $c_rev_part_join_part_ID; ?>">
																															<?php
																																echo $c_rev_part_join_name_EN;
																																if (($c_rev_part_join_name_CN!='')&&($c_rev_part_join_name_CN!='中文名')) {
																																	echo " / " . $c_rev_part_join_name_CN;
																																}

																															?>
																														</a>
																													<?php
																															if ($c_rev_part_join_part_type_ID == 10) {


																															// GO GET THE CHILD INFO!
																																$c_get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $b_child_BOM_ID . " AND `part_rev_ID` = " . $c_rev_part_join_revision_ID . " ORDER BY `entry_order` ASC";
																																// debug
																																// echo "SQL: " . $c_get_child_BOM_SQL;

																																$c_result_get_child_BOM = mysqli_query($con,$c_get_child_BOM_SQL);

																																// while loop
																																while($c_row_get_child_BOM = mysqli_fetch_array($c_result_get_child_BOM)) {
																																	$c_child_BOM_ID = $c_row_get_child_BOM['ID'];
																																	$c_child_BOM_part_rev_ID = $c_row_get_child_BOM['part_rev_ID'];
																																	$c_child_BOM_date_entered = $c_row_get_child_BOM['date_entered'];
																																	$c_child_BOM_record_status = $c_row_get_child_BOM['record_status'];
																																	$c_child_BOM_created_by = $c_row_get_child_BOM['created_by'];
																																	$c_child_BOM_type = $c_row_get_child_BOM['BOM_type'];
																																	$c_child_BOM_parent_BOM_ID = $c_row_get_child_BOM_list['parent_BOM_ID'];

																																}

																																?>
																																<br />
																																<a href="BOM_view.php?id=<?php echo $c_child_BOM_ID; ?>" class="btn btn-info" title="Click here to view this Bill Of Materials">
																																	<i class="fa fa-cogs"></i> BOM # <?php echo $c_child_BOM_ID; ?>
																																</a>
																																<?php
																															}
																													?>
																												  </td>
					 			 						  						  								  <td class="text-center"><?php echo $c_components_usage_qty; ?></td>
																												  <td>
																													<a href="part_view.php?id=<?php echo $c_rev_part_join_part_ID; ?>" class="btn btn-warning" title="Rev. #: <?php echo $c_components_part_rev_ID; ?>">
																														<?php echo $c_rev_part_join_rev_num; ?>
																													</a>
																												  </td>
																												  <td>
																													<?php

																														// if ($c_rev_part_join_part_type_ID == 10) { echo '10<br />'; }

																														echo $c_component_type_EN;
																														if (($c_component_type_CN!='')&&($c_component_type_EN!='中文名')) {
																															echo " / " . $c_component_type_CN;
																														}

																													?>
																												  </td>
																												</tr>
																												<?php




																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/*              START A NEW SUB-LEVEL (4)                    */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			if (($view == 'full')&&($c_rev_part_join_part_type_ID == 10)) {

																																				// START THE SUB-LEVEL BOM HERE!
																																				$d_get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $c_child_BOM_ID . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

																																				 // DEBUG:
																																				 // echo $d_get_components_SQL;

																																				 $d_result_get_components = mysqli_query($con,$d_get_components_SQL);
																																				 // while loop
																																				 while($d_row_get_components = mysqli_fetch_array($d_result_get_components)) {
																																					$d_components_BOM_item_ID = 	$d_row_get_components['ID'];
																																					$d_components_product_BOM_ID = 	$d_row_get_components['product_BOM_ID']; // should be same as $d_record_ID
																																					$d_components_part_rev_ID = 	$d_row_get_components['part_rev_ID'];
																																					$d_components_parent_ID = 		$d_row_get_components['parent_ID'];
																																					$d_components_created_by = 		$d_row_get_components['created_by'];
																																					$d_components_date_entered = 	$d_row_get_components['date_entered'];
																																					$d_components_record_status = 	$d_row_get_components['record_status']; // should be 2 (published)
																																					$d_components_entry_order = 	$d_row_get_components['entry_order'];
																																					$d_components_usage_qty = 		$d_row_get_components['usage_qty'];
																																					// echo 'OK';
																																					// now get the rev and part info:

																																					$d_order_by = " ORDER BY `parts`.`type_ID` ASC";

																																					$d_combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $d_components_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2" . $d_order_by;
																																					// DEBUG:
																																					// echo 'DEBUG: ' . $d_combine_part_and_rev_SQL . '<br />';

																																					$d_result_get_rev_part_join = mysqli_query($con,$d_combine_part_and_rev_SQL);
																																					// while loop
																																					while($d_row_get_rev_part_join = mysqli_fetch_array($d_result_get_rev_part_join)) {

																																						// GET BOM LIST:

																																						$d_rev_part_join_part_ID = $d_row_get_rev_part_join['part_ID'];
																																						$d_rev_part_join_revision_ID = $d_row_get_rev_part_join['rev_revision_ID'];
																																						$d_rev_part_join_part_type_ID = $d_row_get_rev_part_join['type_ID']; // look this up!
																																						$d_rev_part_join_part_code = $d_row_get_rev_part_join['part_code'];
																																						$d_rev_part_join_name_EN = $d_row_get_rev_part_join['name_EN'];
																																						$d_rev_part_join_name_CN = $d_row_get_rev_part_join['name_CN'];
																																						$d_rev_part_join_type_ID = $d_row_get_rev_part_join['type_ID'];
																																						$d_rev_part_join_rev_num = $d_row_get_rev_part_join['revision_number'];
																																						$d_rev_part_join_part_ID = $d_row_get_rev_part_join['part_ID'];

																																						// echo 'PTID = ' . $d_rev_part_join_type_ID;

																																							// GET COMPONENT PART TYPE:
																																							$d_get_component_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $d_rev_part_join_part_type_ID;
																																							// echo $d_get_component_type_SQL;
																																							$d_result_get_component_type = mysqli_query($con,$d_get_component_type_SQL);
																																							// while loop
																																							while($d_row_get_component_type = mysqli_fetch_array($d_result_get_component_type)) {
																																								$d_component_type_EN = $d_row_get_component_type['name_EN'];
																																								$d_component_type_CN = $d_row_get_component_type['name_CN'];
																																							}


																																						// now get the part revision photo!
																																						$d_num_component_photos_found = 0;
																																						$d_component_photo_location = "assets/images/no_image_found.jpg";

																																						$d_get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $d_rev_part_join_revision_ID;
																																						// echo "<h1>".$d_get_part_component_photo_SQL."</h1>";
																																						$d_result_get_part_component_photo = mysqli_query($con,$d_get_part_component_photo_SQL);
																																						// while loop
																																						while($d_row_get_part_component_photo = mysqli_fetch_array($d_result_get_part_component_photo)) {

																																							$d_num_component_photos_found = $d_num_component_photos_found + 1;

																																							// now print each record:
																																							$d_component_photo_id = $d_row_get_part_component_photo['ID'];
																																							$d_component_photo_name_EN = $d_row_get_part_component_photo['name_EN'];
																																							$d_component_photo_name_CN = $d_row_get_part_component_photo['name_CN'];
																																							$d_component_photo_filename = $d_row_get_part_component_photo['filename'];
																																							$d_component_photo_filetype_ID = $d_row_get_part_component_photo['filetype_ID'];
																																							$d_component_photo_location = $d_row_get_part_component_photo['file_location'];
																																							$d_component_photo_lookup_table = $d_row_get_part_component_photo['lookup_table'];
																																							$d_component_photo_lookup_id = $d_row_get_part_component_photo['lookup_ID'];
																																							$d_component_photo_document_category = $d_row_get_part_component_photo['document_category'];
																																							$d_component_photo_record_status = $d_row_get_part_component_photo['record_status'];
																																							$d_component_photo_created_by = $d_row_get_part_component_photo['created_by'];
																																							$d_component_photo_date_created = $d_row_get_part_component_photo['date_created'];
																																							$d_component_photo_filesize_bytes = $d_row_get_part_component_photo['filesize_bytes'];
																																							$d_component_photo_document_icon = $d_row_get_part_component_photo['document_icon'];
																																							$d_component_photo_document_remarks = $d_row_get_part_component_photo['document_remarks'];
																																							$d_component_photo_doc_revision = $d_row_get_part_component_photo['doc_revision'];

																																							if ($d_component_photo_filename!='') {
																																								// now apply filename
																																								$d_component_photo_location = "assets/images/" . $d_component_photo_location . "/" . $d_component_photo_filename;
																																							}
																																							else {
																																								$d_component_photo_location = "assets/images/no_image_found.jpg";
																																							}

																																						} // end get part rev photo


																																					if ($d_rev_part_join_part_type_ID == 10) {
																																						$total_assemblies = $total_assemblies + 1;
																																					}
																																					else {
																																						$total_components = $total_components + 1;
																																					}

																																				} // end get BOM part / part rev data

																																				 ?>

																																				<tr>
																																				  <td class="text-center">
																																				    <h5>
																																				  		<i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																				  	  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																				  	  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																				  	  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																				  	  echo ' ' . $d_components_entry_order; ?>
																																				  	</h5>
																																				  </td>
																																				  <td class="text-center">
																																					<img src="<?php
																																						echo $d_component_photo_location;
																																					?>" class="rounded img-responsive" alt="<?php
																																						echo $d_rev_part_join_part_code;
																																					?> - <?php
																																						echo $d_rev_part_join_name_EN;
																																						if (($d_rev_part_join_name_CN!='')&&($d_rev_part_join_name_CN!='中文名')) {
																																							echo " / " . $d_rev_part_join_name_CN;
																																						}
																																					?>" style="width:100px;" />
																																				  </td>
																																				  <td>
																																					<a href="part_view.php?id=<?php echo $d_rev_part_join_part_ID; ?>">
																																						<?php echo $d_rev_part_join_part_code; ?>
																																					</a>
																																				  </td>
																																				  <td>
																																						<a href="part_view.php?id=<?php echo $d_rev_part_join_part_ID; ?>">
																																							<?php
																																								echo $d_rev_part_join_name_EN;
																																								if (($d_rev_part_join_name_CN!='')&&($d_rev_part_join_name_CN!='中文名')) {
																																									echo " / " . $d_rev_part_join_name_CN;
																																								}

																																							?>
																																						</a>
																																					<?php
																																							if ($d_rev_part_join_part_type_ID == 10) {


																																							// GO GET THE CHILD INFO!
																																								$d_get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $c_child_BOM_ID . " AND `part_rev_ID` = " . $d_rev_part_join_revision_ID . " ORDER BY `entry_order` ASC";
																																								// debug
																																								// echo "SQL: " . $d_get_child_BOM_SQL;

																																								$d_result_get_child_BOM = mysqli_query($con,$d_get_child_BOM_SQL);

																																								// while loop
																																								while($d_row_get_child_BOM = mysqli_fetch_array($d_result_get_child_BOM)) {
																																									$d_child_BOM_ID = $d_row_get_child_BOM['ID'];
																																									$d_child_BOM_part_rev_ID = $d_row_get_child_BOM['part_rev_ID'];
																																									$d_child_BOM_date_entered = $d_row_get_child_BOM['date_entered'];
																																									$d_child_BOM_record_status = $d_row_get_child_BOM['record_status'];
																																									$d_child_BOM_created_by = $d_row_get_child_BOM['created_by'];
																																									$d_child_BOM_type = $d_row_get_child_BOM['BOM_type'];
																																									$d_child_BOM_parent_BOM_ID = $d_row_get_child_BOM_list['parent_BOM_ID'];

																																								}

																																								?>
																																								<br />
																																								<a href="BOM_view.php?id=<?php echo $d_child_BOM_ID; ?>" class="btn btn-info" title="Click here to view this Bill Of Materials">
																																									<i class="fa fa-cogs"></i> BOM # <?php echo $d_child_BOM_ID; ?>
																																								</a>
																																								<?php
																																							}
																																					?>
																																				  </td>
																																				  <td class="text-center"><?php echo $d_components_usage_qty; ?></td>
																																				  <td>
																																					<a href="part_view.php?id=<?php echo $d_rev_part_join_part_ID; ?>" class="btn btn-warning" title="Rev. #: <?php echo $d_components_part_rev_ID; ?>">
																																						<?php echo $d_rev_part_join_rev_num; ?>
																																					</a>
																																				  </td>
																																				  <td>
																																					<?php

																																						// if ($d_rev_part_join_part_type_ID == 10) { echo '10<br />'; }

																																						echo $d_component_type_EN;
																																						if (($d_component_type_CN!='')&&($d_component_type_EN!='中文名')) {
																																							echo " / " . $d_component_type_CN;
																																						}

																																					?>
																																				  </td>
																																				</tr>
																																				<?php





/* PLEASE SCROLL RIGHT >>>>>> ^_^ */																																					/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/*              START A NEW SUB-LEVEL (5)                    */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														if (($view == 'full')&&($d_rev_part_join_part_type_ID == 10)) {

																																															// START THE SUB-LEVEL BOM HERE!
																																															$e_get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $d_child_BOM_ID . " AND  `record_status` =2 ORDER BY `entry_order` ASC";

																																															 // DEBUG:
																																															 // echo $e_get_components_SQL;

																																															 $e_result_get_components = mysqli_query($con,$e_get_components_SQL);
																																															 // while loop
																																															 while($e_row_get_components = mysqli_fetch_array($e_result_get_components)) {
																																																$e_components_BOM_item_ID = 	$e_row_get_components['ID'];
																																																$e_components_product_BOM_ID = 	$e_row_get_components['product_BOM_ID']; // should be same as $e_record_ID
																																																$e_components_part_rev_ID = 	$e_row_get_components['part_rev_ID'];
																																																$e_components_parent_ID = 		$e_row_get_components['parent_ID'];
																																																$e_components_created_by = 		$e_row_get_components['created_by'];
																																																$e_components_date_entered = 	$e_row_get_components['date_entered'];
																																																$e_components_record_status = 	$e_row_get_components['record_status']; // should be 2 (published)
																																																$e_components_entry_order = 	$e_row_get_components['entry_order'];
																																																$e_components_usage_qty = 		$e_row_get_components['usage_qty'];
																																																// echo 'OK';
																																																// now get the rev and part info:

																																																$e_order_by = " ORDER BY `parts`.`type_ID` ASC";

																																																$e_combine_part_and_rev_SQL = "SELECT `parts`.`ID` AS `part_ID`, `parts`.`type_ID`, `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $e_components_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2" . $e_order_by;
																																																// DEBUG:
																																																// echo 'DEBUG: ' . $e_combine_part_and_rev_SQL . '<br />';

																																																$e_result_get_rev_part_join = mysqli_query($con,$e_combine_part_and_rev_SQL);
																																																// while loop
																																																while($e_row_get_rev_part_join = mysqli_fetch_array($e_result_get_rev_part_join)) {

																																																	// GET BOM LIST:

																																																	$e_rev_part_join_part_ID = $e_row_get_rev_part_join['part_ID'];
																																																	$e_rev_part_join_revision_ID = $e_row_get_rev_part_join['rev_revision_ID'];
																																																	$e_rev_part_join_part_type_ID = $e_row_get_rev_part_join['type_ID']; // look this up!
																																																	$e_rev_part_join_part_code = $e_row_get_rev_part_join['part_code'];
																																																	$e_rev_part_join_name_EN = $e_row_get_rev_part_join['name_EN'];
																																																	$e_rev_part_join_name_CN = $e_row_get_rev_part_join['name_CN'];
																																																	$e_rev_part_join_type_ID = $e_row_get_rev_part_join['type_ID'];
																																																	$e_rev_part_join_rev_num = $e_row_get_rev_part_join['revision_number'];
																																																	$e_rev_part_join_part_ID = $e_row_get_rev_part_join['part_ID'];

																																																	// echo 'PTID = ' . $e_rev_part_join_type_ID;

																																																		// GET COMPONENT PART TYPE:
																																																		$e_get_component_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` =" . $e_rev_part_join_part_type_ID;
																																																		// echo $e_get_component_type_SQL;
																																																		$e_result_get_component_type = mysqli_query($con,$e_get_component_type_SQL);
																																																		// while loop
																																																		while($e_row_get_component_type = mysqli_fetch_array($e_result_get_component_type)) {
																																																			$e_component_type_EN = $e_row_get_component_type['name_EN'];
																																																			$e_component_type_CN = $e_row_get_component_type['name_CN'];
																																																		}


																																																	// now get the part revision photo!
																																																	$e_num_component_photos_found = 0;
																																																	$e_component_photo_location = "assets/images/no_image_found.jpg";

																																																	$e_get_part_component_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $e_rev_part_join_revision_ID;
																																																	// echo "<h1>".$e_get_part_component_photo_SQL."</h1>";
																																																	$e_result_get_part_component_photo = mysqli_query($con,$e_get_part_component_photo_SQL);
																																																	// while loop
																																																	while($e_row_get_part_component_photo = mysqli_fetch_array($e_result_get_part_component_photo)) {

																																																		$e_num_component_photos_found = $e_num_component_photos_found + 1;

																																																		// now print each record:
																																																		$e_component_photo_id = $e_row_get_part_component_photo['ID'];
																																																		$e_component_photo_name_EN = $e_row_get_part_component_photo['name_EN'];
																																																		$e_component_photo_name_CN = $e_row_get_part_component_photo['name_CN'];
																																																		$e_component_photo_filename = $e_row_get_part_component_photo['filename'];
																																																		$e_component_photo_filetype_ID = $e_row_get_part_component_photo['filetype_ID'];
																																																		$e_component_photo_location = $e_row_get_part_component_photo['file_location'];
																																																		$e_component_photo_lookup_table = $e_row_get_part_component_photo['lookup_table'];
																																																		$e_component_photo_lookup_id = $e_row_get_part_component_photo['lookup_ID'];
																																																		$e_component_photo_document_category = $e_row_get_part_component_photo['document_category'];
																																																		$e_component_photo_record_status = $e_row_get_part_component_photo['record_status'];
																																																		$e_component_photo_created_by = $e_row_get_part_component_photo['created_by'];
																																																		$e_component_photo_date_created = $e_row_get_part_component_photo['date_created'];
																																																		$e_component_photo_filesize_bytes = $e_row_get_part_component_photo['filesize_bytes'];
																																																		$e_component_photo_document_icon = $e_row_get_part_component_photo['document_icon'];
																																																		$e_component_photo_document_remarks = $e_row_get_part_component_photo['document_remarks'];
																																																		$e_component_photo_doc_revision = $e_row_get_part_component_photo['doc_revision'];

																																																		if ($e_component_photo_filename!='') {
																																																			// now apply filename
																																																			$e_component_photo_location = "assets/images/" . $e_component_photo_location . "/" . $e_component_photo_filename;
																																																		}
																																																		else {
																																																			$e_component_photo_location = "assets/images/no_image_found.jpg";
																																																		}

																																																	} // end get part rev photo


																																																if ($e_rev_part_join_part_type_ID == 10) {
																																																	$total_assemblies = $total_assemblies + 1;
																																																}
																																																else {
																																																	$total_components = $total_components + 1;
																																																}

																																															} // end get BOM part / part rev data

																																															 ?>

																																															<tr>
																																															  <td class="text-center">
																																															    <h6>
																																																	<i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																																  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																																  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																																  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																																  ?><i class="fa fa-angle-right"></i><?php // switch to PHP for beautiful code ^_^
																																																  echo ' ' . $e_components_entry_order; ?>
																																																</h6>
																																															  </td>
																																															  <td class="text-center">
																																																<img src="<?php
																																																	echo $e_component_photo_location;
																																																?>" class="rounded img-responsive" alt="<?php
																																																	echo $e_rev_part_join_part_code;
																																																?> - <?php
																																																	echo $e_rev_part_join_name_EN;
																																																	if (($e_rev_part_join_name_CN!='')&&($e_rev_part_join_name_CN!='中文名')) {
																																																		echo " / " . $e_rev_part_join_name_CN;
																																																	}
																																																?>" style="width:100px;" />
																																															  </td>
																																															  <td>
																																																<a href="part_view.php?id=<?php echo $e_rev_part_join_part_ID; ?>">
																																																	<?php echo $e_rev_part_join_part_code; ?>
																																																</a>
																																															  </td>
																																															  <td>
																																																	<a href="part_view.php?id=<?php echo $e_rev_part_join_part_ID; ?>">
																																																		<?php
																																																			echo $e_rev_part_join_name_EN;
																																																			if (($e_rev_part_join_name_CN!='')&&($e_rev_part_join_name_CN!='中文名')) {
																																																				echo " / " . $e_rev_part_join_name_CN;
																																																			}

																																																		?>
																																																	</a>
																																																<?php
																																																		if ($e_rev_part_join_part_type_ID == 10) {


																																																		// GO GET THE CHILD INFO!
																																																			$e_get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $d_child_BOM_ID . " AND `part_rev_ID` = " . $e_rev_part_join_revision_ID . " ORDER BY `entry_order` ASC";
																																																			// debug
																																																			// echo "SQL: " . $e_get_child_BOM_SQL;

																																																			$e_result_get_child_BOM = mysqli_query($con,$e_get_child_BOM_SQL);

																																																			// while loop
																																																			while($e_row_get_child_BOM = mysqli_fetch_array($e_result_get_child_BOM)) {
																																																				$e_child_BOM_ID = $e_row_get_child_BOM['ID'];
																																																				$e_child_BOM_part_rev_ID = $e_row_get_child_BOM['part_rev_ID'];
																																																				$e_child_BOM_date_entered = $e_row_get_child_BOM['date_entered'];
																																																				$e_child_BOM_record_status = $e_row_get_child_BOM['record_status'];
																																																				$e_child_BOM_created_by = $e_row_get_child_BOM['created_by'];
																																																				$e_child_BOM_type = $e_row_get_child_BOM['BOM_type'];
																																																				$e_child_BOM_parent_BOM_ID = $e_row_get_child_BOM_list['parent_BOM_ID'];

																																																			}

																																																			?>
																																																			<br />
																																																			<a href="BOM_view.php?id=<?php echo $e_child_BOM_ID; ?>" class="btn btn-info" title="Click here to view this Bill Of Materials">
																																																				<i class="fa fa-cogs"></i> BOM # <?php echo $e_child_BOM_ID; ?>
																																																			</a>
																																																			<?php
																																																		}
																																																?>
																																															  </td>
																																															  <td class="text-center"><?php echo $e_components_usage_qty; ?></td>
																																															  <td>
																																																<a href="part_view.php?id=<?php echo $e_rev_part_join_part_ID; ?>" class="btn btn-warning" title="Rev. #: <?php echo $e_components_part_rev_ID; ?>">
																																																	<?php echo $e_rev_part_join_rev_num; ?>
																																																</a>
																																															  </td>
																																															  <td>
																																																<?php

																																																	// if ($e_rev_part_join_part_type_ID == 10) { echo '10<br />'; }

																																																	echo $e_component_type_EN;
																																																	if (($e_component_type_CN!='')&&($e_component_type_EN!='中文名')) {
																																																		echo " / " . $e_component_type_CN;
																																																	}

																																																?>
																																															  </td>
																																															</tr>
																																															<?php

																																															} // close the loop... for LOOP 5 (e)

																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/*                    END SUB-LEVEL (5)                      */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														/* ********************************************************* */
																																														} // end of level 5 view = full and type = 10 (subassy)




																																				} // close the loop... for LOOP 4 (d)

																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/*                    END SUB-LEVEL (4)                      */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			/* ********************************************************* */
																																			} // end of level 4 view = full and type = 10 (subassy)





																												} // close the loop... for LOOP 3 (c)

																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/*                    END SUB-LEVEL (3)                      */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											/* ********************************************************* */
																											} // end of level 3 view = full and type = 10 (subassy)






																				} // close the loop... for LOOP 2 (b)

																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/*                    END SUB-LEVEL (2)                      */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			/* ********************************************************* */
																			} // end of level 2 view = full and type = 10 (subassy)






														} // close the loop... for LOOP 1 (a)

													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/*                    END SUB-LEVEL (1)                      */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													/* ********************************************************* */
													} // end of level 1 view = full and type = 10 (subassy)

					 			} // close the loop... for original BOM loop!

					 			?>
					 		  </tbody>

					 		  <tfoot>
					 			<tr>
					 			  <th colspan="6">
					 			  	TOTAL COMPONENTS: <?php echo $total_components; ?><br />
					 			  	TOTAL SUB-ASSEMBLIES: <?php echo $total_assemblies; ?><br />
					 			  	TOTAL ITEMS: <?php echo ($total_components + $total_assemblies); ?>
					 			  </th>
					 			</tr>
 				 			  </tfoot>
					 		</table>
					 	   </div>


							</div>
								  <div class="panel-footer">
									<div class="text-left">
									<?php
									if ($total_assemblies > 0) {
										// SHOW EXPAND / CONTRACT BUTTONS
										if ($view == 'full') {
											// CONTRACT
											?>
											<a href="BOM_view.php?id=<?php echo $record_id; ?>" class="btn btn-info">
												<i class="fa fa-compress"></i>
												CONTRACT VIEW
											</a>
											<?php
										}
										else {
											// EXPAND
											?>
											<a href="BOM_view.php?id=<?php echo $record_id; ?>&view=full" class="btn btn-info">
												<i class="fa fa-expand"></i>
												EXPAND VIEW
											</a>
											<?php
										}
									}
									?>
											<a href="BOM.php" class="btn btn-primary">
												<i class="fa fa-search"></i>
												VIEW ALL BOMS
											</a>
										</div>
								  </div>
								</div>
							</section>



						</div>

						<div class="clearfix">&nbsp;</div>






									<!-- END OF MAIN BODY COLUMN -->
									</div>

								</div>



					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
