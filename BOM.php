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


$page_id = 63;

// pull the header and template stuff:
pagehead($page_id); ?>

<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
							<h2>Bill of Materials (BOM) Log</h2>
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li><span>BOM Log</span></li>
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
					<?php 
					
					// turning this into a function:
					
					function bom_type_jumper() {
					?>
					<div class="row">
						<div class="col-md-11">
						<!-- BOM TYPE JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">SELECT BOM TYPE / 选择BOM表零件类型:</option>
                              <option value="BOM.php">View All / 看全部</option>
                              <option value="BOM.php?show=final">View Finished Products / 看最终成品</option>
                              <option value="BOM.php?show=sub">View Sub-Assemblies / 看子组件</option>
                             </select>
                            <!-- / PART TYPE JUMPER -->
						</div>
					
						<div class="col-md-1">
					 		<a href="BOM_add.php" class="mb-xs mt-xs mr-xs btn btn-success text-center"><i class="fa fa-plus-square"></i></a>
					 	</div>
					</div>
					<?php 
					} // end of BOM type jumper function
					
					// now run the function
					bom_type_jumper();
					
					?>
					
					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 <thead>
					  <tr>
					    <td>Image</td>
					    <th>Part #</th>
					    <th>Revision</th>
					    <th>Date Entered</th>
					    <th>Type</th>
					  </tr>
					  </thead>
					  
					  <tbody>
					  <?php
					  
					  if (isset($_REQUEST['show'])) { 
					  	$WHERE_SQL = " WHERE `product_BOM`.`record_status` = 2 AND `product_BOM`.`BOM_type` = '" . $_REQUEST['show'] . "'";
					  }
					  else {
					  	$WHERE_SQL = " WHERE `product_BOM`.`record_status` = 2";
					  }
					  
					  $order_by = " ORDER BY `product_BOM`.`entry_order` ASC";
					  
					  $get_BOM_list_SQL = "SELECT * FROM `product_BOM`" . $WHERE_SQL . $order_by;
					  // echo $get_BOM_list_SQL;
					  
					  $BOM_count = 0;
	
					  $result_get_BOM_list = mysqli_query($con,$get_BOM_list_SQL);
					  // while loop
					  while($row_get_BOM_list = mysqli_fetch_array($result_get_BOM_list)) {
					  
					  	// GET BOM LIST:
					  	
					  	$BOM_ID = $row_get_BOM_list['ID'];
					  	$BOM_part_rev_ID = $row_get_BOM_list['part_rev_ID']; // use this to look up
					  	$BOM_date_entered = $row_get_BOM_list['date_entered'];
					  	$BOM_record_status = $row_get_BOM_list['record_status'];
					  	$BOM_created_by = $row_get_BOM_list['created_by'];
					  	$BOM_type = $row_get_BOM_list['BOM_type'];
					  	$BOM_parent_BOM_ID = $row_get_BOM_list['parent_BOM_ID'];
					  	
					  	
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
					  	
					  		$rev_part_join_part_code = $row_get_rev_part_join['part_code'];
							$rev_part_join_name_EN = $row_get_rev_part_join['name_EN'];
							$rev_part_join_name_CN = $row_get_rev_part_join['name_CN'];
							$rev_part_join_type_ID = $row_get_rev_part_join['type_ID'];
							$rev_part_join_rev_num = $row_get_rev_part_join['revision_number'];
							$rev_part_join_part_ID = $row_get_rev_part_join['part_ID'];
							
							} // end get BOM part / part rev data
							
								$num_rev_photos_found = 0;
								$rev_photo_location = "assets/images/no_image_found.jpg";
								
								$get_part_rev_photo_SQL = "SELECT * FROM `documents` WHERE  `lookup_table` LIKE  'part_revisions' AND  `lookup_ID` =" . $BOM_part_rev_ID;
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
									
									if ($rev_photo_filename!='') {
										// now apply filename
										$rev_photo_location = "assets/images/" . $rev_photo_location . "/" . $rev_photo_filename;
									}
									else {
										$rev_photo_location = "assets/images/no_image_found.jpg";
									}
									
								}
								
								// echo "<h1>Revs Found: " . $num_rev_photos_found . "</h1>";
					  ?>
					  
					  <tr>
					    <td>
					      <a href="BOM_view.php?id=<?php echo $BOM_ID; ?>">
					        <img src="<?php 
					    		echo $rev_photo_location; 
					    	?>" class="rounded img-responsive" alt="<?php 
					    		echo $rev_part_join_part_code; 
					    	?> - <?php 
					    		echo $rev_part_join_name_EN; 
					    		
					    		if (($rev_part_join_name_CN!='')&&($rev_part_join_name_CN!='中文名')) { 
					    			echo " / " . $rev_part_join_name_CN; 
					    		} 
					    	?>" style="width:100px;" />
					      </a>
					    </td>
					    <td>
					      <a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>">
					        <?php echo $rev_part_join_part_code; ?> - <?php echo $rev_part_join_name_EN; if (($rev_part_join_name_CN!='')&&($rev_part_join_name_CN!='中文名')) { echo " / " . $rev_part_join_name_CN; }?>
					      </a>
					      <br />
					      
					      <a href="BOM_view.php?id=<?php echo $BOM_ID; ?>" class="btn btn-primary"><i class="fa fa-gears"></i> BOM # <?php echo $BOM_ID; ?></a>
					      <a href="" class="btn btn-info"><i class="fa fa-info-circle"></i> PART #<?php echo $rev_part_join_part_code; ?></a>
					      
					    </td>
					    <td>
					      <a href="part_view.php?id=<?php echo $rev_part_join_part_ID; ?>" class="btn btn-warning">
					    	<?php echo $rev_part_join_rev_num; ?>
					      </a>
					    </td>
					    <td><?php echo date("Y-m-d", strtotime($BOM_date_entered)); ?></td>
					    <td><?php echo $BOM_type; ?></td>
					  </tr>
					  <?php 
					  
					  $BOM_count = $BOM_count + 1;
					  
					  } // end while loop
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
					  // now run the function
					  bom_type_jumper();
					?>
					 
					
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>