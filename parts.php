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


$page_id = 8;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Parts</h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Parts</span></li>
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
						<div class="col-md-11">
						<!-- PART TYPE JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">SELECT PART TYPE / 选择产品零件类型:</option>
                              <option value="parts.php">View All / 看全部</option>
                              <?php 	
										
							$get_part_types_SQL = "SELECT * FROM  `part_type`";
					  		// echo $get_part_types_SQL;
	
					  		$result_get_part_types = mysqli_query($con,$get_part_types_SQL);
					  		// while loop
					  		while($row_get_part_types = mysqli_fetch_array($result_get_part_types)) {
					  			$part_types_ID = $row_get_part_types['ID'];
					  			$part_types_EN = $row_get_part_types['name_EN'];
					  			$part_types_CN = $row_get_part_types['name_CN'];
										
							   ?>
                              <option value="parts.php?type_id=<?php echo $part_types_ID; ?>"><?php echo $part_types_EN; if (($part_types_CN!='')&&($part_types_CN!='中文名')) { ?> / <?php echo $part_types_CN; } ?></option>
                              <?php 
							  } // end get part list 
							  ?>
                              <option value="parts.php">View All / 看全部</option>
                             </select>
                            <!-- / PART TYPE JUMPER -->
						</div>
					
						<div class="row">
					 		<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
					 	</div>
					</div>
					
					
					
					
					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					
					  <tr>
					    <th>Photo</th>
					    <th><a href="parts.php?sort=part_code">Code</a></th>
					    <th><a href="parts.php?sort=name_EN">Name</a></th>
					    <th><a href="parts.php?sort=name_CN">名字</a></th>
					    <th>Rev #</th>
					    <th><a href="parts.php?sort=type_ID">Type</a></th>
					    <th><a href="parts.php?sort=classification_ID">Classification</a></th>
					  </tr>
					  
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
										
									}
									
									// echo "<h1>Revs Found: " . $num_rev_photos_found . "</h1>";
										
									if ($num_rev_photos_found != 0) {
										$rev_photo_location = "assets/images/" . $rev_photo_location . "/" . $rev_photo_filename;
									}
									else {
										$rev_photo_location = "assets/images/no_image_found.jpg";
									}
					  
					  ?>
					  
					  <tr>
					    <td class="text-center"><a href="part_view.php?id=<?php echo $part_ID; ?>"><img src="<?php echo $rev_photo_location; ?>" class="rounded img-responsive" alt="<?php echo $row_get_parts['part_code']; ?> - <?php echo $row_get_parts['name_EN']; if (($row_get_parts['name_CN']!='')&&($row_get_parts['name_CN']!='中文名')) { ?> / <?php echo $row_get_parts['name_CN']; } ?>" style="width:100px;"></a></td>
					    <td><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php echo $part_code; ?></a></td>
					    <td><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php echo $part_name_EN; ?></a></td>
					    <td><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php if (($part_name_CN!='')&&($part_name_CN!='中文名')) { echo $part_name_CN; } ?></a></td>
					    <td><?php echo $rev_number; ?></td>
					    <td><a href="part_type_view.php?id="<?php echo $part_type_ID; ?>"><?php echo $part_type_EN; if (($part_type_CN != '') && ($part_type_CN != '中文名')) { echo ' / ' . $part_type_CN; } ?></a></td>
					    <?php 
					    if ($part_classification_ID == 1) { ?>
					    
							<td class="danger">
						
							<i class="fa fa-exclamation-triangle fa-2x"></i><?php }
							
					    else if ($part_classification_ID == 3) { ?>
					    
							<td class="" style="background: #666666; color: white;">
						
							<i class="fa fa-times-circle fa-2x"></i><?php }
					    else { ?>
					    
							<td class="primary">
						
							<i class="fa fa-check-square fa-2x"></i><?php }
					    
					    
					    ?>
					    
					    <?php echo $part_class_EN; if (($part_class_CN != '') && ($part_class_CN != '中文名')) { echo ' / ' . $part_class_CN; } ?></td>
					  </tr>
					  
					  <?php 
					  
					  $part_count = $part_count + 1;
					  
					  } // end while loop
					  ?>
					  
					  <tr>
					    <th colspan="7">TOTAL: <?php echo $part_count; ?></th>
					  </tr>
					  
					  
					 </table>
					</div>
					
					<div class="row">
					 	<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
					 </div>
					
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>