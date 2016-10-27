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
                		<th class="text-center"><i class="fa fa-cog" title="Actions"></i></th>
					    <td class="text-center"><i class="fa fa-file-image-o" title="Photo / 照片"></i></td>
					    <th>Part #</th>
					    <th class="text-center">Revision</th>
					    <th class="text-center">Date Entered</th>
					    <th class="text-center">Type</th>
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

					  	$BOM_ID 			= $row_get_BOM_list['ID'];
					  	$BOM_part_rev_ID 	= $row_get_BOM_list['part_rev_ID']; // use this to look up
					  	$BOM_date_entered 	= $row_get_BOM_list['date_entered'];
					  	$BOM_record_status 	= $row_get_BOM_list['record_status'];
					  	$BOM_created_by 	= $row_get_BOM_list['created_by'];
					  	$BOM_type 			= $row_get_BOM_list['BOM_type'];
					  	$BOM_parent_BOM_ID 	= $row_get_BOM_list['parent_BOM_ID'];

					  	$combine_part_and_rev_SQL = "SELECT `parts`.`part_code`, `parts`.`name_EN`, `parts`.`name_CN`, `parts`.`type_ID`, `part_revisions`.`revision_number`, `part_revisions`.`part_ID` FROM  `part_revisions` LEFT JOIN  `parts` ON  `part_revisions`.`part_ID` =  `parts`.`ID` WHERE `part_revisions`.`ID` =" . $BOM_part_rev_ID . " AND `part_revisions`.`record_status` = 2 AND `parts`.`record_status` = 2";

					    $result_get_rev_part_join = mysqli_query($con,$combine_part_and_rev_SQL);
					    // while loop
					    while($row_get_rev_part_join = mysqli_fetch_array($result_get_rev_part_join)) {

					  		// GET BOM LIST:

					  		$rev_part_join_part_code 	= $row_get_rev_part_join['part_code'];
							$rev_part_join_name_EN 		= $row_get_rev_part_join['name_EN'];
							$rev_part_join_name_CN 		= $row_get_rev_part_join['name_CN'];
							$rev_part_join_type_ID 		= $row_get_rev_part_join['type_ID'];
							$rev_part_join_rev_num 		= $row_get_rev_part_join['revision_number'];
							$rev_part_join_part_ID 		= $row_get_rev_part_join['part_ID'];

							} // end get BOM part / part rev data

							// echo "<h1>Revs Found: " . $num_rev_photos_found . "</h1>";
					  ?>

					  <tr>
						<td class="text-center">
					
							<!-- ********************************************************* -->
							<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
							<!-- ********************************************************* -->
			
							<?php 
			
							// VARS YOU NEED TO WATCH / CHANGE:
							$add_to_form_name 	= 'BOM_';					// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
							$form_ID 			= $BOM_ID;					// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
							$edit_URL 			= 'BOM_edit'; 				// REQUIRED - specify edit page URL
							$add_URL 			= 'BOM_add'; 				// REQURED - specify add page URL
							$table_name 		= 'product_BOM';					// REQUIRED - which table are we updating?
							$src_page 			= $this_file;				// REQUIRED - this SHOULD be coming from page_functions.php
							$add_VAR 			= ''; 						// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
							?>
	 
								<a class="modal-with-form btn btn-default" href="#modalForm_<?php 
				
									echo $add_to_form_name; 
									echo $form_ID; 
				
								?>"><i class="fa fa-gear"></i></a>

								<!-- Modal Form -->
								<div id="modalForm_<?php 
				
									echo $add_to_form_name; 
									echo $form_ID; 
					
								?>" class="modal-block modal-block-primary mfp-hide">
									<section class="panel">
										<header class="panel-heading">
											<h2 class="panel-title">Admin Options</h2>
										</header>
										<div class="panel-body">
				
											<div class="table-responsive">
											 <table class="table table-bordered table-striped table-hover table-condensed mb-none" id="data_table_id">
											 <thead>
												<tr>
													<th class="text-left" colspan="2">Action</th>
													<th>Decsription</th>
												</tr>
											  </thead>
											  <tbody>
												<tr>
												  <td>EDIT</td>
												  <td>
													<a href="<?php 
														echo $edit_URL; 
													?>.php?id=<?php 
														echo $form_ID; 
													?>" class="mb-xs mt-xs mr-xs btn btn-warning">
														<i class="fa fa-pencil" stlye="color: #999"></i>
													</a>
												  </td>
												  <td>Edit this record</td>
												</tr>
												<tr>
												  <td>DELETE</td>
												  <td>
													<a href="record_delete_do.php?table_name=<?php 
														echo $table_name; 
													?>&src_page=<?php 
														echo $src_page; 
													?>&id=<?php 
														echo $form_ID;
														echo '&' . $add_VAR; // NOTE THE LEADING '&' <<<  
													?>" class="mb-xs mt-xs mr-xs btn btn-danger">
														<i class="fa fa-trash modal-icon" stlye="color: #999"></i>
													</a>
												  </td>
												  <td>Delete this record</td>
												</tr>
												<tr>
												  <td>ADD</td>
												  <td>
													<a href="<?php 
														echo $add_URL; 
														echo '.php?' . $add_VAR;  // NOTE THE LEADING '?' <<<
													?>" class="mb-xs mt-xs mr-xs btn btn-success">
														<i class="fa fa-plus" stlye="color: #999"></i>
													</a>
												  </td>
												  <td>Add a similar item to this table</td>
												</tr>
											  </tbody>
											  <tfoot>
												<tr>
												  <td>&nbsp;</td>
												  <td>&nbsp;</td>
												  <td>&nbsp;</td>
												</tr>
											  </tfoot>
											  </table>
											</div><!-- end of responsive table -->	
				
										</div><!-- end panel body -->
										<footer class="panel-footer">
											<div class="row">
												<div class="col-md-12 text-left">
													<button class="btn btn-danger modal-dismiss"><i class="fa fa-times" stlye="color: #999"></i> Cancel</button>
												</div>
											</div>
										</footer>
									</section>
								</div>
		
							<!-- ********************************************************* -->
							<!-- 			   END THE ADMIN POP-UP OPTIONS 			   -->
							<!-- ********************************************************* -->
						</td>
					    <td class="text-center">
					      <a href="BOM_view.php?id=<?php echo $BOM_ID; ?>">
					      	<?php part_img($BOM_part_rev_ID,0); ?>
					      </a>
					    </td>
					    <td>
					      <a href="BOM_view.php?id=<?php echo $BOM_ID; ?>" class="btn btn-primary btn-xs"><i class="fa fa-gears"></i> BOM # <?php echo $BOM_ID; ?></a> - <?php part_num($rev_part_join_part_ID); ?> - <?php part_name($rev_part_join_part_ID); ?>
					    </td>
					    <td class="text-center">
					      <?php part_rev($BOM_part_rev_ID); ?>
					    </td>
					    <td class="text-center"><?php echo date("Y-m-d", strtotime($BOM_date_entered)); ?></td>
					    <td class="text-center"><?php echo $BOM_type; ?></td>
					  </tr>
					  <?php

					  $BOM_count = $BOM_count + 1;

					  } // end while loop
					  ?>
					  </tbody>

					  <tfoot>
					  <tr>
					    <th colspan="6">TOTAL: <?php echo $BOM_count; ?></th>
					  </tr>
					  </tfoot>

					 </table>
					</div>
					
					<br />

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
