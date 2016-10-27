<?php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */
session_start (); /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// now check the user is OK to view this page //
/* //////// require ('page_access.php'); / */
// ///*/
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

$page_id = 99;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Part Revisions</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><span>Part Revisions</span></li>
			</ol>

			<a class="sidebar-right-toggle" data-open="sidebar-right"><i
				class="fa fa-chevron-left"></i></a>
		</div>
	</header>
					  <?php

							// run notifications function:
							$msg = 0;
							if (isset ( $_REQUEST ['msg'] )) {
								$msg = $_REQUEST ['msg'];
							}
							$action = 0;
							if (isset ( $_REQUEST ['action'] )) {
								$action = $_REQUEST ['action'];
							}
							$change_record_id = 0;
							if (isset ( $_REQUEST ['new_record_id'] )) {
								$change_record_id = $_REQUEST ['new_record_id'];
							}
							$page_record_id = 0;
							if (isset ( $record_id )) {
								$page_record_id = $record_id;
							}

							// now run the function:
							notify_me ( $page_id, $msg, $action, $change_record_id, $page_record_id );
							?>

		<!-- start: page -->
		<?php add_button(0, 'part_revision_add'); ?>
		
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-condensed mb-none">
			<tr>
				<th class="text-center">ID</th>
				<th class="text-center">Code</th>
				<th>Part Name</th>
				<th class="text-center">Rev. #</th>
				<th>Remarks</th>
				<th class="text-center">Date Approved</th>
				<th class="text-center">User</th>
				<th class="text-center">Price</th>
				<th class="text-center">Weight</th>
				<th class="text-center">Status</th>

				<th class="text-center"><span class="btn btn-xs btn-default" title="Action"><i class="fa fa-gear"></i></span></th>
			</tr>
						  <?php
								$get_part_revisions_SQL = "SELECT `part_revisions`.`ID`, `parts`.`name_EN` as `part_name_EN` " .
															" , `parts`.`name_CN` as `part_name_CN` " .
															" , `parts`.`ID` as `part_ID` " .
															" , `parts`.`part_code` " .
															" , `part_revisions`.`revision_number` " .
															" , `part_revisions`.`remarks` ".
															" , `part_revisions`.`date_approved` ".
															" , `users`.`ID` as `user_ID` ".
															" , concat(`users`.`first_name`,' ',`users`.`last_name`) as `user_name` ".
															" , `part_revisions`.`price_USD` ".
															" , `part_revisions`.`weight_g` ".
															" , `part_status`.`name_EN` as `status_name_EN` ".
															" FROM `part_revisions`, `parts`, `part_status`, `users`  WHERE ".
															" `part_revisions`.`part_ID` = `parts`.`ID` " .
															" AND `part_revisions`.`status_ID` = `part_status`.`ID` ".
															" AND `part_revisions`.`user_ID` = `users`.`ID` ".
															" AND `part_revisions`.`record_status` = 2 ".
															" ORDER BY `part_revisions`.`ID` ASC";

								$part_revision_count = 0;

								$result_get_part_revision = mysqli_query ( $con, $get_part_revisions_SQL );
								while ( $row_get_part_revision = mysqli_fetch_array ( $result_get_part_revision ) ) {

									?>

			<tr>
			
				<td class="text-center"><?php echo $row_get_part_revision['ID']; ?></td>
				<td class="text-center"><?php part_num($row_get_part_revision['part_ID']); ?></td>
				<td class="text-center"><?php part_name($row_get_part_revision['part_ID']); ?></td>
				<td class="text-center"><?php part_rev($row_get_part_revision['ID']); ?></td>
				<td><?php echo $row_get_part_revision['remarks']; ?></td>
				<td class="text-center"><?php echo date("Y-m-d", strtotime($row_get_part_revision['date_approved'])); ?></td>
				<td class="text-center"><?php get_creator($row_get_part_revision['user_ID']); ?></td>
				<td class="text-center"><?php echo '<acronym title="United States Dollars">$</acronym>' . number_format($row_get_part_revision['price_USD'],2); ?> USD</td>
				<td class="text-center"><?php echo number_format($row_get_part_revision['weight_g'],2); ?><acronym title="grams">g</acronym></td>
				<td class="text-center<?php 
				
				if ($row_get_part_revision['status_name_EN'] == 'Released') {
					?> text-success<?php
				}
				else if ($row_get_part_revision['status_name_EN'] == 'WIP') {
					?> text-warning<?php
				}
				else {
					?> text-danger<?php
				}
				
				?>"><?php echo $row_get_part_revision['status_name_EN']; ?></td>
                <td class="text-center"> 
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->
			
					<?php 
			
					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= 'part_rev_';						// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $row_get_part_revision['ID'];		// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'part_revision_edit'; 			// REQUIRED - specify edit page URL
					$add_URL 			= 'part_revision_add.php'; 				// REQURED - specify add page URL
					$table_name 		= 'part_revisions';					// REQUIRED - which table are we updating?
					$src_page 			= $this_file;						// REQUIRED - this SHOULD be coming from page_functions.php
					$add_VAR 			= ''; 								// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
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
												echo '?' . $add_VAR;  // NOTE THE LEADING '?' <<<
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
			</tr>

						  <?php

									$part_revision_count = $part_revision_count + 1;
								} // end while loop
								?>

			 <tr>
				<th colspan="11" class="text-left">TOTAL: <?php echo $part_revision_count; ?></th>
			</tr>


		</table>
	</div>
	
	<?php
		add_button(0, 'part_revision_add');
		?>
	
	<!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
