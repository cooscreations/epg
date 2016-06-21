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
	$parent_ID = $row_get_part['parent_ID'];
	
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
						<h2>Part Profile - <?php echo $part_code; ?> - <?php echo $name_EN; ?> / <?php echo $name_CN; ?></h2>
					
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
							  ?>
                              <option value="parts.php">View All / 看全部</option>
                             </select>
                            <!-- / PART JUMPER -->
						</div>
					</div>
					
					
					<div class="clearfix">&nbsp;</div>
					
					
					
					<div class="tabs tabs-vertical tabs-left">
						<ul class="nav nav-tabs col-sm-1 col-xs-1">
							<?php 
							// show list of part revisions in reverse order, making the most recent one the expanded tab:
							
							// get part revision info:
							// NOTE: I use this query again lower down for the panel body. It's a little sloppy, but it should work :)
								$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `part_ID` ='" . $part_ID . "' ORDER BY `revision_number` DESC";

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
									
									?>
									
						
							<div id="rev_<?php echo $rev_body_id; ?>" class="tab-pane <?php if ($loop_body_count == 1) { ?>active<?php } ?>">
								
								
								<div class="row">
								
									<div class="col-md-4 col-lg-3">
									
									
									
									<section class="panel">
								<div class="panel-body">
									<div class="thumb-info mb-md">
										<img src="assets/images/parts/01312.png" class="rounded img-responsive" alt="<?php echo $part_code; ?>; ?> - Part Name / 名字">
										<div class="thumb-info-title">
											<span class="thumb-info-inner"><?php echo $part_code; ?></span>
											<span class="thumb-info-type">Rev. <?php echo $rev_body_number; ?></span>
										</div>
									</div>
									
									
									<h6 class="text-muted">About</h6>
									<ul>
									  <li><strong>Release Date:</strong> <?php echo $rev_body_date; ?></li>
									  <li><strong>Released By:</strong> <a href="user_view.php?id=<?php echo $rev_body_user; ?>"><?php echo $rev_user_first_name . " " . $rev_user_last_name; if (($rev_user_name_CN != '')&&($rev_user_name_CN != '中文名')) { echo " / " . $rev_user_name_CN; } ?></a></li>
									</ul>
									
								</div>
							</section>
							
							<ul class="simple-card-list mb-xlg">
								<li class="<?php echo $part_class_color; ?>">
									<h3><?php echo $part_class_EN; ?> / <?php echo $part_class_CN; ?></h3>
									<p><?php echo $part_class_description; ?></p>
								</li>
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
										<ul class="simple-user-list">
											<li>
												<figure class="image rounded">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle">
												</figure>
												<span class="title">InsuJet&trade; 3.6</span>
												<span class="message truncate">123 batches</span>
											</li>
											<li>
												<figure class="image rounded">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle">
												</figure>
												<span class="title">InsuJet&trade; 3.5</span>
												<span class="message truncate">123 batches</span>
											</li>
											<li>
												<figure class="image rounded">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle">
												</figure>
												<span class="title">InsuJet&trade; 3.4</span>
												<span class="message truncate">123 batches</span>
											</li>
										</ul>
									</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="#">(View All)</a>
										</div>
								  </div>
								</div>
							</section>
							
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">
										<span class="label label-primary label-sm text-normal va-middle mr-sm">1</span>
										<span class="va-middle">Material(s)</span>
									</h2>
								</header>
								<div class="panel-body">
									<div class="content">
										<ul class="simple-user-list">
											<li>
												<figure class="image rounded">
													<img src="assets/images/!sample-user.jpg" alt="Joseph Doe Junior" class="img-circle">
												</figure>
												<span class="title">PC Makrolon® 2858</span>
												<span class="message truncate">View Material Record</span>
											</li>
										</ul>
									</div>
								  <div class="panel-footer">
									<div class="text-right">
											<a class="text-uppercase text-muted" href="#">(View All)</a>
										</div>
								  </div>
								</div>
							</section>
							
									<!-- END OF LEFT COLUMN: -->
									</div>
									
									<!-- START MAIN BODY COLUMN: -->
									<div class="col-md-8 col-lg-8">
							
							
						
						

						<div class="row">
							
							<h3>Documents</h3>
							
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
						
						<div class="clearfix">&nbsp;</div>
							
									
									
									<div class="row">
							
						<h3>Batch History</h3>
								
					<?php 
					// firstly, let's make sure we have some batches to display...
					
					// count variants for this purchase order
        			$count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_rev` = " . $rev_body_id; 
        			$count_batches_query = mysqli_query($con, $count_batches_sql);
        			$count_batches_row = mysqli_fetch_row($count_batches_query);
        			$total_batches = $count_batches_row[0];
					
					if ($total_batches == 0) {
						?><center>No batches found. <a href="part_batch_add.php?new_record_id=<?php echo $rev_body_id; ?>">Add one?</a></center><?php
					}
					else { // FOUND BATCHES - SHOW THEM!
					
					?>			
								
								
								
					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  <tr>
					    <th>Batch #</th>
					    <th>P.O. #</th>
					    <th>P.O. Date</th>
					  </tr>
					  
					  <!-- START DATASET -->
					  <?php 
					  
					  // get batch list
					  
					  $total_batches = 0; // default
					  
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
								$batch_id = $row_get_batch['ID'];
								$PO_ID = $row_get_batch['PO_ID'];
								$part_ID = $row_get_batch['part_ID'];
								$batch_number = $row_get_batch['batch_number'];
								$part_rev = $row_get_batch['part_rev'];
				
								// GET PART DETAILS:
								$get_part_SQL = "SELECT * FROM `parts` WHERE `ID` = " . $part_ID;
								$result_get_part = mysqli_query($con,$get_part_SQL);
								// while loop
								while($row_get_part = mysqli_fetch_array($result_get_part)) {
				
									// now print each result to a variable:  
									$part_id = $row_get_part['ID'];
									$part_code = $row_get_part['part_code'];
									$part_name_EN = $row_get_part['name_EN'];
									$part_name_CN = $row_get_part['name_CN'];
								
								}
				
				
								// GET P.O. DETAILS:
								$get_PO_SQL = "SELECT * FROM  `purchase_orders` WHERE `ID` = " . $PO_ID;
								$result_get_PO = mysqli_query($con,$get_PO_SQL);
								// while loop
								while($row_get_PO = mysqli_fetch_array($result_get_PO)) {
					
									// now print each record:  
									$PO_id = $row_get_PO['ID'];
									$PO_number = $row_get_PO['PO_number'];
									$PO_created_date = $row_get_PO['created_date'];
									$PO_description = $row_get_PO['description'];
		
								} // end while loop
								
								// get part revision info:
								$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev;
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
					
					// NOW LET'S DO THIS!
					  
					  ?>
					  <tr<?php if ($batch_id == $_REQUEST['new_record_id']) { ?> class="success"<?php } ?>>
					    <td><a href="batch_view.php?id=<?php echo $batch_id; ?>"><?php echo $batch_number; ?></a></td>
					    <td><a href="purchase_order_view.php?id=<?php echo $PO_id; ?>"><?php echo $PO_number; ?></a></td>
					    <td><?php echo $PO_created_date; ?></td>
					  </tr>
					  <?php 
					  
					  
					  
					  $total_batches = $total_batches + 1;
					  } // END GET BATCH 'WHILE' LOOP
					  
					  ?>
					  <!-- END DATASET -->
					  
					  <tr>
					    <th colspan="3">Total batches for rev. <?php echo $rev_number; ?>: <?php echo $total_batches ;
					    
					    		    
											// now count the total batches for ALL revisions:
        									$count_j_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_ID` = " . $part_ID; 
        									$count_j_batches_query = mysqli_query($con, $count_j_batches_sql);
        									$count_j_batches_row = mysqli_fetch_row($count_j_batches_query);
        									$total_j_batches = $count_j_batches_row[0];
					 					    
					 					    if ($total_j_batches > $total_batches) {
					 					    
					 					    	// found even more batches - link to the entire list!
					 					    	echo ' <span style="font-weight:normal;">(<a href="batch_log.php?part_id='.$part_ID.'">VIEW ALL ' . $total_j_batches . ' BATCHES</a>)</span>'; 
					 					     } ?>
					 					     
					 	</th>
					  </tr>
					  
					  
					 </table>
					</div>
								
					<?php 
					
					} // END FOUND BATCHES ELSE STATEMENT
					
					?>
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