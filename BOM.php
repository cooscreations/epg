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
					
					<div class="row">
						<div class="col-md-11">
						<!-- BOM TYPE JUMPER -->
                            <select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
                              <option value="#" selected="selected">SELECT BOM TYPE / 选择BOM表零件类型:</option>
                              <option value="BOM.php?show=all">View All / 看全部</option>
                              <option value="BOM.php?show=final">View Finished Products</option>
                              <option value="BOM.php?show=sub">View Sub-Assemblies</option>
                             </select>
                            <!-- / PART TYPE JUMPER -->
						</div>
					
						<div class="col-md-1">
					 		<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success text-center"><i class="fa fa-plus-square"></i></a>
					 	</div>
					</div>
					
					
					
					
					
					<div class="table-responsive">
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
					 <thead>
					  <tr>
					    <th>ID</th>
					    <th>Part #</th>
					    <th>Revision</th>
					    <th>Date Entered</th>
					    <th>Type</th>
					  </tr>
					  </thead>
					  
					  <tbody>
					  <?php
					  
					  $WHERE_SQL = " WHERE `record_status` = 2";
					  $order_by = " ORDER BY `date_entered` DESC";
					  
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
					  	
					  	
					  	/* START HERE TOMORROW!
					  	PARTS: `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`description`, `parts`.`type_ID`, `parts`.`classification_ID`, `parts`.`record_status`, `parts`.`product_type_ID`
					  	PART REVISIONS: `part_revisions`.`ID` AS `rev_revision_ID`, `part_revisions`.`part_ID`, `part_revisions`.`revision_number`, `part_revisions`.`remarks`, `part_revisions`.`date_approved`, `part_revisions`.`user_ID`, `part_revisions`.`price_USD`, `part_revisions`.`weight_g`, `part_revisions`.`status_ID`, `part_revisions`.`material_ID`, `part_revisions`.`treatment_ID`, `part_revisions`.`treatment_notes`, `part_revisions`.`record_status`
					  	*/
					  	
					  	$combine_part_and_rev_SQL = "SELECT * FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $BOM_part_rev_ID;
					  
					  ?>
					  
					  <tr>
					    <td><?php echo $BOM_ID; ?></td>
					    <td>01120</td>
					    <td><?php echo $BOM_part_rev_ID; ?></td>
					    <td><?php echo $BOM_date_entered; ?></td>
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
					
					<div class="row">
					 	<a href="BOM_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-left"><i class="fa fa-plus-square"></i></a>
					 </div>
					
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>