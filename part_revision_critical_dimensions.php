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

if (isset($_REQUEST['part_rev_id'])) {
	$add_SQL 			= " AND `part_revision_ID` = '" . $_REQUEST['part_rev_id'] . "'";
	$this_part_rev_id 	= $_REQUEST['part_rev_id'];
	$add_button_URL		= "part_rev_id=" . $this_part_rev_id;
	$nav_add 			= '<li><a href="part_view.php?rev_id='.$this_part_rev_id.'" title="View Part Profile">Part Profile</a></li>';
}
else if (isset($_REQUEST['part_id'])) {
	// reverse engineering - d'oh!
	// get all part_revs for this part ID:
	$get_part_revs_list_SQL = "SELECT * FROM `part_revisions` WHERE `part_ID` = '" . $_REQUEST['part_id'] . "' AND `record_status` = '2'";
	$part_rev_count = 0;

	$result_get_part_revs_list = mysqli_query ( $con, $get_part_revs_list_SQL );
	// while loop
	while ( $row_get_part_revs_list = mysqli_fetch_array ( $result_get_part_revs_list ) ) {
	
		$part_revs_list_ID 					= $row_get_part_revs_list['ID'];
		
		
		// now update SQL query:
		if ($part_rev_count == 0) {
			$add_SQL 			.= " AND `part_revision_ID` = '" . $part_revs_list_ID . "'";
		}
		else {
			$add_SQL 			.= " OR `part_revision_ID` = '" . $part_revs_list_ID . "'";
		}
		$part_rev_count = $part_rev_count + 1;
		
	} // end get part revs
	
	// now update other vars:
	
	$this_part_id 	= $_REQUEST['part_id'];
	$add_button_URL	= "part_id=" . $this_part_id;
	$nav_add = '<li><a href="part_view.php?id='.$this_part_id.'" title="View Part Profile">Part Profile</a></li>';
	
}
else {
	$add_SQL 			= '';
	$this_part_rev_id 	= '';
	$add_button_URL		= '';
	$nav_add			= '';
}

?>

<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Critical Dimensions / 关键尺寸</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <?php echo $nav_add; ?>
                <li><span>Critical Dimensions / 关键尺寸</span></li>
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
    
    <div class="row">
    	<div class="col-md-1">
    		<?php add_button(0, 'part_revision_critical_dimension_add', 'id', 'Click here to add a new record to this table', $add_button_URL); ?>
    	</div>
		<div class="col-md-11">
		<!-- PART JUMPER -->
			<select onChange="document.location = this.value" data-plugin-selectTwo class="form-control populate">
			  <option value="#" selected="selected">JUMP TO ANOTHER PART / 看别的:</option>
			  <option value="part_revision_critical_dimensions.php">View All / 看全部</option>
			  <?php

			$get_j_parts_SQL = "SELECT * FROM `parts` WHERE `record_status` = '2'";
			// echo $get_parts_SQL;

			$result_get_j_parts = mysqli_query($con,$get_j_parts_SQL);
			// while loop
			while($row_get_j_parts = mysqli_fetch_array($result_get_j_parts)) {

				$j_part_ID 					= $row_get_j_parts['ID'];
				$j_part_code 				= $row_get_j_parts['part_code'];
				$j_part_name_EN 			= $row_get_j_parts['name_EN'];
				$j_part_name_CN 			= $row_get_j_parts['name_CN'];
				$j_part_description 		= $row_get_j_parts['description'];
				$j_part_type_ID 			= $row_get_j_parts['type_ID'];
				$j_part_classification_ID 	= $row_get_j_parts['classification_ID'];
				
				$j_get_part_revs_list_SQL = "SELECT * FROM `part_revisions` WHERE `part_ID` = '" . $j_part_ID . "' AND `record_status` = '2'";
				$j_part_rev_count = 0;
			  
			  	$j_add_SQL = ''; // DEFAULT / RESET

				$j_result_get_part_revs_list = mysqli_query ( $con, $j_get_part_revs_list_SQL );
				// while loop
				while ( $j_row_get_part_revs_list = mysqli_fetch_array ( $j_result_get_part_revs_list ) ) {
	
					$j_part_revs_list_ID 					= $j_row_get_part_revs_list['ID'];
		
		
					// now update SQL query:
					if ($j_part_rev_count == 0) {
						$j_add_SQL 			.= " AND `part_revision_ID` = '" . $j_part_revs_list_ID . "'";
					}
					else {
						$j_add_SQL 			.= " OR `part_revision_ID` = '" . $j_part_revs_list_ID . "'";
					}
					$j_part_rev_count = $part_rev_count + 1;
				}
				
				// now count the critical dimensions for this part:
				$count_j_crit_dims_sql 		= "SELECT COUNT( ID ) FROM `part_rev_critical_dimensions` WHERE `record_status` = '2'" . $j_add_SQL . "";
				// echo '<h1>SQL is: ' . $count_j_crit_dims_sql . '</h1>';
				$count_j_crit_dims_query 		= mysqli_query($con, $count_j_crit_dims_sql);
				$count_j_crit_dims_row 		= mysqli_fetch_row($count_j_crit_dims_query);
				$total_j_crit_dims 			= $count_j_crit_dims_row[0];

			   ?>
			  <option value="part_revision_critical_dimensions.php?part_id=<?php echo $j_part_ID; ?>"><?php echo $j_part_code; ?> - <?php echo $j_part_name_EN; if (($j_part_name_CN != '')&&($j_part_name_CN != '中文名')) { ?> / <?php echo $j_part_name_CN; } ?> (<?php echo $total_j_crit_dims; ?>)</option>
			  <?php
			  } // end get part list
			  ?>
			  <option value="part_revision_critical_dimensions.php">View All / 看全部</option>
			 </select>
			<!-- / PART JUMPER -->
		</div>
	</div>
    
    
    <div class="table-responsive">
        <table
            class="table table-bordered table-striped table-condensed mb-none">
            <tr>
            	<th class="text-center"><i class="fa fa-gear btn btn-default"></i></th>
                <th class="text-center">Part</th>
                <th class="text-center">Test Item / 检测项目</th>
                <th class="text-center">AQL</th>
                <th class="text-center">Measurement <br /> 测量</th>
                <th class="text-center">Specification / 规格</th>
                <th class="text-center">Inspection Method <br /> 检测方法</th>
                <th class="text-center"># Batches</th>
            </tr>

            <?php
                          $get_crit_dim_SQL = "SELECT * FROM `part_rev_critical_dimensions` WHERE 1" . $add_SQL . " ORDER BY `part_rev_critical_dimensions`.`part_revision_ID` ASC";
                          // echo '<h1>SQL is: ' . $get_crit_dim_SQL . '</h1>';

								$crit_dim_count = 0;

								$result_get_crit_dim = mysqli_query ( $con, $get_crit_dim_SQL );
								// while loop
								while ( $row_get_crit_dim = mysqli_fetch_array ( $result_get_crit_dim ) ) {
								
									$crit_dim_ID 					= $row_get_crit_dim['ID'];
									$crit_dim_part_revision_ID 		= $row_get_crit_dim['part_revision_ID'];
									$crit_dim_drawing_QC_ID 		= $row_get_crit_dim['drawing_QC_ID'];
									$crit_dim_dimension_type_ID 	= $row_get_crit_dim['dimension_type_ID'];
									$crit_dim_dimension_minimum 	= $row_get_crit_dim['dimension_minimum'];
									$crit_dim_dimension_maximum 	= $row_get_crit_dim['dimension_maximum'];
									$crit_dims_specification_notes  = $row_get_crit_dim['specification_notes'];
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
									
									// count variants for this purchase order
									$count_batches_sql 		= "SELECT COUNT( ID ) FROM  `part_batch` WHERE `part_rev` = '" . $crit_dim_part_revision_ID . "' AND `record_status` = '2'";
									$count_batches_query 	= mysqli_query($con, $count_batches_sql);
									$count_batches_row 		= mysqli_fetch_row($count_batches_query);
									$total_batches 			= $count_batches_row[0];
									
									// get part number:
									$get_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $crit_dim_part_revision_ID . "'";
									$result_get_part_ID = mysqli_query($con,$get_part_ID_SQL);
									// while loop
									while($row_get_part_ID = mysqli_fetch_array($result_get_part_ID)) {

										// now print each record:
										$part_ID 					= $row_get_part_ID['part_ID'];
										
									} // end get part ID

									?>

            <tr>
            
            <td class="text-center">
                    
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->
			
					<?php 
			
					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= '';											// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $crit_dim_ID;									// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'part_revisions_critical_dimension_edit'; 	// REQUIRED - specify edit page URL
					$add_URL 			= 'part_revisions_critical_dimension_add'; 		// REQURED - specify add page URL
					$table_name 		= 'part_rev_critical_dimensions';				// REQUIRED - which table are we updating?
					$src_page 			= $this_file;									// REQUIRED - this SHOULD be coming from page_functions.php
					$add_VAR 			= ''; 											// REQUIRED - DEFAULT = id - this can change, for example when we add a line item to a PO
			
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
            
            	<td class="text-center"><?php 
            		part_name_from_rev($crit_dim_part_revision_ID);
					echo '&nbsp;';
					part_num_from_rev($crit_dim_part_revision_ID);
					echo '&nbsp;';
					part_rev($crit_dim_part_revision_ID); 
				?></td>
            	<td class="text-center"><a href="inpection_method_class_view.php?id=<?php echo $this_method_method_class_ID; ?>"><?php 
            		echo $this_method_class_name_EN;
					if (($this_method_class_name_CN!='')&&($this_method_class_name_CN!='中文名')) {
						 echo ' / ' . $this_method_class_name_CN;
					}
            	?></a></td>
            	<td class="text-center">
            	  <span class="btn btn-xs btn-primary">
            		<?php echo $crit_dim_inspection_level; ?>
            	  </span>
            	  &nbsp;
            	  <span class="btn btn-xs btn-warning">
            		<?php echo $crit_dim_AQL_level; ?>
            	  </span>
            	</td>
            	<td class="text-center"><?php echo 'Q' . $crit_dim_drawing_QC_ID; ?></td>
            	<td class="text-center">
            	<?php 
            	
            	if ($this_method_method_class_ID == 1) { // it's a measurement, let's get the data
            	
            	?>
            	  <span title="<?php 
            	  	echo $this_dimension_type_name_EN;
            	  	if (($this_dimension_type_name_CN!='')&&($this_dimension_type_name_CN!='中文名')) {
            	  		echo ' / ' . $this_dimension_type_name_CN;
            	  	}
            	  ?>">
            		<?php 
            		
            			if ($this_dimension_type_symbol != '') {
            				echo $this_dimension_type_symbol . ' ';
            			}
            		
            			echo $crit_dim_dimension_minimum 
            			. ' ' 
            			. $this_dimension_type_unit_of_measurement 
            			. ' - ' 
            			. $crit_dim_dimension_maximum 
            			. ' ' 
            			. $this_dimension_type_unit_of_measurement; 
            		?>
            		
            	  </span>
            	  <?php 
            	  }
            	  else {
            	  
            	  	// not a measurement, so just show the spec:
            	  	echo $crit_dims_specification_notes;
            	  }
            	  ?>
            	</td>
            	<td class="text-left">
            		<span class="btn btn-xs btn-success">
            		  <i class="fa fa-<?php echo $this_dimension_type_icon_code; ?>"></i>
            		</span>
            		<a href="inpection_method_view.php?id=<?php echo $crit_dim_inspection_method_ID; ?>"><?php 
            		echo $this_method_method_name_EN;
					if (($this_method_method_name_CN!='')&&($this_method_method_name_CN!='中文名')) {
						 echo ' / ' . $this_method_method_name_CN;
					}
            	?></a></td>
            	<td class="text-center">
            	  <a href="batch_log.php?part_id=<?php echo $part_ID; ?>" title="Click to view these batches">
            		<?php echo $total_batches; ?>
            	  </a>
            	</td>
            	
            </tr>

            <?php

									$crit_dim_count = $crit_dim_count + 1;
								} // end while loop
								?>

            <tr>
                <th colspan="9">TOTAL: <?php echo $crit_dim_count; ?></th>
            </tr>
        </table>
    </div>
    
    <?php add_button(0, 'part_revision_critical_dimension_add', 'id', 'Click here to add a new record to this table', $add_button_URL); ?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
