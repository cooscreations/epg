<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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

$session_user_id = $_SESSION['username'];

// pull the header and template stuff:
pagehead();

$record_id = NULL;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else if (isset($_REQUEST['part_ID'])) {
	$record_id = $_REQUEST['part_ID'];
}

if ($record_id != 0) {
		$get_parts_SQL = "SELECT * FROM `parts` WHERE `ID` =".$record_id;
		// echo $get_parts_SQL;

		$result_get_parts = mysqli_query($con,$get_parts_SQL);
		// while loop
		while($row_get_parts = mysqli_fetch_array($result_get_parts)) {

			$part_ID = $row_get_parts['ID'];
			$part_code = $row_get_parts['part_code'];
			$part_name_EN = $row_get_parts['name_EN'];
			$part_name_CN = $row_get_parts['name_CN'];
			$part_description = $row_get_parts['description'];
			$part_type_ID = $row_get_parts['type_ID'];
			$part_classification_ID = $row_get_parts['classification_ID'];

			// GET PART TYPE:

			$get_part_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $row_get_parts['type_ID'] . "'";
			// echo $get_part_type_SQL;

			$result_get_part_type = mysqli_query($con,$get_part_type_SQL);
			// while loop
			while($row_get_part_type = mysqli_fetch_array($result_get_part_type)) {
				$part_type_EN = $row_get_part_type['name_EN'];
				$part_type_CN = $row_get_part_type['name_CN'];
			}

			// GET PART CLASSIFICATION:

			$get_part_class_SQL = "SELECT * FROM  `part_classification` WHERE  `ID` ='" . $row_get_parts['classification_ID'] . "'";
			// echo $get_part_class_SQL;

			$result_get_part_class = mysqli_query($con,$get_part_class_SQL);
			// while loop
			while($row_get_part_class = mysqli_fetch_array($result_get_part_class)) {
				$part_class_EN = $row_get_part_class['name_EN'];
				$part_class_CN = $row_get_part_class['name_CN'];
			}

		}
}

?>
					<!-- start: page -->

					<div class="row">
						<div class="col-md-12">

						<!-- START THE FORM! -->
						<form id="form" class="form-horizontal form-bordered" action="part_revision_add_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Add a Part Revision:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Part:</label>
                            <div class="col-md-5">
                                <?php part_drop_down($record_id); ?>
                            </div>

							<div class="col-md-1">
								<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Part Revision Number:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="revision_number" value="<?php echo $rev_revision_number; ?>" />
                            </div>

							<div class="col-md-1">
								<!-- <button type="button" class="mb-xs mt-xs mr-xs btn btn-success pull-right" data-toggle="popover" data-container="body" data-placement="top" title="" data-content="A, B, C, D, E" data-original-title="EXISTING REVISIONS:" aria-describedby="popover876299">?</button> -->
							</div>
                        </div>
						
						<!--
						<div class="form-group">
							<label class="col-md-3 control-label">Material:</label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="part_rev_material_ID" required>
									<option value="" selected="selected">Select:</option>
										<?php
										$get_part_rev_material_SQL = "SELECT * FROM `material` WHERE `record_status` = '2' ORDER BY `name_EN` ASC";
										$result_get_part_rev_material = mysqli_query($con,$get_part_rev_material_SQL);
										// while loop
										while($row_get_part_rev_material = mysqli_fetch_array($result_get_part_rev_material)) {

												// now print each record:
												$material_id 			= $row_get_part_rev_material['ID'];
												$material_name_EN 		= $row_get_part_rev_material['name_EN'];	//
												$material_name_CN 		= $row_get_part_rev_material['name_CN'];	//
												$material_description 	= $row_get_part_rev_material['description'];	//
												$material_record_status	= $row_get_part_rev_material['record_status']; // SHOULD BE 2
												$material_wiki_URL 		= $row_get_part_rev_material['wiki_URL'];
												
										?>
								<option value="<?php echo $material_id; ?>"><?php echo $material_name_EN;
	
								if (($material_name_CN !='')&&($material_name_CN!='中文名')) {
									echo ' / ' . $material_name_CN;
								}
	
	
								?></option>

										<?php
										} // END WHILE LOOP
										?>
							</select>

							</div>

							<div class="col-md-1">
								<a href="material_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>
						</div>
						-->

						<div class="form-group">
							<label class="col-md-3 control-label">Part Treatment:</label>
							<div class="col-md-5">
								This feature is coming soon - please submit feedback if you need it now!
							</div>


							<div class="col-md-1">
								&nbsp;
							</div>
						</div>


						
                        
                        

						<div class="form-group">
							<label class="col-md-3 control-label">Cost Price ($USD):</label>
							<div class="col-md-3">
								
								<div class="input-group mb-md">
									<span class="input-group-addon">$</span>
									<input type="text" class="form-control" id="inputDefault" placeholder="0.00" name="price_USD" value="" />
								</div>
							</div>


							<div class="col-md-3">
								&nbsp;
							</div>
						</div>
                        
                        

						<div class="form-group">
							<label class="col-md-3 control-label">Weight (g):</label>
							<div class="col-md-3">
							  <div class="input-group mb-md">
								<input type="text" class="form-control text-right" id="inputDefault" placeholder="0.00" name="weight_g" value="" />
							 	<span class="input-group-addon ">grams (g)</span>
							  </div>
							</div>


							<div class="col-md-3">
								&nbsp;
							</div>
						</div>
						
						
						<div class="form-group">
							<label class="col-md-3 control-label">Part Revision Status:</label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="part_rev_status_ID" required>
										<?php
										$get_part_rev_status_SQL = "SELECT * FROM `part_status` WHERE `record_status` = '2'";
										$result_get_part_rev_status = mysqli_query($con,$get_part_rev_status_SQL);
										// while loop
										while($row_get_part_rev_status = mysqli_fetch_array($result_get_part_rev_status)) {

												// now print each record:
												$part_rev_status_id = $row_get_part_rev_status['ID'];						//
												$part_rev_status_name_EN = $row_get_part_rev_status['name_EN'];				//
												$part_rev_status_name_CN = $row_get_part_rev_status['name_CN'];				//
												$part_rev_status_description = $row_get_part_rev_status['description'];		//
												$part_rev_status_record_status = $row_get_part_rev_status['record_status'];	// SHOULD BE 2!

										?>
										<option value="<?php echo $part_rev_status_id; ?>"<?php if ($part_rev_status_id == 1) { ?> selected="selected"<?php } ?>><?php echo $part_rev_status_name_EN;
	
											if (($part_rev_status_name_CN !='')&&($part_rev_status_name_CN!='中文名')) {
												echo ' / ' . $part_rev_status_name_CN;
											}
	
										?></option>

										<?php
										} // END WHILE LOOP
										?>
							</select>

							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>


						<div class="form-group">
							<label class="col-md-3 control-label">Remarks:<span class="required">*</span></label>
							<div class="col-md-5">
								<textarea class="form-control" rows="3" id="textareaDefault" name="remarks" required>Please help to update this record.</textarea>
							</div>


							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                        
                        <div class="form-group">
							<label class="col-md-3 control-label">Date:<span class="required">*</span></label>
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="<?php echo $rev_date_approved; ?>" name="date_added" required value="<?php echo date("Y-m-d H:i:s"); ?>" />
								</div>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                        
                        <div class="form-group">
							<label class="col-md-3 control-label">User:<span class="required">*</label>
							<div class="col-md-5">
								<?php creator_drop_down($_SESSION['user_ID']); ?>
							</div>

							<div class="col-md-1">
								<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>

						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">Record Status:</label>
							<div class="col-md-5">
								<?php record_status_drop_down(2); ?>
							</div>





							<div class="col-md-1">
								&nbsp;
							</div>
						</div>

                    </div>

                    <footer class="panel-footer">
                        <?php form_buttons('part_revisions.php',0); ?>
                    </footer>
                </section>
                <!-- now close the form -->
            </form>


						</div>

						</div>




								<!-- now close the panel --><!-- end row! -->

					<!-- end: page -->
<?php
// now close the page out:
pagefoot($page_id);

?>
