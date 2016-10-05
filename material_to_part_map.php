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

$record_id = 0;

if (isset($_REQUEST['rev_id'])) {
	$record_id = $_REQUEST['rev_id'];
}
else {
	header("Location: parts.php?msg=NG&action=view&error=no_id");
	exit();
}

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

/* 
PAGE NOTES:

1. Present a table of all materials and let them select which ones apply to this part revision
2. Include a button for "APPLY TO ALL REVISIONS FOR THIS PART"


*/

// Let's get the revision info first:

$get_rev_SQL = "SELECT * FROM `part_revisions` WHERE `ID` = '" . $record_id . "'";
// debug:
// echo '<h3>'.$get_rev_SQL.'<h3>';
$result_get_rev = mysqli_query($con,$get_rev_SQL);
// while loop
while($row_get_rev = mysqli_fetch_array($result_get_rev)) {

	// now print each result to a variable:
	$rev_ID 				= $row_get_rev['ID'];
	$part_ID 				= $row_get_rev['part_ID'];
	$revision_number 		= $row_get_rev['revision_number'];
	$rev_remarks 			= $row_get_rev['remarks'];
	$rev_date_approved 		= $row_get_rev['date_approved'];
	$rev_user_ID 			= $row_get_rev['user_ID'];
	$rev_price_USD 			= $row_get_rev['price_USD'];
	$rev_weight_g 			= $row_get_rev['weight_g'];
	$rev_status_ID 			= $row_get_rev['status_ID'];
	$rev_material_ID 		= $row_get_rev['material_ID'];
	$rev_treatment_ID 		= $row_get_rev['treatment_ID'];
	$rev_treatment_notes	= $row_get_rev['treatment_notes'];
	$rev_record_status 		= $row_get_rev['record_status'];
}

?>
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Materials for <?php part_num($part_ID, 0); ?> - <?php part_name($part_ID, 0); ?></h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="parts.php">All Parts</a>
									</li>
									<li>
										<a href="part_view.php?id=<?php echo $part_ID; ?>">Part Profile</a>
									</li>
								<li><span>Part Materials</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="material_to_part_map_do.php" method="post">

								
								<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Select Materials</h2>
								</header>
								<div class="panel-body">

									
								  <div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed mb-none">
									  <thead>
										<tr>
											<th class="text-center" width="">Material</th>
											<th class="text-center" width=""><span class="btn btn-xs btn-primary">ALL REVS.</span></th>
									      <?php 
									      	// now create a column for every revision that exists:
									      	$get_all_revs_SQL = "SELECT * FROM `part_revisions` WHERE `part_ID` = '" . $part_ID . "' AND `record_status` = '2' ORDER BY `revision_number` DESC";
									      	$result_get_all_revs = mysqli_query($con,$get_all_revs_SQL);
											$rev_count = 0;
											$push_HTML = array(); 		// BLANK EMPTY ARRAY
											$rev_list_array = array(); 	// BLANK EMPTY ARRAY
											// while loop
											while($row_get_all_revs = mysqli_fetch_array($result_get_all_revs)) {

												// now print each result to a variable:
												$this_rev_ID 					= $row_get_all_revs['ID'];
												$this_part_ID 					= $row_get_all_revs['part_ID'];
												$this_revision_number 			= $row_get_all_revs['revision_number'];
												$this_rev_remarks 				= $row_get_all_revs['remarks'];
												$this_rev_date_approved 		= $row_get_all_revs['date_approved'];
												$this_rev_user_ID 				= $row_get_all_revs['user_ID'];
												$this_rev_price_USD 			= $row_get_all_revs['price_USD'];
												$this_rev_weight_g 				= $row_get_all_revs['weight_g'];
												$this_rev_status_ID 			= $row_get_all_revs['status_ID'];
												$this_rev_material_ID 			= $row_get_all_revs['material_ID'];
												$this_rev_treatment_ID 			= $row_get_all_revs['treatment_ID'];
												$this_rev_treatment_notes		= $row_get_all_revs['treatment_notes'];
												$this_rev_record_status 		= $row_get_all_revs['record_status'];
												
												$rev_count = $rev_count + 1;
												
												$rev_list_array[$this_rev_ID];
												$push_HTML[$this_rev_ID] = $this_rev_ID;
												
									      
									      	?>
											<th class="text-center" width=""><?php part_rev($this_rev_ID); ?></th>
										  	<?php 
										  	
												// now let's build the HTML to inject lower down:
												// CHECK TO SEE IF THIS EXISTS FOR PRE-SELECTION!
											
												// echo '<h1>ARRAY IN ' . $rev_id . ' NOT FOUND!</h1>';
												/*
												$push_HTML[$this_rev_ID] = '
												<td class="text-center">
												  <input type="checkbox" id="mat_1_rev_' . $this_rev_ID . '">
												</td>
												';
												*/
												// echo '<h2>PRINT ARRAY:</h2>';
												// print_r($push_HTML);
												// echo '<h2>COUNT ARRAY IN:</h2>';
												// echo 'COUNT: ' . count($push_HTML);
										  	
										  	
										  	} // end get revisions loop
										  ?>
										</tr>
									  </thead>
									  <tbody>
									  <?php 
									  // NOW LOOP FOR EACH MATERIAL FOUND!
									  $get_mats_SQL = "SELECT * FROM `material` WHERE `record_status` = '2' ORDER BY `name_EN` ASC";
									  // echo $get_mats_SQL;

										$mat_count = 0;

										$result_get_mats = mysqli_query ( $con, $get_mats_SQL );
										// while loop
										while ( $row_get_mats = mysqli_fetch_array ( $result_get_mats ) ) {
										
											$this_mat_id 			= $row_get_mats['ID'];
											$this_mat_name_EN 		= $row_get_mats['name_EN'];
											$this_mat_name_CN 		= $row_get_mats['name_CN'];
											$this_mat_description 	= $row_get_mats['description'];
											$this_mat_record_status	= $row_get_mats['record_status'];
											$this_mat_wiki_URL 		= $row_get_mats['wiki_URL'];
											
											  ?>
											  
<!-- ************************************************************************************************************* -->
<!-- ************************************************************************************************************* -->
<!-- ************************************************************************************************************* -->
<!-- ************************************************************************************************************* -->


							
							<script>
							
							$(".parent").each(function(index){
								var group = $(this).data("group");
								var parent = $(this);

								parent.change(function(){  //"select all" change 
									 $(group).prop('checked', parent.prop("checked"));
								});
								$(group).change(function(){ 
									parent.prop('checked', false);
									if ($(group+':checked').length == $(group).length ){
										parent.prop('checked', true);
									}
								});
							});
							
							</script>

												<tr>
													<td class="text-center">
													  <a href="material_view.php?id=<?php echo $this_mat_id; ?>">
														<?php echo$this_mat_name_EN; 
													
														if (($this_mat_name_CN!='')&&($this_mat_name_CN!='中文名')) {
															echo ' / ' . $this_mat_name_CN;
														}
													
														?>
													  </a>
													</td>
													<td class="text-center">
													  <!-- <input type="checkbox" id="mat_<?php echo $this_mat_id; ?>_rev_all" class="all_revs_<?php echo $this_mat_id; ?>" /> -->
													  <input type="checkbox" class="parent" data-group=".group<?php echo $this_mat_id; ?>" />
													</td>
													<?php 
									  
													  // DEBUG: SHOW THE VALUE OF EACH ARRAY:
									 
														foreach($push_HTML as $rev_ID_key) {
															// echo "Key = " . $rev_ID_key . ", Value in = " . $push_HTML_code;
															// echo "<br>";
															
															// now check to see if the mapping already exists:
															
															$get_existing_map_SQL = "SELECT * FROM `part_to_material_map` WHERE `part_rev_ID` = '" . $rev_ID_key . "' AND `material_ID` = '" . $this_mat_id . "' AND `record_status` = 2";
															
															$existing_map_count = 0;

															$result_get_existing_map = mysqli_query ( $con, $get_existing_map_SQL );
															// while loop
															while ( $row_get_existing_map = mysqli_fetch_array ( $result_get_existing_map ) ) {
										
																$this_existing_map_ID 				= $row_get_existing_map['ID'];
																$this_existing_map_part_rev_ID 		= $row_get_existing_map['part_rev_ID'];
																$this_existing_map_material_ID 		= $row_get_existing_map['material_ID'];
																$this_existing_map_variant_ID 		= $row_get_existing_map['variant_ID'];
																$this_existing_map_record_status 	= $row_get_existing_map['record_status']; // SHOULD BE 2
																
																$existing_map_count = $existing_map_count + 1;
																
															}
															
															?>
															<!-- now output the result of array: -->							  
															<td class="text-center">
																<!-- <input type="checkbox" id="mat_<?php echo $this_mat_id; ?>_rev_<?php echo $rev_ID_key; ?>" class="rev_options_mat_ID_<?php echo $this_mat_id; ?>_rev_<?php echo $rev_ID_key; ?>" />-->
																<input type="checkbox" id="mat_<?php echo $this_mat_id; ?>_rev_<?php echo $rev_ID_key; ?>" class="group<?php echo $this_mat_id; ?>" name="check[]"<?php if ($existing_map_count > 0) { ?> checked="checked"<?php } ?> />
															</td>
															<!-- end result output from array -->
															<?php
														}
													  ?>
												</tr>
												
<!-- ************************************************************************************************************* -->
<!-- ************************************************************************************************************* -->
<!-- ************************************************************************************************************* -->
<!-- ************************************************************************************************************* -->

											  <?php 									  
									  } // end get materials loop
									  
									  ?>
									  </tbody>
									  <tfoot>
									  	<tr>
									  		<td>&nbsp;</td>
									  		<td>&nbsp;</td>
									  		<?php 
									  		$start_foot_loop = 0;
									  		while ($start_foot_loop < $rev_count) {
									  			?>
									  		<td>&nbsp;</td>
									  			<?php
									  			$start_foot_loop = $start_foot_loop + 1;
									  		}
									  		
									  		?>
									  	</tr>
									  </tfoot>
									</table>
								  </div><!-- close responsive table div -->
									  
								</div><!-- close panel body -->


								<footer class="panel-footer">
								
								<div class="row">
								
									<!-- ADD ANY OTHER HIDDEN VARS HERE -->
								  <div class="col-md-5 text-left">	
									<?php form_buttons('purchase_order_view', $record_id); ?>
								  </div>
								  
								  
								   <!-- NEXT STEP SELECTION -->
									    
									    <?php 
									    if ($_REQUEST['next_step'] == 'add') {
									    	$next_step_selected = 'add';
									    }
									    else {
									    	$next_step_selected = 'view';
									    }
									    ?>
									    
										<label class="col-md-1 control-label text-right">...and then...</label>
										
										<div class="col-md-6 text-left">
											<div class="radio-custom radio-success">
												<input type="radio" id="next_step" name="next_step" value="view_record"<?php if ($next_step_selected == 'view') { ?> checked="checked"<?php } ?>>
												<label for="radioExample9">View P.O.</label>
											</div>

											<div class="radio-custom radio-warning">
												<input type="radio" id="next_step" name="next_step" value="view_list"<?php if ($next_step_selected == 'add') { ?> checked="checked"<?php } ?>>
												<label for="radioExample10">View ALL P.O.s</label>
											</div>
										</div>
										
										<!-- END OF NEXT STEP SELECTION -->
								  
								    </div><!-- end row div -->
								  
								</footer>
							</section>
										<!-- now close the form -->
										</form>


						</div>

						</div>




								<!-- now close the panel --><!-- end row! -->

					<!-- end: page -->
				</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
