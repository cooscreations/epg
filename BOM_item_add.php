<?php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* //////// require ('page_access.php');  *//////*/
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////

header ( 'Content-Type: text/html; charset=utf-8' );
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

if (isset($_REQUEST['BOM_id'])) {
	$BOM_id = $_REQUEST['BOM_id'];
}
else {
	header("Location: BOM.php?msg=NG&action=view&error=no_id");
	exit();
}

// NOW GET THIS BOM INFO:
$get_this_BOM_list_SQL = "SELECT * FROM `product_BOM` WHERE `ID` = '" . $BOM_id . "' ORDER BY `entry_order` ASC";
$result_get_this_BOM_list = mysqli_query($con,$get_this_BOM_list_SQL);
// while loop
while($row_get_this_BOM_list = mysqli_fetch_array($result_get_this_BOM_list)) {
	$this_BOM_ID 			= $row_get_this_BOM_list['ID']; // should equal $BOM_id
	$this_BOM_part_rev_ID 	= $row_get_this_BOM_list['part_rev_ID']; // look up
	$this_BOM_date_entered 	= $row_get_this_BOM_list['date_entered'];
	$this_BOM_record_status = $row_get_this_BOM_list['record_status'];
	$this_BOM_created_by 	= $row_get_this_BOM_list['created_by'];
	$this_BOM_type 			= $row_get_this_BOM_list['BOM_type'];
	$this_BOM_parent_BOM_ID = $row_get_this_BOM_list['parent_BOM_ID'];
	$this_BOM_entry_order 	= $row_get_this_BOM_list['entry_order'];
	
	// Get the Part ID:
	
	$get_this_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $this_BOM_part_rev_ID . "'";
	$result_get_this_part_ID = mysqli_query($con,$get_this_part_ID_SQL);
	// while loop
	while($row_get_this_part_ID = mysqli_fetch_array($result_get_this_part_ID)) {
		$this_part_ID 			= $row_get_this_part_ID['part_ID'];	
	}
}

$page_id = 99;

// pull the header and template stuff:
pagehead ( $page_id );

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Add Item to BOM # <?php part_num($this_part_ID, 0); ?></h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><a href="BOM.php">All BOM</a></li>
				<li><a href="BOM_view.php?id=<?php echo $BOM_id; ?>">BOM Record</a></li>
				<li><span>Add New BOM Item</span></li>
			</ol>

			<a class="sidebar-right-toggle" data-open="sidebar-right"><i
				class="fa fa-chevron-left"></i></a>
		</div>
	</header>

	<!-- start: page -->

	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			 <form class="form-horizontal form-bordered" action="BOM_item_add_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Add Item to BOM # <?php part_num($this_part_ID, 0); ?> - <?php part_name($this_part_ID, 0); ?></h2>
                    </header>
                    <div class="panel-body">  
                    
                    

                        <div class="form-group">
                            <label class="col-md-3 control-label"><em>(Select Different BOM:)</em></label>
                            <div class="col-md-5">
                            	<input type="hidden" name="product_BOM_ID" value="<?php echo $BOM_id; ?>" />
								<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
								  <option value="#" selected="selected">CHANGE BOM / 选别的BOM:</option>
								  <option value="BOM.php">View All / 看全部</option>
									<?php 
									
									$get_existing_BOM_list_SQL = "SELECT * FROM `product_BOM` WHERE `record_status` = '2' ORDER BY `entry_order` ASC";
									$result_get_existing_BOM_list = mysqli_query($con,$get_existing_BOM_list_SQL);
									// while loop
									while($row_get_existing_BOM_list = mysqli_fetch_array($result_get_existing_BOM_list)) {
										$BOM_ID 			= $row_get_existing_BOM_list['ID'];
										$BOM_part_rev_ID 	= $row_get_existing_BOM_list['part_rev_ID']; // look up
										$BOM_date_entered 	= $row_get_existing_BOM_list['date_entered'];
										$BOM_record_status 	= $row_get_existing_BOM_list['record_status'];
										$BOM_created_by 	= $row_get_existing_BOM_list['created_by'];
										$BOM_type 			= $row_get_existing_BOM_list['BOM_type'];
										$BOM_parent_BOM_ID 	= $row_get_existing_BOM_list['parent_BOM_ID'];
										$BOM_entry_order 	= $row_get_existing_BOM_list['entry_order'];
										
										// Get the Part ID:
										
										$get_existing_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $BOM_part_rev_ID . "'";
										$result_get_existing_part_ID = mysqli_query($con,$get_existing_part_ID_SQL);
										// while loop
										while($row_get_existing_part_ID = mysqli_fetch_array($result_get_existing_part_ID)) {
											$existing_part_ID 			= $row_get_existing_part_ID['part_ID'];	
										}
										
										?>
										<option value="BOM_item_add.php?BOM_id=<?php echo $BOM_ID; ?>">BOM #<?php echo $BOM_ID; ?> - <?php part_num($existing_part_ID, 0); ?> - <?php part_name($existing_part_ID, 0); ?> (<?php echo $BOM_type; ?> assembly)</option>
										<?php 
									} // end while loop
									?>
									</select>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                               
                    
                        <div class="form-group">
                            <label class="col-md-3 control-label">Part Revision<span class="required">*</span>:</label>
                            <div class="col-md-5">
                                <?php part_rev_drop_down(); ?>
                            </div>
                            
							<div class="col-md-1">
								<a href="part_add.php?part_type_ID=10" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Parent Part / Rev.:</label>
                            <div class="col-md-5">
                                <?php part_rev_drop_down(0, 0, 'parent_ID'); ?>
                            </div>
                            
							<div class="col-md-1">
								<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>
                        </div>
                        
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Entry Order:</label>
                            <div class="col-md-5">
							  <input type="text" class="form-control" placeholder="eg. 7.1.1.2" name="entry_order" value="">
                            </div>
                            
                            
                            
							<div class="col-md-1">
								<!-- Modal Basic -->
								<a class="mb-xs mt-xs mr-xs modal-basic btn btn-info pull-right" href="#other_BOM_Items"><i class="fa fa-question-circle-o"></i></a>

								<div id="other_BOM_Items" class="modal-block mfp-hide">
									<section class="panel">
										<header class="panel-heading">
											<h2 class="panel-title">Other Items in this BOM</h2>
										</header>
										<div class="panel-body">
											<div class="modal-wrapper">
												<div class="modal-text">
												<?php 
																								
												// get the BOM item count:
												$count_BOM_items_SQL = "SELECT COUNT(ID) FROM `product_BOM_items` WHERE `product_BOM_ID` = '" . $BOM_id . "' AND `record_status` = '2'"; // maps for this part revision
												// echo "<br />" . $count_BOM_items_SQL . "<br />";
												$count_BOM_items_query = mysqli_query($con, $count_BOM_items_SQL);
												$count_BOM_items_row = mysqli_fetch_row($count_BOM_items_query);
												// Here we have the total row count
												$total_BOM_items = $count_BOM_items_row[0];
												
												if ($total_BOM_items == 0) {
												  ?>
													<p class="text-danger">There are currently no other items in this Bill Of Materials.</p>
												  <?php 
												}
												else {
												
													?>
													<div class="table-responsive">
													 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
													   <thead>
														<tr>
															<th class="text-center">Part #</th>
															<th class="text-center">Part Rev.</th>
															<th class="text-center">Entry Order</th>
														 </tr>
														</thead>
														<tbody>
													<?php
													
													$display_parent_count = 0;
												
													// now get those items:
													$get_modal_BOM_items_SQL = "SELECT * FROM `product_BOM_items` WHERE `product_BOM_ID` = '" . $BOM_id . "' AND `record_status` = '2' ORDER BY `entry_order` ASC";
													$result_get_modal_BOM_items = mysqli_query($con,$get_modal_BOM_items_SQL);
													// while loop
													while($row_get_modal_BOM_items = mysqli_fetch_array($result_get_modal_BOM_items)) {
														$modal_BOM_item_ID 				= $row_get_modal_BOM_items['ID'];
														$modal_BOM_item_product_BOM_ID 	= $row_get_modal_BOM_items['product_BOM_ID'];
														$modal_BOM_item_part_rev_ID 	= $row_get_modal_BOM_items['part_rev_ID'];
														$modal_BOM_item_parent_ID 		= $row_get_modal_BOM_items['parent_ID'];
														$modal_BOM_item_created_by 		= $row_get_modal_BOM_items['created_by'];
														$modal_BOM_item_date_entered 	= $row_get_modal_BOM_items['date_entered'];
														$modal_BOM_item_record_status 	= $row_get_modal_BOM_items['record_status'];
														$modal_BOM_item_entry_order 	= $row_get_modal_BOM_items['entry_order'];
														$modal_BOM_item_usage_qty 		= $row_get_modal_BOM_items['usage_qty'];
														
														if ($display_parent_count == 0) {
															// first row, let's show the parent info!
															
															// get parent part_ID
															$get_parent_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $modal_BOM_item_parent_ID . "'";
															$result_get_parent_part_ID = mysqli_query($con,$get_parent_part_ID_SQL);
															// while loop
															while($row_get_parent_part_ID = mysqli_fetch_array($result_get_parent_part_ID)) {
																$parent_part_ID = $row_get_parent_part_ID['part_ID'];
															}
															?>
														<tr class="primary">
														  <td><?php part_num($parent_part_ID); ?> - <?php part_name($parent_part_ID); ?></td>
														  <td class="text-center"><?php part_rev($modal_BOM_item_parent_ID); ?></td>
														  <td class="text-center">(Parent)</td>
														</tr>
															<?php
															
														} // end of FIRST TIME ONLY parent view
														
														// now get the part ID
														$get_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $modal_BOM_item_part_rev_ID . "'";
														$result_get_part_ID = mysqli_query($con,$get_part_ID_SQL);
														// while loop
														while($row_get_part_ID = mysqli_fetch_array($result_get_part_ID)) {
															$part_ID = $row_get_part_ID['part_ID'];
														}
												  ?>
														<tr>
														  <td><?php part_num($part_ID); ?> - <?php part_name($part_ID); ?></td>
														  <td class="text-center"><?php part_rev($modal_BOM_item_part_rev_ID); ?></td>
														  <td class="text-center"><?php echo $modal_BOM_item_entry_order; ?></td>
														</tr>	
												  <?php
												  
												  	$display_parent_count = 1;
												  } // end get BOM items
												  
												  ?>
														</tbody>
													 </table>
													</div>
												  <?php
												  
												} // end of items found
												?>
												</div>
											</div>
										</div>
										<footer class="panel-footer">
											<div class="row">
												<div class="col-md-12 text-right">
													<!-- <button class="btn btn-primary modal-confirm">Confirm</button> -->
													<button class="btn btn-danger modal-dismiss">Close</button>
												</div>
											</div>
										</footer>
									</section>
								</div>
								<!-- end Modal Basic -->
							</div>
                        </div>
                        

                        <div class="form-group">
                            <label class="col-md-3 control-label">Usage Qty:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="usage_qty">
								  <option value="" selected="selected">Select Usage Qty:</option>
									<?php 
									$start_qty = 1;
									$end_qty = 100;
									while ($start_qty < $end_qty) {
										?>
								        <option value="<?php echo $start_qty; ?>"<?php if ($start_qty == 1) { ?> selected="selected"<?php } ?>><?php echo $start_qty; ?></option>
										<?
										$start_qty = $start_qty + 1;
									}
									?>
								</select>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>



                    </div>
                    <footer class="panel-footer">
                        <?php form_buttons('BOM_view', $record_id, $add_VARS = 'id=' . $BOM_id . ''); ?>
                    </footer>
                </section>
                <!-- now close the form -->
            </form>



		</div>

	</div>




	<!-- now close the panel -->
	<!-- end row! -->

	<!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
