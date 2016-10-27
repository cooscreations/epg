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

if (isset($_REQUEST['inspection_method_ID'])) {
	
	$inspection_method_ID = $_REQUEST['inspection_method_ID'];
		
	// now get the method data:
	$get_this_method_SQL = "SELECT `inspection_method`.`ID` AS `method_ID`, `inspection_method`.`name_EN` AS `method_name_EN`, `inspection_method`.`name_CN` AS `method_name_CN`, `inspection_method`.`description`, `inspection_method_class`.`ID` AS `method_class_ID`, `inspection_method_class`.`name_EN` AS `class_name_EN`, `inspection_method_class`.`name_CN` AS `class_name_CN`, `AQL_level`, `sample_level` 
	FROM `inspection_method` 
	JOIN `inspection_method_class` 
	ON `inspection_method`.`method_class_ID` = `inspection_method_class`.`ID`
	WHERE `inspection_method`.`ID` = '" . $inspection_method_ID . "' 
	AND `inspection_method`.`record_status` = '2'
	AND `inspection_method_class`.`record_status` = '2'";
	$result_get_this_method = mysqli_query($con,$get_this_method_SQL);
	// while loop
	while($row_get_this_method = mysqli_fetch_array($result_get_this_method)) {

		// now print each record:
		$this_method_ID 				= $row_get_this_method['method_ID'];
		$this_method_method_name_EN 	= $row_get_this_method['method_name_EN'];
		$this_method_method_name_CN 	= $row_get_this_method['method_name_CN'];
		$this_method_description 		= $row_get_this_method['description'];
		$this_method_method_class_ID 	= $row_get_this_method['method_class_ID'];
		$this_method_class_name_EN 		= $row_get_this_method['class_name_EN'];
		$this_method_class_name_CN 		= $row_get_this_method['class_name_CN'];
		$this_method_AQL_level 			= $row_get_this_method['AQL_level'];
		$this_method_sample_level 		= $row_get_this_method['sample_level'];
	} // end get method and class loop
	
}
else {
	$inspection_method_ID = '';
}

if ( (isset($_REQUEST['id'])) && ($_REQUEST['id']!=0) ) {

	$record_id = $_REQUEST['id'];
	// WE ARE EDITING!
	$get_crit_dim_SQL = "SELECT * FROM `part_rev_critical_dimensions` WHERE `ID` = " . $record_id;
	// echo '<h1>SQL: ' . $get_crit_dim_SQL . '</h1>';
	$result_get_crit_dim = mysqli_query($con,$get_crit_dim_SQL);

    // while loop
    while($row_get_crit_dim = mysqli_fetch_array($result_get_crit_dim)) {
        
		$record_ID 						= $row_get_crit_dim['ID'];
		$crit_dim_part_revision_ID 		= $row_get_crit_dim['part_revision_ID'];
		$crit_dim_drawing_QC_ID 		= $row_get_crit_dim['drawing_QC_ID'];
		$crit_dim_dimension_type_ID 	= $row_get_crit_dim['dimension_type_ID'];
		$crit_dim_dimension_minimum 	= $row_get_crit_dim['dimension_minimum'];
		$crit_dim_dimension_maximum 	= $row_get_crit_dim['dimension_maximum'];
		$crit_dim_specification_notes 	= $row_get_crit_dim['specification_notes'];
		$crit_dim_inspection_method_ID 	= $row_get_crit_dim['inspection_method_ID'];
		$crit_dim_inspection_level 		= $row_get_crit_dim['inspection_level'];
		$crit_dim_AQL_level 			= $row_get_crit_dim['AQL_level'];
		$crit_dim_record_status 		= $row_get_crit_dim['record_status'];
		$crit_dim_remarks 				= $row_get_crit_dim['remarks'];
		
		// now get the method data:
		$get_this_method_SQL = "SELECT `inspection_method`.`ID` AS `method_ID`, `inspection_method`.`name_EN` AS `method_name_EN`, `inspection_method`.`name_CN` AS `method_name_CN`, `inspection_method`.`description`, `inspection_method_class`.`ID` AS `method_class_ID`, `inspection_method_class`.`name_EN` AS `class_name_EN`, `inspection_method_class`.`name_CN` AS `class_name_CN`, `AQL_level`, `sample_level` 
		FROM `inspection_method` 
		JOIN `inspection_method_class` 
		ON `inspection_method`.`method_class_ID` = `inspection_method_class`.`ID`
		WHERE `inspection_method`.`ID` = '" . $crit_dim_inspection_method_ID . "' 
		AND `inspection_method`.`record_status` = '2'
		AND `inspection_method_class`.`record_status` = '2'";
		$result_get_this_method = mysqli_query($con,$get_this_method_SQL);
		// while loop
		while($row_get_this_method = mysqli_fetch_array($result_get_this_method)) {

			// now print each record:
			$this_method_ID 				= $row_get_this_method['method_ID'];
			$this_method_method_name_EN 	= $row_get_this_method['method_name_EN'];
			$this_method_method_name_CN 	= $row_get_this_method['method_name_CN'];
			$this_method_description 		= $row_get_this_method['description'];
			$this_method_method_class_ID 	= $row_get_this_method['method_class_ID'];
			$this_method_class_name_EN 		= $row_get_this_method['class_name_EN'];
			$this_method_class_name_CN 		= $row_get_this_method['class_name_CN'];
			$this_method_AQL_level 			= $row_get_this_method['AQL_level'];
			$this_method_sample_level 		= $row_get_this_method['sample_level'];
		} // end get method and class loop
		
		// now get the dymension type data:
		$get_this_dimension_type_SQL = "SELECT * FROM `dimension_types` WHERE `ID` = '" . $crit_dim_dimension_type_ID . "'";
		$result_get_this_dimension_type = mysqli_query($con,$get_this_dimension_type_SQL);
		// while loop
		while($row_get_this_dimension_type = mysqli_fetch_array($result_get_this_dimension_type)) {

			// now print each record:
			$this_dimension_type_ID 					= $row_get_this_dimension_type['ID'];
			$this_dimension_type_name_EN 				= $row_get_this_dimension_type['name_EN'];
			$this_dimension_type_name_CN 				= $row_get_this_dimension_type['name_CN'];
			$this_dimension_type_symbol 				= $row_get_this_dimension_type['symbol'];
			$this_dimension_type_unit_of_measurement 	= $row_get_this_dimension_type['unit_of_measurement'];
			$this_dimension_type_icon_code			 	= $row_get_this_dimension_type['icon_code'];
		} // end get dimension type loop
		
		
	}

	$page_title = 'Edit Critical Dimension';
	$form_action = 'EDIT';
}
else {

	$page_title = 'Add Critical Dimension';
	$form_action = 'ADD';
	// WE ARE ADDING!
	// specify default vars for a document record:
		$record_ID 						= '';									// AUTO-INCREMENT (NULL)
		
		if (isset($_REQUEST['part_rev_id'])) {
			$crit_dim_part_revision_ID 		= $_REQUEST['part_rev_id'];
			
			// find out if that exists, then go to the next one?
			// challenge is that is requires you to select that part... 
		
			$get_last_existing_crit_dims_SQL = "SELECT `drawing_QC_ID` FROM `part_rev_critical_dimensions` WHERE `record_status` = '2' AND `part_revision_ID` = '" . $crit_dim_part_revision_ID . "' ORDER BY `part_rev_critical_dimensions`.`drawing_QC_ID` DESC LIMIT 0,1";
			$result_get_last_existing_crit_dims = mysqli_query($con,$get_last_existing_crit_dims_SQL);
			// while loop
			while($row_get_last_existing_crit_dims = mysqli_fetch_array($result_get_last_existing_crit_dims)) {

				// now print each record:
				$last_existing_crit_dim_ID 					= $row_get_last_existing_crit_dims['drawing_QC_ID'];
			}
			
			// if none are found, this will be 1, which is also OK :)
			$crit_dim_drawing_QC_ID = $last_existing_crit_dim_ID + 1;
			
			
		}
		else {
			$crit_dim_part_revision_ID 		= '0';
			$crit_dim_drawing_QC_ID 		= '1'; // first one!
		}
		
		
		if ($this_method_method_class_ID != 1) {
			$crit_dim_dimension_type_ID 	= '4';
		}
		else {
			$crit_dim_dimension_type_ID 	= '0';
		}
		$crit_dim_dimension_minimum 	= '0.0000';
		$crit_dim_dimension_maximum 	= '0.0000';
		$crit_dim_specification_notes	= '';
		$crit_dim_dimension_type_ID		= '1';
		// $crit_dim_inspection_method_ID 	= $row_get_crit_dim['inspection_method_ID']; 	// SHOULD BE PRE-SET ALREADY
		$crit_dim_inspection_level 		= $this_method_sample_level; 						// get from method
		$crit_dim_AQL_level 			= $this_method_AQL_level;							// get from method
		$crit_dim_record_status 		= '2';
		$crit_dim_remarks 				= 'Please help to update this record';
		
}

$page_id = 99;

// pull the header and template stuff:
pagehead($page_id);

?>
<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2><?php echo $page_title ?></h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><a href="part_revision_critical_dimensions.php">All Critical Dimensions</a></li>
                <?php 
                
                /* **************************************** */
                if ($crit_dim_part_revision_ID != 0) {
                	// link to part profile
                	?>
                <li><a href="part_view.php?rev_id=<?php echo $crit_dim_part_revision_ID; ?>">Part Record</a></li>
                	<?php
                }
                /* **************************************** */
                
                
                if ($record_id != '') {
                	?>
                <li><a href="part_rev_critical_dimension_view.php?id=<?php echo $record_id; ?>">View Record</a></li>
                	<?php
                }
                ?>
                <li><span><?php echo $page_title; ?></span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

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

    <!-- start: page -->

    <div class="row">
        <div class="col-md-12">
			
			

            <!-- START THE FORM! -->
            <form class="form-horizontal form-bordered" action="part_revision_critical_dimension_add_do.php" method="post" enctype="multipart/form-data">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Critical Dimension Details:</h2>
                    </header>
                    <div class="panel-body">
                    
                        
                    	<div class="col-md-4 col-lg-9">
                            <div class="panel-body">
        
        <?php 
        
        // UPDATE: MUST select a inspecption method first:

		if ( (!isset($_REQUEST['inspection_method_ID'])) && ($record_id == '') ) {
			// ASK FOR THE METHOD
			// echo '<h1>No Inspection Method Set</h1>'; 
			
			?>
			
			<!-- START FORM ROW -->
			<div class="form-group">
				<label class="col-md-3 control-label">Select Inspection Method:</label>
				<div class="col-md-5">
					
					<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate" name="inspection_method_ID_jump">
						<option value="#" selected="selected">Select Inspection Method:</option>
						<?php 
						// get inspection method classes:
						$get_method_class_list_SQL = "SELECT * FROM `inspection_method_class` WHERE `record_status` = '2'";
						// echo '<h1>SQL: ' . $get_method_class_list_SQL . '</h1>';
	
						$result_get_method_class_list = mysqli_query($con,$get_method_class_list_SQL);

						// while loop
						while($row_get_method_class_list = mysqli_fetch_array($result_get_method_class_list)) {
							$method_class_ID 				= $row_get_method_class_list['ID'];
							$method_class_name_EN 			= $row_get_method_class_list['name_EN'];
							$method_class_name_CN 			= $row_get_method_class_list['name_CN'];
							$method_class_record_status 	= $row_get_method_class_list['record_status'];
							$method_class_AQL_level 		= $row_get_method_class_list['AQL_level'];
							$method_class_sample_level 		= $row_get_method_class_list['sample_level'];
						?>
						<optgroup label="<?php 
							echo $method_class_name_EN; 
							if ( ($method_class_name_CN != '') && ($method_class_name_CN != '中文名') ) {
								echo ' / ' . $method_class_name_CN;
							}
						?>">
							<?php 
							// now get the inspection methods or this class:
							$get_this_class_method_list_SQL = "SELECT * FROM `inspection_method` WHERE `record_status` = '2' AND `method_class_ID` = '" . $method_class_ID . "'";
							$result_get_this_class_method_list = mysqli_query($con,$get_this_class_method_list_SQL);

							// while loop
							while($row_get_this_class_method_list = mysqli_fetch_array($result_get_this_class_method_list)) {
							
								$inspection_method_ID 			= $row_get_this_class_method_list['ID'];
								$inspection_method_name_EN 		= $row_get_this_class_method_list['name_EN'];
								$inspection_method_name_CN 		= $row_get_this_class_method_list['name_CN'];
								$inspection_method_description 	= $row_get_this_class_method_list['description'];
							?>
							<option value="part_revision_critical_dimension_add.php?part_rev_id=<?php echo $crit_dim_part_revision_ID; ?>&inspection_method_ID=<?php echo $inspection_method_ID; ?>"><?php 
								echo $inspection_method_name_EN; 
								if ( ( $inspection_method_name_CN != '' ) && ( $inspection_method_name_CN != '中文名' ) ) {
									echo ' / ' . $inspection_method_name_CN;
								}
							?></option>
							<?php 
							} // end get inspection methods for this class
							?>
						</optgroup>
						<?php 
						} // end get method classes
						?>
					</select>
				</div>

				<div class="col-md-1">
					<a href="inspection_methods.php" target="_blank" title="Click to view Inspection Methods &amp; Classes in a new window" class="btn btn-xs btn-info">Manage</a>
				</div>
			</div>
			<!-- END FORM ROW -->
			
			<?php
			
		}
		else {
			
			// have table - show contents!
			$inspection_method_ID = $_REQUEST['inspection_method_ID'];
			// echo '<h1>Method is ' . $inspection_method_ID . '</h1>';
			
			// OK TO CONTINUE......:
			
		?>
                            
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label text-success">SELECTED METHOD:</label>
                                <div class="col-md-3">
                                
                                    <input type="hidden" name="inspection_method_ID" id="inspection_method_ID" value="<?php echo $inspection_method_ID; ?>" />
									
									<a href="#method" title="METHOD ID: <?php echo $this_method_ID; ?>">
										<?php 
											echo $this_method_method_name_EN; 
											if ( ( $this_method_method_name_CN != '' ) && ( $this_method_method_name_CN != '中文名' ) ) {
												echo ' / ' . $this_method_method_name_CN;
											}
										?>
									</a>
									
									
									<br />
									
									<em>(
									  <a href="#method_class" title="METHOD CLASS ID: <?php echo $this_method_method_class_ID; ?>">
									    <?php 
										  echo $this_method_class_name_EN; 
										  if ( ( $this_method_class_name_CN != '' ) && ( $this_method_class_name_CN != '中文名' ) ) {
											  echo ' / ' . $this_method_class_name_CN;
										  }
									    ?>
									  </a>
									)</em>
                                    
                                    
                                </div>

                                <div class="col-md-2">
                                    
									  <span class="btn btn-xs btn-primary">
										<?php echo $this_method_AQL_level; ?>
									  </span>
									  &nbsp;
									  <span class="btn btn-xs btn-warning">
										<?php echo $this_method_sample_level; ?>
									  </span>
									  
                                </div>

                                <div class="col-md-1">
                                    <a href="part_revision_critical_dimension_add.php?id=<?php echo $record_id; ?>&part_rev_id=<?php echo $crit_dim_part_revision_ID; ?>" class="btn btn-xs btn-info"><em>Change</em></a>
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Part Revision:</label>
                                <div class="col-md-5">
                                    <?php part_rev_drop_down($crit_dim_part_revision_ID); ?>
                                </div>

                                <div class="col-md-1">
                                    <?php add_button(0, 'part_revision_add'); ?>
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Tech. Drawing QC ID:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="drawing_QC_ID" value="Q<?php echo $crit_dim_drawing_QC_ID; ?>" />
                                </div>

                                <div class="col-md-1">
                                    <button type="button" class="btn btn-primary m-xs" data-toggle="popover" data-container="body" data-placement="top" title="Existing QC Drawing Numbers" data-content="<?php 
                                    
                                    if ($crit_dim_drawing_QC_ID == 1) {
                                    	echo 'No other dimensions found. Suggested: Q' . $crit_dim_drawing_QC_ID;
                                    }
                                    else {
                                    	// get existing crit dims:
                                    	$existing_crit_dims_found = 0;
                                    	$get_existing_crit_dims_SQL = "SELECT `drawing_QC_ID` FROM `part_rev_critical_dimensions` WHERE `record_status` = '2' AND `part_revision_ID` = '" . $crit_dim_part_revision_ID . "' ORDER BY `part_rev_critical_dimensions`.`drawing_QC_ID` ASC";
										$result_get_existing_crit_dims = mysqli_query($con,$get_existing_crit_dims_SQL);
										// while loop
										while($row_get_existing_crit_dims = mysqli_fetch_array($result_get_existing_crit_dims)) {

											// now print each record:
											$existing_crit_dim_ID = $row_get_existing_crit_dims['drawing_QC_ID'];
											
											if ($existing_crit_dims_found != 0) { 
												echo ", "; 
											}
											
											echo "Q" . $existing_crit_dim_ID;
											
											$existing_crit_dims_found = $existing_crit_dims_found + 1;
										}
										
                                    	echo ". Suggested: Q" . $crit_dim_drawing_QC_ID;
                                    	
                                    }
                                    
                                    ?>"><i class="fa fa-question"></i></button>
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            <?php 
                            if ($this_method_method_class_ID != 1) {
													
                            	// ask for specification:
                            	?>
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Specification:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="crit_dim_specification_notes" value="<?php echo $crit_dim_specification_notes; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <input type="hidden" name="crit_dim_dimension_minimum" value="<?php echo $crit_dim_dimension_minimum; ?>" />
                            <input type="hidden" name="crit_dim_dimension_maximum" value="<?php echo $crit_dim_dimension_maximum; ?>" />
                            <input type="hidden" name="crit_dim_measurement_type" value="4" />
                            
                            	<?
                            }
                            
                            else {
                            
                            	// it's a dimension - get min, max and type:
                            	?> 
                            	
                            	
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Specification / 规格:</label>
                                
                                <div class="col-md-2">
                                
                                <div class="input-group mb-md">
									<span class="input-group-addon" title="Minimum (mm)">
										<i class="fa fa-compress"></i>
									</span>
									<input type="text" class="form-control" id="inputDefault" name="crit_dim_dimension_minimum" value="<?php echo $crit_dim_dimension_minimum; ?>" />
								</div>
								
                                </div>
								
                                

                                <div class="col-md-1 text-center">
                                    <i class="fa fa-arrows-h"></i>
                                </div>
                                
                                
                                <div class="col-md-2">
                                
                                <div class="input-group mb-md">
									<span class="input-group-addon" title="Maximum (mm)">
										<i class="fa fa-expand"></i>
									</span>
									<input type="text" class="form-control" id="inputDefault" name="crit_dim_dimension_maximum" value="<?php echo $crit_dim_dimension_maximum; ?>" />
								</div>
								
                                </div>
                                

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            <input type="hidden" name="crit_dim_specification_notes" value="<?php echo $crit_dim_specification_notes; ?>" />
                            
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Dimension Type:</label>
                                <div class="col-md-5">
                                	<select class="form-control populate" name="crit_dim_dimension_type" id="crit_dim_dimension_type" data-plugin-selectTwo>
										<option value="0" selected="selected">Select Dimension Type:</option>
	
									<?php 
	
										// now get the dymension type data:
										$get_this_dimension_type_SQL = "SELECT * FROM `dimension_types`";
										$result_get_this_dimension_type = mysqli_query($con,$get_this_dimension_type_SQL);
										// while loop
										while($row_get_this_dimension_type = mysqli_fetch_array($result_get_this_dimension_type)) {

											// now print each record:
											$this_dimension_type_ID 					= $row_get_this_dimension_type['ID'];
											$this_dimension_type_name_EN 				= $row_get_this_dimension_type['name_EN'];
											$this_dimension_type_name_CN 				= $row_get_this_dimension_type['name_CN'];
											$this_dimension_type_symbol 				= $row_get_this_dimension_type['symbol'];
											$this_dimension_type_unit_of_measurement 	= $row_get_this_dimension_type['unit_of_measurement'];
											$this_dimension_type_icon_code			 	= $row_get_this_dimension_type['icon_code'];

		
										?>
										<option value="<?php echo $file_type_ID; ?>"<?php if ($crit_dim_dimension_type_ID == $this_dimension_type_ID) { ?> selected="selected"<?php } ?>><?php 
											echo $this_dimension_type_name_EN;
											if (($this_dimension_type_name_CN!='')&&($this_dimension_type_name_CN!='中文名')){
												echo ' / ' . $this_dimension_type_name_CN;
											}
										?></option>
										<?php
									
									} // end get dimension type loop
									?>
									</select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            	<? 
                            }
                            ?>
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Inspection Level:</label>
                                <div class="col-md-5">
                                	<select class="form-control populate" name="crit_dim_inspection_level" id="crit_dim_inspection_level" data-plugin-selectTwo>
										<option value="0" selected="selected">Select Inspection Level:</option>
	
									<?php 
	
									$get_crit_dim_inspection_level_SQL = "SELECT * FROM `AQL_letter` GROUP BY `AQL_code`";
									// echo '<h1>SQL: ' . $get_crit_dim_inspection_level_SQL . '</h1>';
	
									$result_get_crit_dim_inspection_level = mysqli_query($con,$get_crit_dim_inspection_level_SQL);

									// while loop
									while($row_get_crit_dim_inspection_level = mysqli_fetch_array($result_get_crit_dim_inspection_level)) {
									
										$inspection_level_list_ID 					= $row_get_crit_dim_inspection_level['ID'];
										$inspection_level_list_AQL_code	 			= $row_get_crit_dim_inspection_level['AQL_code'];
										$inspection_level_list_order_qty_min 		= $row_get_crit_dim_inspection_level['order_qty_min'];
										$inspection_level_list_order_qty_max 		= $row_get_crit_dim_inspection_level['order_qty_max'];
										$inspection_level_list_AQL_letter_result 	= $row_get_crit_dim_inspection_level['AQL_letter_result'];
		
										?>
										<option value="<?php echo $inspection_level_list_AQL_code; ?>"<?php if ($crit_dim_inspection_level == $inspection_level_list_AQL_code) { ?> selected="selected"<?php } ?>><?php 
											echo $inspection_level_list_AQL_code; ?></option>
										<?php
									}
									?>
									</select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">AQL standard:</label>
                                <div class="col-md-5">
                                	<select class="form-control populate" name="crit_dim_AQL_level" id="crit_dim_AQL_level" data-plugin-selectTwo>
										<option value="0" selected="selected">Select Inspection Level:</option>
	
									<?php 
	
									$get_crit_dim_AQL_level_SQL = "SELECT * FROM `AQL_level` GROUP BY `AQL_level`";
									// echo '<h1>SQL: ' . $get_crit_dim_AQL_level_SQL . '</h1>';
	
									$result_get_crit_dim_AQL_level = mysqli_query($con,$get_crit_dim_AQL_level_SQL);

									// while loop
									while($row_get_crit_dim_AQL_level = mysqli_fetch_array($result_get_crit_dim_AQL_level)) {
									
										$AQL_level_list_ID = $row_get_crit_dim_AQL_level['ID'];
									
										$AQL_level_list_ID 				= $row_get_crit_dim_AQL_level['ID'];
										$AQL_level_list_AQL_level 		= $row_get_crit_dim_AQL_level['AQL_level'];
										$AQL_level_list_fail_max_qty 	= $row_get_crit_dim_AQL_level['fail_max_qty'];
										$AQL_level_list_letter_code 	= $row_get_crit_dim_AQL_level['letter_code'];
										$AQL_level_list_sample_size 	= $row_get_crit_dim_AQL_level['sample_size'];
										
										?>
										<option value="<?php echo $AQL_level_list_AQL_level; ?>"<?php if ($crit_dim_AQL_level == $AQL_level_list_AQL_level) { ?> selected="selected"<?php } ?>><?php 
											echo $AQL_level_list_AQL_level; ?></option>
										<?php
									}
									?>
									</select>
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Remarks:</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="inputDefault" name="crit_dim_remarks" value="<?php echo $crit_dim_remarks; ?>" />
                                </div>

                                <div class="col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                            <!-- END FORM ROW -->
                            
                            
                            <!-- START FORM ROW -->
                            <div class="form-group">
								<label class="col-md-3 control-label">Record Status:</label>
								<div class="col-md-5">
									<?php echo record_status_drop_down($crit_dim_record_status); ?>
								</div>
							
								<div class="col-md-1">
									&nbsp;
								</div>
							</div>
                            <!-- END FORM ROW -->
                            
                
                <?php
		} // END OF OK TO CONTINUE
        
        
        ?>
                            
                                </div>
                        </div>

                    </div>
                    
                    <footer class="panel-footer">
                    
                    <div class="row">
					 
					 <?php 
        
        // UPDATE: MUST select a table first:

		if (isset($_REQUEST['inspection_method_ID'])) {
			// ASK FOR THE TABLE
			// echo '<h1>No Table</h1>'; 
			
			?>
                    
						<!-- ADD ANY OTHER HIDDEN VARS HERE -->
					  <div class="col-md-5 text-left">	
					  
						<input type="hidden" value="<?php echo $form_action; ?>" name="form_action" />
						
						<?php form_buttons('critical_dimension_view', $record_id); ?>
					  </div>
					  
					  
					   <!-- NEXT STEP SELECTION -->
							
							<?php 
							if ($_REQUEST['next_step'] == 'view_list') {
								$next_step_selected = 'view_list';
							}
							else {
								$next_step_selected = 'view';
							}
							?>
							
							<label class="col-md-1 control-label text-right">...and then...</label>
							
							<div class="col-md-6 text-left">
								<div class="radio-custom radio-success">
									<input type="radio" id="next_step" name="next_step" value="view_record"<?php if ($next_step_selected == 'view') { ?> checked="checked"<?php } ?>>
									<label for="radioExample9">View Critical Dimension</label>
								</div>

								<div class="radio-custom radio-warning">
									<input type="radio" id="next_step" name="next_step" value="view_list"<?php if ($next_step_selected == 'view_list') { ?> checked="checked"<?php } ?>>
									<label for="radioExample10">View ALL Critical Dimensions</label>
								</div>
							</div>
							
							<!-- END OF NEXT STEP SELECTION -->
							
							<?php 
							
							} // end of hide buttons for table selection 
							
							?>
							
					    </div><!-- END ROW -->
					  
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
