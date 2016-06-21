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

$page_id = 14;

$sort = '';
if (isset($_REQUEST['sort'])){ $sort = $_REQUEST['sort']; }

$sort_dir = 'ASC';
if (isset($_REQUEST['sort_dir'])){ $sort_dir = $_REQUEST['sort_dir']; }

$this_view = '';
if (isset($_REQUEST['view'])){ $this_view = $_REQUEST['view']; }

// pull the header and template stuff:
pagehead($page_id); 


?>



<!-- START MAIN PAGE BODY : -->
				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Shenzhen Warehouse Stock List - <?php if ($this_view == 'latest') { ?>Latest Part Revisions Only<?php } else {?>All Part Revisions<?php } ?></span></h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Warehouse Stock List</li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<div class="row">
					<p class="pull-right"><?php if ($this_view == 'latest') { ?><a href="warehouse_stock_log.php?view=all" class="mb-xs mt-xs mr-xs btn btn-success"><i class="fa fa-eye"></i> SHOW ALL REVISONS</a><?php } else {?><a href="warehouse_stock_log.php?view=latest" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-eye-slash"></i> HIDE OLD REVISIONS</a><?php } ?></p>
					</div>
					
					<div class="row">
					
					
					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					  <tr>
					    <th rowspan="2">Part #</th>
					    <th rowspan="2">Part Rev.</th>
					    <th rowspan="2">Part Name EN</th>
					    <th rowspan="2">中文名</th>
					    <th class="dark center" colspan="3">TOTAL</th>
					    <th class="success center" colspan="2">USEFUL</th>
					    <th class="warning center" colspan="2">QUARANTINE</th>
					    <th class="danger center" colspan="2">SCRAPPED</th>
					  </tr>
					  
					  <tr>
					    <th class="dark">IN</th>
					    <th class="dark">OUT</th>
					    <th class="dark">NOW</th>
					    <th class="success">IN</th>
					    <th class="success">OUT</th>
					    <th class="warning">IN</th>
					    <th class="warning">OUT</th>
					    <th class="danger">IN</th>
					    <th class="danger">OUT</th>
					  </tr>
					  
					  <!-- START DATASET -->
					  <?php 
					  
					  $total_unique_parts = 0;
					  $last_part = 0;
					  
					  $get_unique_part_revs_SQL = "SELECT  
					  `part_batch`.`ID` AS  `part_batch_ID` ,  
					  `part_batch`.`PO_ID` ,  
					  `part_batch`.`part_ID` , 
					  `part_batch`.`batch_number` , 
					  `part_batch`.`part_rev` ,  
					  `part_batch_movement`.`ID` AS  `movement_ID` , 
					  `part_batch_movement`.`part_batch_ID` ,  
					  `part_batch_movement`.`amount_in` , 
					  `part_batch_movement`.`amount_out` ,  
					  `part_batch_movement`.`part_batch_status_ID` , 
					  `part_batch_movement`.`remarks` ,  
					  `part_batch_movement`.`user_ID` ,  
					  `part_batch_movement`.`date` 
					  FROM  `part_batch` , `part_batch_movement` 
					  WHERE  `part_batch_movement`.`part_batch_ID` =  `part_batch`.`ID` 
					  GROUP BY `part_batch`.`part_rev` 
					  ORDER BY `part_batch`.`part_ID` ASC, `part_batch`.`part_rev` DESC";
					
						$result_get_unique_part_revs = mysqli_query($con,$get_unique_part_revs_SQL);
						// while loop
						while($row_get_unique_part_revs = mysqli_fetch_array($result_get_unique_part_revs)) {
	
								// now print each record:  
								$part_batch_id = $row_get_batch['ID'];
					
						$part_batch_ID = $row_get_unique_part_revs['part_batch_ID'];
						$PO_ID = $row_get_unique_part_revs['PO_ID'];
						$part_ID = $row_get_unique_part_revs['part_ID'];
						$batch_number = $row_get_unique_part_revs['batch_number'];
						$part_rev = $row_get_unique_part_revs['part_rev'];
						$movement_ID = $row_get_unique_part_revs['movement_ID'];
						$part_batch_ID = $row_get_unique_part_revs['part_batch_ID'];
						$amount_in = $row_get_unique_part_revs['amount_in'];
						$amount_out = $row_get_unique_part_revs['amount_out'];
						$part_batch_status_ID = $row_get_unique_part_revs['part_batch_status_ID'];
						
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
								
								
								// count revisions for this part
                        		$count_revs_sql = "SELECT COUNT( ID ) FROM  `part_revisions` WHERE  `part_ID` =  '".$part_ID."'"; 
                        		$count_revs_query = mysqli_query($con, $count_revs_sql);
                        		$count_revs_row = mysqli_fetch_row($count_revs_query);
                        		$total_revs = $count_revs_row[0];
								
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
								
								// NOW, let's calculate the numbers we need...
								
								// 1. get all the batches where the part and rev are the same and link the part batch movement table:
								
							// FIRST, LET'S SET AND RESET THE DEFAULT PART COUNTS	
					  $total_in = 0;
					  $total_out = 0;
					  $total_now = $total_in - $total_out;
					  $total_OK_in = 0;
					  $total_OK_out = 0;
					  $total_quarantined_in = 0;
					  $total_quarantined_out = 0;
					  $total_scrapped_in = 0;
					  $total_scrapped_out = 0;
								
								$get_batch_list_SQL = "SELECT 
								`part_batch`.`ID` AS `part_batch_ID`, 
								`part_batch`.`PO_ID`, 
								`part_batch`.`part_ID`, 
								`part_batch`.`batch_number`, 
								`part_batch`.`part_rev`, 
								`part_batch_movement`.`ID` AS `movement_ID`, 
								`part_batch_movement`.`amount_in`, 
								`part_batch_movement`.`amount_out`, 
								`part_batch_movement`.`part_batch_status_ID` 
								FROM `part_batch`, `part_batch_movement` 
								WHERE `part_batch`.`part_ID` = '".$part_ID."' 
								AND `part_batch`.`part_rev` = '".$rev_id."' 
								AND `part_batch_movement`.`part_batch_ID` = `part_batch`.`ID`";
								$result_get_batch_list = mysqli_query($con,$get_batch_list_SQL);
								// while loop
								while($row_get_batch_list = mysqli_fetch_array($result_get_batch_list)) {
									
									// now print each record:  
									$rev_id = $row_get_part_rev['ID'];
									
									$b_part_batch_ID = $row_get_batch_list['part_batch_ID'];
									$b_PO_ID = $row_get_batch_list['PO_ID'];
									$b_part_ID = $row_get_batch_list['part_ID'];
									$b_batch_number = $row_get_batch_list['batch_number'];
									$b_part_rev = $row_get_batch_list['part_rev'];
									$b_movement_ID = $row_get_batch_list['movement_ID'];
									$b_amount_in = $row_get_batch_list['amount_in'];
									$b_amount_out = $row_get_batch_list['amount_out'];
									$b_part_batch_status_ID = $row_get_batch_list['part_batch_status_ID'];
								
								
									// now update stock numbers:
									
									$total_in = $total_in + $b_amount_in;
									$total_out = $total_out + $b_amount_out;
					  				$total_now = $total_in - $total_out;
					  				if ($b_part_batch_status_ID == 6) {  // QUARANTINE
					  					$total_quarantined_in = $total_quarantined_in + $b_amount_in;
					  					$total_quarantined_out = $total_quarantined_out + $b_amount_out;
					  				}
					  				if ($b_part_batch_status_ID == 7) { // SCRAPPED  
					  					$total_scrapped_in = $total_scrapped_in + $b_amount_in;
					  					$total_scrapped_out = $total_scrapped_out + $b_amount_out;
					  				}
					  				$total_OK_in = ($total_in - ($total_quarantined_in + $total_scrapped_in) );
					  				$total_OK_out = ($total_out - ($total_quarantined_out + $total_scrapped_out) );
									
								}
								
					// if we only want to show the latest revision:
					
					if ($this_view == 'latest') {
						if ($part_ID == $last_part) {
							// we already showed the latest one!
							$show_this_row = 0;
						}
						else {
							$show_this_row = 1;
						}
					}
					else {
						$show_this_row = 1;
					}
					
					// NOW LET'S DO THIS!
					if ($show_this_row == 1) { // OK to display:
					  ?>
					    <tr>
					    <td><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php echo $part_code; ?></a></td>
					    <td><?php echo $rev_number; ?></td>
					    <td><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php echo $part_name_EN; ?></a></td>
					    <td><a href="part_view.php?id=<?php echo $part_ID; ?>"><?php echo $part_name_CN; ?></a></td>
					    <td class="text-right"><?php echo number_format($total_in, 0, ".", ","); ?></td>
					    <td class="text-right"><?php echo number_format($total_out, 0, ".", ","); ?></td>
					    <td class="info text-right"><strong><?php echo number_format($total_now, 0, ".", ","); ?></strong></td>
					    <td class="success text-right"><?php echo number_format($total_OK_in, 0, ".", ","); ?></td>
					    <td class="success text-right"><?php echo number_format($total_OK_out, 0, ".", ","); ?></td>
					    <td class="warning text-right"><?php echo number_format($total_quarantined_in, 0, ".", ","); ?></td>
					    <td class="warning text-right"><?php echo number_format($total_quarantined_out, 0, ".", ","); ?></td>
					    <td class="danger text-right"><?php echo number_format($total_scrapped_in, 0, ".", ","); ?></td>
					    <td class="danger text-right"><?php echo number_format($total_scrapped_out, 0, ".", ","); ?></td>
					    </tr>
					    <?php 
					  
					    $total_unique_parts = $total_unique_parts + 1;
					    $last_part = $part_ID;
					  
					   } // end show_this_row
					  
					  } // END GET BATCH 'WHILE' LOOP
					  
					  ?>
					  <!-- END DATASET -->
					  
					  <tr>
					    <th colspan="4">TOTAL UNIQUE PARTS: <?php echo $total_unique_parts ;?></th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					    <th>&nbsp;</th>
					  </tr>
					  
					  
					 </table>
					</div>
					
								
					</div> <!-- end row! -->
					 
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>