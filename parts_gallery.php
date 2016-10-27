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


$page_id = 54;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->


<!-- start: page -->
					<section class="content-with-menu content-with-menu-has-toolbar media-gallery">
						<div class="content-with-menu-container">
							<div class="inner-menu-toggle">
								<a href="#" class="inner-menu-expand" data-open="inner-menu">
									Show Bar <i class="fa fa-chevron-right"></i>
								</a>
							</div>

							<menu id="content-menu" class="inner-menu" role="menu">
								<div class="nano">
									<div class="nano-content">

										<div class="inner-menu-toggle-inside">
											<a href="#" class="inner-menu-collapse">
												<i class="fa fa-chevron-up visible-xs-inline"></i><i class="fa fa-chevron-left hidden-xs-inline"></i> Hide Bar
											</a>
											<a href="#" class="inner-menu-expand" data-open="inner-menu">
												Show Bar <i class="fa fa-chevron-down"></i>
											</a>
										</div>

										<div class="inner-menu-content">

											<a class="btn btn-block btn-success btn-md pt-sm pb-sm text-md">
												<i class="fa fa-plus mr-xs"></i>
												Add A New Part
											</a>

											<hr class="separator" />

											<div class="sidebar-widget m-none">
												<div class="widget-header clearfix">
													<!-- START LOOP RESULT OUTPUT OF product_categories HERE! -->
													<h6 class="title pull-left mt-xs">Insujet</h6>
													<div class="pull-right">
														<a href="product_cat.php?id=1" class="btn btn-info btn-sm btn-widget-act"><i class="fa fa-info-circle">Info</a></a>
													</div>
												</div>
												<div class="widget-content">
													<ul class="mg-folders">
														<li>
															<a href="product_revision_view.php?id=1" class="menu-item"><i class="fa fa-folder"></i> 3.60</a>
															<!--
															<div class="item-options">
																<a href="#">
																	<i class="fa fa-edit"></i>
																</a>
																<a href="#" class="text-danger">
																	<i class="fa fa-times"></i>
																</a>
															</div>
															-->
														</li>
														<li>
															<a href="product_revision_view.php?id=1" class="menu-item"><i class="fa fa-folder"></i> 3.61</a>
															<!--
															<div class="item-options">
																<a href="#">
																	<i class="fa fa-edit"></i>
																</a>
																<a href="#" class="text-danger">
																	<i class="fa fa-times"></i>
																</a>
															</div>
															-->
														</li>
														<li>
															<a href="product_revision_view.php?id=1" class="menu-item"><i class="fa fa-folder"></i> 3.62</a>
															<!--
															<div class="item-options">
																<a href="#">
																	<i class="fa fa-edit"></i>
																</a>
																<a href="#" class="text-danger">
																	<i class="fa fa-times"></i>
																</a>
															</div>
															-->
														</li>
														<li>
															<a href="product_revision_view.php?id=1" class="menu-item"><i class="fa fa-folder"></i> 3.70</a>
															<!--
															<div class="item-options">
																<a href="#">
																	<i class="fa fa-edit"></i>
																</a>
																<a href="#" class="text-danger">
																	<i class="fa fa-times"></i>
																</a>
															</div>
															-->
														</li>
													</ul>
												</div>
											</div>

											<hr class="separator" />

											<div class="sidebar-widget m-none">
												<div class="widget-header">
													<h6 class="title">Labels / Quick Links</h6>
													<span class="widget-toggle">+</span>
												</div>
												<div class="widget-content">
													<ul class="mg-tags">
														<li><a href="#">Other</a></li>
														<li><a href="#">Data</a></li>
														<li><a href="#">Could</a></li>
														<li><a href="#">Appear</a></li>
														<li><a href="#">Here</a></li>
														<li><a href="#">?</a></li>
														<li><a href="#">*</a></li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</menu>
							<div class="inner-body mg-main">

								<div class="inner-toolbar clearfix">
									<ul>
										<li>
											<a href="#" id="mgSelectAll"><i class="fa fa-check-square"></i> <span data-all-text="Select All" data-none-text="Select None">Select All</span></a>
										</li>
										<li>
											<a href="#"><i class="fa fa-pencil"></i> Edit</a>
										</li>
										<li>
											<a href="#"><i class="fa fa-trash-o"></i> Delete</a>
										</li>
										<li class="right" data-sort-source data-sort-id="media-gallery">
											<ul class="nav nav-pills nav-pills-primary">
												<li>
													<label>Filter by Type:</label>
												</li>




													<li class="active">
														<a data-option-value="*" href="#all">All</a>
													</li>



												<?php


												$li = 0;

												$list_part_types_SQL = "SELECT * FROM  `part_type` WHERE `record_status` = 2";
												// echo $get_part_type_SQL;

												$result_list_part_types = mysqli_query($con,$list_part_types_SQL);
												// while loop
												while($row_list_part_types = mysqli_fetch_array($result_list_part_types)) {
													$list_part_type_ID = $row_list_part_types['ID'];
													$list_part_type_EN = $row_list_part_types['name_EN'];
													$list_part_type_CN = $row_list_part_types['name_CN'];

													?>
													<li>
														<a data-option-value="type_<?php echo $list_part_type_ID; ?>" href="#type_<?php echo $list_part_type_ID; ?>">
															<?php echo $list_part_type_EN; if (($list_part_type_CN!='')&&($list_part_type_CN!='中文名')) { ?> / <?php echo $list_part_type_CN; } ?>
														</a>
													</li>
													<?php

												} // END WHILE LOOP
												?>
											</ul>
										</li>
									</ul>
								</div>
								<div class="row mg-files" data-sort-destination data-sort-id="media-gallery">


	<?php

	  if (isset($_REQUEST['type_id'])) {
		$WHERE_SQL = " WHERE `type_ID` = '" . $_REQUEST['type_id'] . "'";
	  }
	  else {
		$WHERE_SQL = "";
	  }


	  if (isset($_REQUEST['sort'])) {
		$order_by = ' ORDER BY `' . $_REQUEST['sort'] . '` ASC';
	  }
	  else {
		$order_by = ' ORDER BY `part_code` ASC';
	  }

	  $get_parts_SQL = "SELECT * FROM `parts`" . $WHERE_SQL . $order_by;
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



								?>

									<!-- START ITEM PRINT -->
									<div class="isotope-item type_<?php echo $part_type_ID; ?> col-sm-6 col-md-4 col-lg-3">
										<div class="thumbnail">
											<div class="thumb-preview">
												<a class="thumb-image" href="<?php echo $rev_photo_location;?>">
													<img src="<?php echo $rev_photo_location;?>" class="img-responsive" alt="<?php echo $row_get_parts['part_code']; ?> - <?php echo $row_get_parts['name_EN']; if (($row_get_parts['name_CN']!='')&&($row_get_parts['name_CN']!='中文名')) { ?> / <?php echo $row_get_parts['name_CN']; } ?>">
												</a>
												<div class="mg-thumb-options">
													<div class="mg-zoom"><i class="fa fa-search"></i></div>
													<div class="mg-toolbar">
														<div class="mg-option checkbox-custom checkbox-inline">
															<input type="checkbox" id="part_<?php echo $part_ID; ?>" value="1">
															<label for="part_<?php echo $part_ID; ?>">SELECT</label>
														</div>
														<div class="mg-group pull-right">
															<a href="#">EDIT</a>
															<button class="dropdown-toggle mg-toggle" type="button" data-toggle="dropdown">
																<i class="fa fa-caret-up"></i>
															</button>
															<ul class="dropdown-menu mg-menu" role="menu">
																<li><a href="#"><i class="fa fa-download"></i> Download</a></li>
																<li><a href="#"><i class="fa fa-trash-o"></i> Delete</a></li>
															</ul>
														</div>
													</div>
												</div>
											</div>
											<h5 class="mg-title text-semibold"><?php echo $rev_part_id; ?> (<?php echo $rev_number; ?>) <small><?php
												if ($part_classification_ID == 1) { ?>

													<i class="fa fa-exclamation-triangle fa-2x text-warning"></i><?php }

												else if ($part_classification_ID == 3) { ?>

													<i class="fa fa-times-circle fa-2x text-danger"></i><?php }
												else { ?>

													<i class="fa fa-check-square fa-2x text-success"></i><?php }


												?></small></h5>
											<div class="mg-description">
												<small class="pull-left text-muted"><?php echo $part_name_EN; if (($part_name_CN!='')&&($part_name_CN!='中文名')) { echo " / " . $part_name_CN; } ?></small>
												<small class="pull-right text-muted"><?php echo $part_type_EN;  if (($part_type_CN!='')&&($part_type_CN!='中文名')) { echo " / " . $part_type_CN; } ?></small>
											</div>
										</div>
									</div>
									<!-- FINISH ITEM PRINT -->




								</div>
							</div>
						</div>
					</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
