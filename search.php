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



$page_id = 55;

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Search</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Search</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
<?php

	$keyword = '';

if (isset($_REQUEST['query'])) {

	$keyword = $_REQUEST['query'];

  if (strlen($keyword)<3) {
		// string is too short! redirect to search page with an error
		header('Location: search.php?too_short');
		// exit();
  }
  else {	 // it's OK to search for this...




	// check if it's in the search_query table, otherwise add it

	date_default_timezone_set('Asia/Hong_Kong');

	$add_query_SQL = "INSERT INTO `search_queries`(`ID`, `query_term`, `user_ID`, `date_entered`, `record_status`) VALUES (NULL,'" . addslashes($keyword) . "','1','" . date("Y-m-d H:i:s") . "', '2')";

	// DEBUG
	// echo $add_cat_SQL;

	// NOW LET'S SEND THE USER SOMEWHERE SENSIBLE...
	if (mysqli_query($con, $add_query_SQL)) {

				// redirect to page with message
				// header("Location: view_cat.php?msg=OK&action=add");
				// die();
				echo '<!-- ADDED QUERY TO THE SEARCH TERM LIST -->';
	}
	else {
		// redirect to page with message
		// header("Location: view_cat.php?msg=NG&action=add&error=SQL_fail");
		// die();
		echo '<!-- FAILED TO ADD QUERY TO THE SEARCH LIST TABLE';
	}
  } // END NOT TOO SHORT
} // END WHILE FORM VAR FOUND

?>

<!-- now run the page -->

        <?php


		// BOOTSTRAP BEAUTIFICATION:

		?>
		<div class="search-content">
						<div class="search-control-wrapper">
							<form action="search.php">
								<div class="form-group">
									<div class="input-group">
										<input type="text" class="form-control" value="<?php echo $keyword; ?>" id="query" name="query">
										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" name="submit" value="submit" id="submit">Search</button>
										</span>
									</div>
								</div>
							</form>
						</div>
						<div class="tab-content">
							<div id="everything" class="tab-pane active">
								<!-- <p class="total-results text-muted">Showing results:</p> -->

								<ul class="list-unstyled search-results-list">

<?php if ($keyword!='') {

	/*
	DEV NOTES:

	show all tables:
	SHOW TABLES IN  `cl11-epg`

	show table fields:
	SHOW FIELDS FROM tablename

	*/

	$num_results = 0;


	// UPDATE: time to make a smarter search algorythm :)

	$this_table = 0;

	$get_tables_SQL = "SHOW TABLES IN  `cl11-epg`";

	$result_get_tables = mysqli_query($con,$get_tables_SQL);
		// while loop
		while($row_get_tables = mysqli_fetch_array($result_get_tables)) {


				// now print each common search result
				$table_name = $row_get_tables['Tables_in_cl11-epg'];
				// echo "<h3>TABLE: " . $table_name . "</h3>";

				// IGNORE THESE TABLES:

				if (

					($table_name != 'AQL_letter')
					&&
					($table_name != 'AQL_level')
					&&
					($table_name != 'global_config')
					&&
					($table_name != 'prt_batch_movement')
					&&
					($table_name != 'search_queries')
					&&
					($table_name != 'record_status_type')
					&&
					($table_name != 'update_log')
					// can add more tables to ignore here...

				) {


						$this_table = $this_table + 1;
						if ($this_table!=1) {
							$search_SQL = $search_SQL . "
							UNION
							";
						}

						if ($table_name == 'world_regions') {
							// my bad...
							$search_SQL = $search_SQL . "(SELECT `region_ID` AS `ID`, '" . $table_name . "' as `table` FROM `" . $table_name . "`
						WHERE ";
						}
						else {
							$search_SQL = $search_SQL . "(SELECT `ID`, '" . $table_name . "' as `table` FROM `" . $table_name . "`
						WHERE ";
						}

						// now get the column names that ARE NOT INT or TINYINT:
						$get_cols_SQL = "SHOW FIELDS FROM " . $table_name . " WHERE `Type` NOT LIKE  '%int%'";
						// echo "<h4>COLS: " . $get_cols_SQL . "</h4>";

						$this_field_name = 0;

						$result_get_cols = mysqli_query($con,$get_cols_SQL);
						// while loop
						while($row_get_cols = mysqli_fetch_array($result_get_cols)) {

							   $this_field_name = $this_field_name + 1;
							   if ($this_field_name!=1) {
									// add the 'OR' for every one execpt the first one...
									$search_SQL = $search_SQL . "
									OR ";
								}

								// now print each common search result
								$field_name = $row_get_cols['Field'];

								// now append the search string:
								$search_SQL = $search_SQL . "`" . $field_name . "` LIKE '%" . $keyword ."%'";

						} // END OF GET COLUMNS

						// make the search ignore any tables with ZERO results:

						if ($this_field_name==0) {
							$search_SQL = $search_SQL . "0";
						}

						// now close the SQL statement
						$search_SQL = $search_SQL . ")";

				} // END IGNORE TABLES LIST

		} // END WHILE GET TABLES

		    // DEBUG
			// echo "<h3>search SQL = <pre>". $search_SQL . "</pre></h3>";

		$result_get_search = mysqli_query($con,$search_SQL);
		// while loop
		while($row_get_search = mysqli_fetch_array($result_get_search)) {

				// default:
				$has_thumb = false;
				$description_text = '';

				// now print each common search result
				$search_ID = $row_get_search['ID'];
				$type = $row_get_search['table'];

				// now use 'type' to go get more info...

				$get_details_SQL = "SELECT * FROM `" . $type . "` WHERE `ID` = " . $search_ID;

				$result_get_details = mysqli_query($con,$get_details_SQL);
				// while loop
				while($row_get_details = mysqli_fetch_array($result_get_details)) {

						$link_to = "view_" . $type;
						$result_ID = $search_ID;
						$display_type = $type;
						$description_text = '';
						$HR_status = '';

						$name_EN = $row_get_details['name_EN'];
						$name_CN = $row_get_details['name_CN'];

						// now capture anomolies or special data:


						if ($type == 'users') {
							// lookup record
							$name_EN = $row_get_details['first_name'] . " " . $row_get_details['last_name'];
							$link_to = 'user_view';
							$display_type = 'User / 用户';
							$has_thumb = true;
							$pic_link = 'assets/images/users/user_' . $result_ID . '.png';
							$description_text = 'Current member of staff.';
							$HR_status = 'OK';

							if ($name_CN=='中文名') {
								$name_CN = '';
							}
						}
						else if ($type == 'suppliers') {
							// lookup record
							$name_EN = $row_get_details['name_EN'];
							$name_CN = $row_get_details['name_CN'];
							$link_to = 'supplier_view';
							$display_type = 'Supplier / 供应商';
							/*
							$has_thumb = true;
							$pic_link = 'images/vendors/ven_' . $result_ID . '.jpg';
							*/
							$description_text = 'Supplier to EPG';
						}
						else if ($type == 'parts') {
							// lookup record
							$name_EN = $row_get_details['name_EN'];
							$name_CN = $row_get_details['name_CN'];
							$link_to = 'part_view';

							$has_thumb = true;

							// NOW GO GET THE LATEST PHOTO:
							$get_parts_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $result_ID;
					  // echo $get_parts_SQL;

					  $part_count = 0;

					  $result_get_parts = mysqli_query($con,$get_parts_SQL);
					  // while loop
					  while($row_get_parts = mysqli_fetch_array($result_get_parts)) {

					  	// GET PART TYPE:

					  	$part_ID = $row_get_parts['ID'];
						$part_code = $row_get_parts['part_code'];
						$part_name_EN = $row_get_parts['name_EN'];
						$part_name_CN = $row_get_parts['name_CN'];
						$part_description = $row_get_parts['description'];
						$part_type_ID = $row_get_parts['type_ID'];
						$part_classification_ID = $row_get_parts['classification_ID'];
						$part_parent_ID = $row_get_parts['parent_ID'];
						$part_default_supplier_ID = $row_get_parts['default_suppler_ID'];
						$part_part_assy_ID = $row_get_parts['part_assy_ID'];
						$part_record_status = $row_get_parts['record_status'];

								$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $part_type_ID . "'";
								// echo $get_part_type_SQL;

								$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
								// while loop
								while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
									$part_type_EN = $row_get_part_type['name_EN'];
									$part_type_CN = $row_get_part_type['name_CN'];
								}

								// GET PART CLASSIFICATION:

								$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE `ID` ='" . $part_classification_ID . "'";
								// echo $get_part_class_SQL;

								$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
								// while loop
								while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
									$part_class_EN = $row_get_part_class['name_EN'];
									$part_class_CN = $row_get_part_class['name_CN'];
								}



								// get part revision info:
								$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `part_ID` ='" . $part_ID . "' ORDER BY `revision_number` ASC LIMIT 0, 1";

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

								}

								// now get the part revision photo!

									$num_rev_photos_found = 0;
									$rev_photo_location = "assets/images/no_image_found.jpg";

									$get_part_rev_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $rev_id;
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

										// now apply filename
										$rev_photo_location = "assets/images/" . $rev_photo_location . "/" . $rev_photo_filename;

									}

									// echo "<h1>Revs Found: " . $num_rev_photos_found . "</h1>";

							} // END GET PIC WHILE LOOP?

							$pic_link = $rev_photo_location;




							$display_type = 'Part / 零件';

						}
						else if ($type == 'documents') {
							// lookup record
							$name_EN = $row_get_details['name_EN'];
							$name_CN = $row_get_details['name_CN'];
							$link_to = 'document_view';
							$has_thumb = true;


							// now get the document!
							$num_rev_docs_found = 0;
							$rev_doc_location = "assets/images/no_image_found.jpg";

							$get_part_rev_doc_SQL = "SELECT * FROM `documents` WHERE  `ID`=" . $result_ID;
							// echo "<h1>".$get_part_rev_doc_SQL."</h1>";
							$result_get_part_rev_doc = mysqli_query($con,$get_part_rev_doc_SQL);
							// while loop
							while($row_get_part_rev_doc = mysqli_fetch_array($result_get_part_rev_doc)) {

								$num_rev_docs_found = $num_rev_docs_found + 1;

								// now print each record:
								$rev_doc_id = $row_get_part_rev_doc['ID'];
								$rev_doc_name_EN = $row_get_part_rev_doc['name_EN'];
								$rev_doc_name_CN = $row_get_part_rev_doc['name_CN'];
								$rev_doc_filename = $row_get_part_rev_doc['filename'];
								$rev_doc_filetype_ID = $row_get_part_rev_doc['filetype_ID']; // look this up!
								$rev_doc_location = $row_get_part_rev_doc['file_location'];
								$rev_doc_lookup_table = $row_get_part_rev_doc['lookup_table'];
								$rev_doc_lookup_id = $row_get_part_rev_doc['lookup_ID'];
								$rev_doc_document_category = $row_get_part_rev_doc['document_category'];
								$rev_doc_record_status = $row_get_part_rev_doc['record_status'];
								$rev_doc_created_by = $row_get_part_rev_doc['created_by'];
								$rev_doc_date_created = $row_get_part_rev_doc['date_created'];
								$rev_doc_filesize_bytes = $row_get_part_rev_doc['filesize_bytes'];
								$rev_doc_document_icon = $row_get_part_rev_doc['document_icon'];
								$rev_doc_document_remarks = $row_get_part_rev_doc['document_remarks'];
								$rev_doc_doc_revision = $row_get_part_rev_doc['doc_revision'];

								if ($rev_doc_filetype_ID == 5) { // only apply to photos...
									// now apply filename
									$rev_doc_location = "assets/images/" . $rev_doc_location . "/" . $rev_doc_filename;
								}
							}



							// get doc info!

							$pic_link = $rev_doc_location;


							$display_type = 'Document / 文件';
						}

						else if ($type == 'purchase_orders') {
							// lookup record
							$name_EN = $row_get_details['PO_number'];
							$link_to = 'purchase_order_view';
							$display_type = 'Purchase Order';
							$description_text = 'Purchase Order Record';
						}
						else if ($type == 'categories') {
							// lookup record
							$name_EN = $row_get_details['Name_EN'];
							$name_CN = $row_get_details['Name_CN'];
							$link_to = 'view_cat_profile';
							$has_thumb = true;
							$pic_link = 'images/categories/cat_' . $result_ID . '.jpg';
							$display_type = 'Category';
						}
						else if ($type == 'clients') {
							// lookup record
							$link_to = 'view_client_profile';
							$display_type = 'Client';
							$has_thumb = true;
							$pic_link = 'images/clients/client_' . $result_ID . '.jpg';
						}
						else if ($type == 'materials') {
							// lookup record
							$name_EN = $row_get_details['Name_EN'];
							$name_CN = $row_get_details['Name_CN'];
							$link_to = 'view_mat_profile';
							$has_thumb = true;
							$pic_link = 'images/materials/thumbs/mat_' . $result_ID . '.jpg';
							$display_type = 'Material / 材料';
						}
						else if ($type == 'processes') {
							// lookup record
							$link_to = 'view_pro';
							$display_type = 'Process';
							$has_thumb = true;
							$pic_link = 'images/processes/pro_' . $result_ID . '.jpg';
						}
						else if ($type == 'experience') {
							$name_EN = $row_get_details['title'];
							$result_ID = $row_get_details['user_ID'];
							$link_to = 'view_user_profile';
							$display_type = 'Life Experience';
						}
						else if ($type == 'vendors_locations_dept') {
							$display_type = 'Department';
							$link_to = "view_dept_profile";
						}
						else if ($type == 'dept_types') {
							$display_type = 'Department Type';
							$link_to = "view_dept_type_profile";
						}
						else if ($type == 'education_level') {
							$display_type = 'Education Level';
							$link_to = "view_education_level_profile";
						}
						else if ($type == 'vendors_locations') {
							$name_EN = $row_get_details['location_name'];
							$display_type = 'Location';
							$link_to = "view_loc_profile";
						}
						else if ($type == 'vendors_locations_type') {
							$display_type = 'Location Type';
							$link_to = "view_loc_type_profile";
						}
						else if ($type == 'colors_2') {
							$name_EN = $row_get_details['name'];
							$display_type = 'Color';
							$link_to = "colors";
							$result_ID = $result_ID . '&num_mats=1';
						}
						else if ($type == 'world_countries') {
							$link_to = 'view_country_profile';
							$result_ID = $result_ID . '&show=countries';
							$display_type = 'Country';
						}
						else if ($type == 'devices') {
							$link_to = 'view_device_profile';
							$display_type = 'Devices';
							$has_thumb = true;
							$pic_link = 'images/devices/device_' . $result_ID . '.jpg';
						}
						else if ($type == 'job_titles') {
							$link_to = 'view_JD';
							$display_type = 'Job Title';
						}
						else if ($type == 'academy_awards') {
							$link_to = 'view_awards';
							$display_type = 'Academy Awards';
						}
						else if ($type == 'buildings') {
							$link_to = 'view_building_profile';
							$display_type = 'Building';
							$has_thumb = true;
							$pic_link = 'images/buildings/building_' . $result_ID . '.jpg';
						}
						else if ($type == 'staff_grades') {
							$name_EN = $row_get_details['category'];
							$name_CN = "Grade " . $row_get_details['F'];
							$display_type = 'Staff Grade';
						}
						else if ($type == 'rooms') {
							$link_to = 'view_room_profile';
							$display_type = 'Room';
							$has_thumb = true;
							$pic_link = 'images/rooms/room_' . $result_ID . '.jpg';
						}
						else if ($type == 'room_types') {
							$link_to = 'view_room_type_profile';
							$display_type = 'Room Type';
							$has_thumb = false;
						}
						else if ($type == 'industry_sectors') {
							$link_to = 'view_industry_sector_profile';
							$display_type = 'Industry Sector';
						}
						else if ($type == 'business_processes') {
							$link_to = 'view_business_process_profile';
							$display_type = 'Business Procee';
						}
						else if ($type == 'vehicles') {
							$link_to = 'view_vehicle_profile';
							$name_EN = "Plate: " . $row_get_details['plate'] . " (" . $row_get_details['num_seats'] . " seats)";
							$name_CN = '';
							$display_type = 'Vehicle Record';
							$has_thumb = true;
							$pic_link = 'images/vehicles/vehicle_' . $result_ID . '.jpg';
						}
						else if ($type == 'incoterms') {
							$link_to = 'view_incoterm_profile';
							$display_type = 'Incoterm';
						}
						else if ($type == 'skills') {
							$display_type = 'Skills';
							$link_to = 'view_skill_profile';
						}
						else if ($type == 'equipment') {
							$link_to = 'view_equipment_profile';
							$display_type = 'Equipment Record';
							$has_thumb = true;
							$pic_link = 'images/equipment/equipment_' . $result_ID . '.jpg';
						}
						else if ($type == 'dev_stages') {
							$display_type = 'Development Stage';
						}
						else if ($type == 'competencies') {
							$display_type = 'Competency';
						}
						else if ($type == 'equipment_types') {
							$display_type = 'Equipment Type / Model';
							$name_EN = "Model #: " . $row_get_details['model'];
							$name_CN = '';
							$link_to = 'view_equipment_models';
						}
						else if ($type == 'equipment_status') {
							$display_type = 'Equipment Status';
							$link_to = 'view_equipment_status_profile';
						}
						else if ($type == 'issues') {
							$display_type = 'Issue';
							$link_to = 'view_issue_profile';
						}
						else if ($type == 'tooling_type') {
							$display_type = 'Tooling Type';
							$link_to = 'view_tooling_type_profile';
						}
						else if ($type == 'vehicle_types') {
							$display_type = 'Vehicle Type';
							$link_to = 'view_vehicle_type_profile';
						}
						else if ($type == 'issues_topics') {
							$display_type = 'Topic';
							$link_to = 'view_topic_profile';
						}
						else if ($type == 'product_cat') {
							$display_type = 'Product Category';
							$link_to = 'view_product_category_profile';
						}
						else if ($type == 'product_type') {
							$display_type = 'Product Type';
							$link_to = 'view_product_type_profile';
						}
						else if ($type == 'projects') {
							$display_type = 'Project';
							$link_to = 'view_project_profile';
						}
						else if ($type == 'files') {
							$display_type = 'File';
							$link_to = 'view_file_profile';
						}
						else if ($type == 'pages') {
							$display_type = 'Page';
							$link_to = 'view_page_profile';
						}
						else {
							// ?
							echo '<h4>Unknown Type found! Type: "<strong>'.$type.'</strong>"</h4>';
						}

						if (($has_thumb==true)||($has_thumb==1)) {
							// finally, check to see if the image exists:
							if (file_exists($pic_link)) {
								$show_img = $pic_link;
							}
							else {
								$has_thumb = false;
							}
						} // end has_thumb true
	?>

    								<li>
										<p class="result-type">
											<span class="label label-<?php
												if ($display_type == 'Document / 文件') {
													?>warning<?php
												}
												else if ($display_type == 'Part / 零件') {
													?>success<?php
												}
												else {
													?>primary<?php
												} ?>"><?php echo $display_type; ?></span>
										</p>
										<a href="<?php echo $link_to; ?>.php?id=<?php echo $result_ID; ?>"<?php if (($has_thumb==true)||($has_thumb==1)) { ?> class="has-thumb"<?php } ?>>
											<?php if (($has_thumb==true)||($has_thumb==1)) { ?>
                                                <div class="result-thumb">
                                                    <img src="<?php echo $pic_link; ?>" alt="<?php echo $name_EN;
                                                    if (($name_CN!='')&&($name_CN!='中文名')&&($name_CN!='公司名')&&($name_CN!='-')&&($name_CN!=$name_EN)) {
                                                        // OK
                                                        ?> / <?php echo $name_CN;
                                                    }?>" />
                                                </div>
                                            <?php } // end has thumb ?>
											<div class="result-data">
												<p class="h3 title text-<?php
													if ($display_type == 'Leaver') {
														?>danger<?php
													}
													else if ($display_type == 'Client') {
														?>success<?php
													}
													else {
														?>primary<?php
													} ?>"><?php
												if ($name_EN == '') {
													echo '<em>(no name set)</em>';
												}
												else {
													echo $name_EN;
												}
												if (($name_CN!='')&&($name_CN!='中文名')&&($name_CN!='公司名')&&($name_CN!='-')&&($name_CN!=$name_EN)) {
													// OK
													?> / <?php echo $name_CN;
												}?></p>
                                                <?php if ($description_text!='') { ?>
												<p class="description"><?php echo $description_text; ?></p>
                                                <?php } ?>
											</div>
										</a>
									</li>
	<?php
    	} // END WHILE GET DETAILS


		// append results count:
		$num_results = $num_results + 1;
		} // END WHILE

		if ($num_results==0) {
			?>
			<td colspan="3"><em><br /><br />Sorry, we couldn't find what you were searching for. Please try a different search term, or just one word.<br /><br />Please also note that Chinese Character Search is not supported yet - we are working on it!<br /><br />
<br />
</em></td>
			<?php
		}

		?>
								</ul>

								<hr class="solid mb-none" />


							</div>
						</div>
					</div>
		<?php



		/*
        <h2>Popular Search Results</h2>
		<br />
        <div class="search_tags">

        <?php

		$get_pop_SQL = "SELECT COUNT(\"*\") ,  `query_term` FROM  `search_queries` GROUP BY  `query_term` ORDER BY `query_term` ASC";

		$result_get_pop = mysqli_query($con,$get_pop_SQL);
				// while loop
				while($row_get_pop = mysqli_fetch_array($result_get_pop)) {

						$pop_count = $row_get_pop['COUNT("*")'];
						$pop_term = $row_get_pop['query_term'];

						// now show the term:

						?><span style="font-size:<?php echo ($pop_count*6); ?>pt;"><a href="search.php?query=<?php echo $pop_term; ?>" style="border: 1px solid #333; padding:<?php echo $pop_count; ?>px; text-decoration:none; background:#999; color:#eaeaea; font-weight:lighter; letter-spacing:50%;"><?php echo $pop_term; ?></a></span> <?php

				}

		*/

		?>

  <?php
}
else {
?>
<p>
<br /><br />Welcome to search!
<br />
<br />Please use a single word in English for now.
<br />
<br />We hope to add multiple words and Chinese in the future.
<br /><br /></p>
<?php

if (isset($_REQUEST['too_short'])) {
	?>
	<div class="message_box" id="error"><i class="fa fa-exclamation-triangle"></i> Please enter 3 or more characters <i class="fa fa-exclamation-triangle"></i></div>
	<?php
}

}
?>

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
        </div>

        <h2 class="panel-title"><i class="fa fa-map-marker"></i> Popular Search Terms</h2>
        <p class="panel-subtitle">The Top 30 Search Terms</p>
    </header>
    <div class="panel-body">
        <div class="row">
        <?php
            $get_popular_search_terms_SQL = "SELECT COUNT( ID ) ,  `query_term`
            FROM  `search_queries`
            WHERE 1
            GROUP BY  `query_term`
            ORDER BY COUNT( ID ) DESC
            LIMIT 0 , 30";

            $result_get_pop_search = mysqli_query($con,$get_popular_search_terms_SQL);
            // while loop
            while($row_get_pop_search = mysqli_fetch_array($result_get_pop_search)) {

                    $link_to = "view_" . $type;
                    $result_ID = $search_ID;
                    $display_type = $type;
                    $description_text = '';

                    $pop_search_term = $row_get_pop_search['query_term'];
                    //now output
                    ?>
                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-default pull-left" onClick="document.location = this.value" value="search.php?query=<?php echo $pop_search_term; ?>"><?php echo $pop_search_term; ?> <i class="fa fa-question-circle"></i></button>
					<?php

            }
        ?>
        </div>
    </div>
</section>


        </div>

        <!-- CLOSING ALL PAGE -->
        </section>
<?php
// now close the page:
pagefooter($page_id, $record_id); ?>
