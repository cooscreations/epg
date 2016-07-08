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

$page_id = 64;

if (isset($_REQUEST['id'])) { 
	$record_id = $_REQUEST['id']; 
}
else { // no id = nothing to see here!	
	header("Location: BOM.php?msg=NG&action=view&error=no_id");
	exit();		
}

// now get the part info:
$get_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `ID` = " . $record_id;
// echo $get_parts_SQL;

$result_get_BOM = mysqli_query($con,$get_BOM_SQL);

// while loop
while($row_get_BOM = mysqli_fetch_array($result_get_BOM)) {
	$BOM_ID = $row_get_BOM['ID'];
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
						<!-- PART JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">JUMP TO ANOTHER BOM / 看别的:</option>
                              <option value="BOM.php">View All / 看全部</option>
                              <?php 	
								/* 		****** REPLACE THIS WITH THE BOM LOOKUP, BUT SORT BY BOM TYPE - REQUIRES JOIN SQL... 
							$get_j_parts_SQL = "SELECT * FROM `product_BOM`";
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
                              <option value="part_view.php?id=<?php echo $j_part_ID; ?>"><?php echo $j_part_code; ?> - <?php echo $j_part_name_EN; if (($j_part_name_CN != '')&&($j_part_name_CN != '中文名')) { ?> / <?php echo $j_part_name_CN; } ?></option>
                              <?php 
							  } // end get part list 
							  */ 
							  ?>
                              <option value="parts.php">View All / 看全部</option>
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
									  <li><strong>System ID: <?php echo $BOM_ID; ?></strong> </li>
									  <?php if ($BOM_parent_BOM_ID!=0) { ?>
									  	<li><strong>Parent BOM: </strong> <a href="BOM_view.php?id=<?php echo $BOM_parent_BOM_ID; ?>">VIEW</a></li>
									  <?php } ?>
									  <li><strong>Release Date:</strong> <?php echo date("Y-m-d", strtotime($BOM_date_entered)); ?></li>
									  <li><strong>Released By:</strong> <a href="user_view.php?id=<?php echo $rev_body_user; ?>"><?php echo $rev_user_first_name . " " . $rev_user_last_name; if (($rev_user_name_CN != '')&&($rev_user_name_CN != '中文名')) { echo " / " . $rev_user_name_CN; } ?></a></li>
									</ul>
									
								</div>
							</section>
							
									<!-- END OF LEFT COLUMN: -->
									</div>
									
									<!-- START MAIN BODY COLUMN: -->
									<div class="col-md-8 col-lg-8">
							
							
						
						

						<div class="row">
							
							<h3>Components / Sub-Assemblies</h3>
							
							<div class="table-responsive">
							 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  			<tr>
					    			<th>Photo</th>
					    			<th>Code</th>
					    			<th>Name / 名字</th>
					    			<th><abbr title="Revision">Rev.</abbr></th>
					    			<th>Type</th>
					 			 </tr>
					 			 
					 			 <?php 
					 			 
					 			 // GET THE ASSOCIATED PARTS
					 			 $grand_total_components = 0;
								 $total_components = 0;
								 $total_assemblies = 0;
					 			 
					 			 $get_components_SQL = "SELECT * FROM  `product_BOM_items` WHERE  `product_BOM_ID` = " . $record_id . " AND  `record_status` =2";
					 			 
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
					 			  	<a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>">
					 			  		<?php echo $rev_part_join_rev_num; ?>
					 			  	</a>
					 			  </td>
					 			  <td>
					 			  	<?php 
					 			  	
					 			  		echo $component_type_EN; if (($component_type_CN!='')&&($component_type_EN!='中文名')) { echo " / " . $component_type_CN; }
					 			  		
					 			  		}
					 			  		
					 			  		// NOW FIND OUT IF IT'S AN ASSEMBLY, IN WHICH CASE LINK TO THE NEXT BOM!
					 			  		if ($rev_part_join_part_type_ID == 10) {
					 			  		
					 			  			// GO GET THE CHILD INFO!
					 			  			$get_child_BOM_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = 2 AND `parent_BOM_ID` = " . $record_id;
					 			  							 			  			
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
					 			  		
					 			  		 			// LINK TO BOM?!
					 			  		 			echo '<br /><a href="BOM_view.php?id=' . $child_BOM_ID . '">VIEW BOM</a>';
					 			  		 
					 			  		 	}
					 			  	
					 			  	?>
					 			  </td>
					 			</tr>
					 			<?php 
					 			
					 			} // close the loop...
					 			
					 			?>
					 			<tr>
					 			  <th colspan="5">
					 			  	TOTAL COMPONENTS: <?php echo $total_components; ?><br />
					 			  	TOTAL SUB-ASSEMBLIES: <?php echo $total_assemblies; ?><br />
					 			  	TOTAL ITEMS: <?php echo $total_components; ?>
					 			  </th>
					 			</tr>
					 			
					 		</table>
					 	   </div>
							
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