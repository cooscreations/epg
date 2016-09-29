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

$page_id = 71;

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: part_revisions.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	// now get the part rev info:
    $get_part_rev_SQL = "SELECT * FROM `part_revisions` WHERE `ID` = " . $record_id;
    // echo $get_part_rev_SQL;

    $result_get_rev = mysqli_query($con,$get_part_rev_SQL);

    // while loop
    while($row_get_rev = mysqli_fetch_array($result_get_rev)) {
    
        $record_id 				= $row_get_rev['ID'];					//
		$rev_part_ID 			= $row_get_rev['part_ID'];				//
		$rev_revision_number 	= $row_get_rev['revision_number'];		//
		$rev_remarks 			= $row_get_rev['remarks'];				//
		$rev_date_approved		= $row_get_rev['date_approved'];		//
		$rev_user_ID 			= $row_get_rev['user_ID'];				//
		$rev_price_USD 			= $row_get_rev['price_USD'];			//		
		$rev_weight_g 			= $row_get_rev['weight_g'];				//
		$rev_status_ID 			= $row_get_rev['status_ID'];			//
		$rev_material_ID 		= $row_get_rev['material_ID'];			//
		$rev_treatment_ID 		= $row_get_rev['treatment_ID'];			
		$rev_treatment_notes 	= $row_get_rev['treatment_notes'];
		$rev_record_status 		= $row_get_rev['record_status'];		// 

    } // end get info WHILE loop
}

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Edit Part Revision : <?php part_num($rev_part_ID,0); ?> - <?php part_name($rev_part_ID,0); ?> - Revision <?php echo $rev_revision_number; ?></h2>
        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="part_revisions.php">All Revisions</a></li>
                <li><span>Edit Part Revision</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="part_revision_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit Part Revision:</h2>
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
								&nbsp;
							</div>
                        </div>
						
						
						<div class="form-group">
							<label class="col-md-3 control-label">Material:</label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="part_rev_material_ID" required>
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
								<option value="<?php echo $material_id; ?>"<?php if ($rev_material_ID == $material_id) { ?> selected="selected"<?php } ?>><?php echo $material_name_EN;
	
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
									<input type="text" class="form-control" id="inputDefault" placeholder="0.00" name="price_USD" value="<?php echo $rev_price_USD; ?>" />
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
								<input type="text" class="form-control text-right" id="inputDefault" placeholder="0.00" name="weight_g" value="<?php echo $rev_weight_g; ?>" />
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
								<option value="<?php echo $part_rev_status_id; ?>"<?php if ($rev_status_ID == $part_rev_status_id) { ?> selected="selected"<?php } ?>><?php echo $part_rev_status_name_EN;
	
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
								<textarea class="form-control" rows="3" id="textareaDefault" name="remarks" required><?php echo $rev_remarks; ?></textarea>
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
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="<?php echo $rev_date_approved; ?>" name="date_added" required value="<?php echo $rev_date_approved; ?>" />
								</div>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                        
                        <div class="form-group">
							<label class="col-md-3 control-label">User:<span class="required">*</label>
							<div class="col-md-5">
								<?php creator_drop_down($rev_user_ID); ?>
							</div>

							<div class="col-md-1">
								<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>

						</div>

						<div class="form-group">
							<label class="col-md-3 control-label">Record Status:</label>
							<div class="col-md-5">
								<?php record_status_drop_down($rev_record_status); ?>
							</div>





							<div class="col-md-1">
								&nbsp;
							</div>
						</div>

                    </div>

                    <footer class="panel-footer">
                        <?php form_buttons('part_revisions.php', $record_id); ?>
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
pagefoot($page_id);

?>
