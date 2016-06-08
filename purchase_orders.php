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

$page_id = 9;

// pull the header and template stuff:
pagehead($page_id); ?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Purchase Orders</h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Purchase Orders</span></li>
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
				<div class="col-md-12">	
					
					 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
					 <thead>
						 <tr>
							<th>P.O. number</th>
							<th>Created Date</th>
							<th># Batches</th>
							<th class="text-center">Actions</th>
						</tr>
					  </thead>
					  <tbody>
					  <?php 
					  $get_POs_SQL = "SELECT * FROM  `purchase_orders` ORDER BY  `PO_number` ASC";
					  // echo $get_mats_SQL;
					  
					  $PO_count = 0;
	
					  $result_get_POs = mysqli_query($con,$get_POs_SQL);
					  // while loop
					  while($row_get_POs = mysqli_fetch_array($result_get_POs)) {
					  
					  ?>
					  
					  <tr>
					    <td><a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>"><?php echo $row_get_POs['PO_number']; ?></a></td>
					    <td><a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>"><?php echo $row_get_POs['created_date']; ?></a></td>
					    <td>
					    <!-- COUNT BATCHES -->
					    <?php 
					    	// count variants for this material
                        	$count_batches_sql = "SELECT COUNT( ID ) FROM  `part_batch` WHERE  `PO_ID` = " . $row_get_POs['ID']; 
                        	$count_batches_query = mysqli_query($con, $count_batches_sql);
                        	$count_batches_row = mysqli_fetch_row($count_batches_query);
                        	$total_batches = $count_batches_row[0];
					    ?>
					    <a href="purchase_order_view.php?id=<?php echo $row_get_POs['ID']; ?>"><?php echo $total_batches; ?></a>
					    <!-- COUNT BATCHES -->
					    </td>
						<td class="text-center">
                   			 <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                  			 <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
               		   	     <a href="purchase_order_edit.php?id=<?php echo $row_get_POs['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-warning"><i class="fa fa-pencil"></i></a>
							 <a href="purchase_order_delete_do.php?id=<?php echo $row_get_POs['ID']; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-danger"><i class="fa fa-trash"></i></a>
               			 </td>
					  </tr>
					  
					  <?php 
					  
					  $PO_count = $PO_count + 1;
					  
					  } // end while loop
					  ?>
					  </tbody>
					  <tfoot>
						<tr>
							<th colspan="3">TOTAL: <?php echo $PO_count; ?></th>
							<th class="text-center"><a href="purchase_order_add.php" class="mb-xs mt-xs mr-xs btn btn-success">ADD NEW +</a>
							</th>
						</tr>
					  </tfoot>
					  
					 </table>
					
					 
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->
<div id="dialog" class="modal-block mfp-hide">
			<section class="panel">
				<header class="panel-heading">
					<h2 class="panel-title">Are you sure?</h2>
				</header>
				<div class="panel-body">
					<div class="modal-wrapper">
						<div class="modal-text">
							<p>Are you sure that you want to delete this row?</p>
						</div>
					</div>
				</div>
				<footer class="panel-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button id="dialogConfirm" class="btn btn-primary">Confirm</button>
							<button id="dialogCancel" class="btn btn-default">Cancel</button>
						</div>
					</div>
				</footer>
			</section>
		</div>
<?php 
// now close the page out:
pagefoot($page_id);

?>