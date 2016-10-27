<?php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* /////// require ('page_access.php');  /*/////*/
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

$page_id = 22;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Sample Size Code Letters</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <li><span>Sample Size Code Letters</span></li>
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
 
 	<a href="part_revision_critical_dimensions.php" class="btn btn-xs btn-primary">
 		VIEW CRITICAL DIMENSIONS
 	</a>
 
 </div>
 
 <div class="row">
 <!-- START PANEL - 1 -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Sample Size Code Letters</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->   
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped mb-none table-condensed">
          <thead>  
            <tr>
                <th class="text-center" colspan="2">Lot Size</th>
                <th class="text-center" colspan="3">General Inspection Levels</th>
                <th class="text-center" colspan="4">Special Inspection Levels</th>
            </tr>
            <tr>
                <th class="text-center">Min.</th>
                <th class="text-center">Max.</th>
                <th class="text-center">I</th>
                <th class="text-center">II</th>
                <th class="text-center">III</th>
                <th class="text-center">S1</th>
                <th class="text-center">S2</th>
                <th class="text-center">S3</th>
                <th class="text-center">S4</th>
            </tr>
		  </thead>
		  <tbody>
            <?php
            
			  $get_S1_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'S-1' ORDER BY `order_qty_min` ASC"; // we will start with just one record set:
			  // echo $get_S1_AQL_letters_SQL;

					$result_get_S1_AQL_letters = mysqli_query ( $con, $get_S1_AQL_letters_SQL );
					// while loop
					while ( $row_get_S1_AQL_letters = mysqli_fetch_array ( $result_get_S1_AQL_letters ) ) {
					
						$S1_AQL_letter_ID 		= $row_get_S1_AQL_letters['ID'];
						$S1_AQL_code 			= $row_get_S1_AQL_letters['AQL_code'];
						$S1_order_qty_min 		= $row_get_S1_AQL_letters['order_qty_min'];
						$S1_order_qty_max 		= $row_get_S1_AQL_letters['order_qty_max'];
						$S1_AQL_letter_result 	= $row_get_S1_AQL_letters['AQL_letter_result'];
						
						// Now this seems a little odd, but I'm going to just repeast SQL commands for each level for now ^_^
						
						// 1: I
						$get_I_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'I' AND `order_qty_min` = '" . $S1_order_qty_min . "'"; // we will start with just one record set:
					    // echo $get_I_AQL_letters_SQL;

							$result_get_I_AQL_letters = mysqli_query ( $con, $get_I_AQL_letters_SQL );
							// while loop
							while ( $row_get_I_AQL_letters = mysqli_fetch_array ( $result_get_I_AQL_letters ) ) {
				
								$I_AQL_letter_ID 		= $row_get_I_AQL_letters['ID'];
								$I_AQL_code 			= $row_get_I_AQL_letters['AQL_code'];
								$I_order_qty_min 		= $row_get_I_AQL_letters['order_qty_min'];
								$I_order_qty_max 		= $row_get_I_AQL_letters['order_qty_max'];
								$I_AQL_letter_result 	= $row_get_I_AQL_letters['AQL_letter_result'];
							
							} // end while I
							/* *************************************************************************** */
							/* *************************************************************************** */
							/* *************************************************************************** */
							
							// 2: II
							$get_II_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'II' AND `order_qty_min` = '" . $S1_order_qty_min . "'"; // we will start with just one record set:
							// echo $get_II_AQL_letters_SQL;

								$result_get_II_AQL_letters = mysqli_query ( $con, $get_II_AQL_letters_SQL );
								// while loop
								while ( $row_get_II_AQL_letters = mysqli_fetch_array ( $result_get_II_AQL_letters ) ) {
				
									$II_AQL_letter_ID 		= $row_get_II_AQL_letters['ID'];
									$II_AQL_code 			= $row_get_II_AQL_letters['AQL_code'];
									$II_order_qty_min 		= $row_get_II_AQL_letters['order_qty_min'];
									$II_order_qty_max 		= $row_get_II_AQL_letters['order_qty_max'];
									$II_AQL_letter_result 	= $row_get_II_AQL_letters['AQL_letter_result'];
							
								} // end while II
								/* *************************************************************************** */
								/* *************************************************************************** */
								/* *************************************************************************** */
								
								// 3: III
								$get_III_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'III' AND `order_qty_min` = '" . $S1_order_qty_min . "'"; // we will start with just one record set:
								// echo $get_III_AQL_letters_SQL;

									$result_get_III_AQL_letters = mysqli_query ( $con, $get_III_AQL_letters_SQL );
									// while loop
									while ( $row_get_III_AQL_letters = mysqli_fetch_array ( $result_get_III_AQL_letters ) ) {
				
										$III_AQL_letter_ID 		= $row_get_III_AQL_letters['ID'];
										$III_AQL_code 			= $row_get_III_AQL_letters['AQL_code'];
										$III_order_qty_min 		= $row_get_III_AQL_letters['order_qty_min'];
										$III_order_qty_max 		= $row_get_III_AQL_letters['order_qty_max'];
										$III_AQL_letter_result 	= $row_get_III_AQL_letters['AQL_letter_result'];
							
									} // end while III
									/* *************************************************************************** */
									/* *************************************************************************** */
									/* *************************************************************************** */
									
									// SKIPPING S-1 as we already have that as our data-set to get the min / max QTY
									// *************************************************************************** //
									
									// 5: S-2
									$get_S2_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'S-2' AND `order_qty_min` = '" . $S1_order_qty_min . "'"; // we will start with just one record set:
									// echo $get_S2_AQL_letters_SQL;

										$result_get_S2_AQL_letters = mysqli_query ( $con, $get_S2_AQL_letters_SQL );
										// while loop
										while ( $row_get_S2_AQL_letters = mysqli_fetch_array ( $result_get_S2_AQL_letters ) ) {
				
											$S2_AQL_letter_ID 		= $row_get_S2_AQL_letters['ID'];
											$S2_AQL_code 			= $row_get_S2_AQL_letters['AQL_code'];
											$S2_order_qty_min 		= $row_get_S2_AQL_letters['order_qty_min'];
											$S2_order_qty_max 		= $row_get_S2_AQL_letters['order_qty_max'];
											$S2_AQL_letter_result 	= $row_get_S2_AQL_letters['AQL_letter_result'];
							
										} // end while S-2
										/* *************************************************************************** */
										/* *************************************************************************** */
										/* *************************************************************************** */
										
										// 6: S-3
										$get_S3_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'S-3' AND `order_qty_min` = '" . $S1_order_qty_min . "'"; // we will start with just one record set:
										// echo $get_S3_AQL_letters_SQL;

											$result_get_S3_AQL_letters = mysqli_query ( $con, $get_S3_AQL_letters_SQL );
											// while loop
											while ( $row_get_S3_AQL_letters = mysqli_fetch_array ( $result_get_S3_AQL_letters ) ) {
				
												$S3_AQL_letter_ID 		= $row_get_S3_AQL_letters['ID'];
												$S3_AQL_code 			= $row_get_S3_AQL_letters['AQL_code'];
												$S3_order_qty_min 		= $row_get_S3_AQL_letters['order_qty_min'];
												$S3_order_qty_max 		= $row_get_S3_AQL_letters['order_qty_max'];
												$S3_AQL_letter_result 	= $row_get_S3_AQL_letters['AQL_letter_result'];
							
											} // end while S-3
											/* *************************************************************************** */
											/* *************************************************************************** */
											/* *************************************************************************** */
											
											// 7: S-4
											$get_S4_AQL_letters_SQL = "SELECT * FROM `AQL_letter` WHERE `AQL_code` = 'S-4' AND `order_qty_min` = '" . $S1_order_qty_min . "'"; // we will start with just one record set:
											// echo $get_S4_AQL_letters_SQL;

												$result_get_S4_AQL_letters = mysqli_query ( $con, $get_S4_AQL_letters_SQL );
												// while loop
												while ( $row_get_S4_AQL_letters = mysqli_fetch_array ( $result_get_S4_AQL_letters ) ) {
				
													$S4_AQL_letter_ID 		= $row_get_S4_AQL_letters['ID'];
													$S4_AQL_code 			= $row_get_S4_AQL_letters['AQL_code'];
													$S4_order_qty_min 		= $row_get_S4_AQL_letters['order_qty_min'];
													$S4_order_qty_max 		= $row_get_S4_AQL_letters['order_qty_max'];
													$S4_AQL_letter_result 	= $row_get_S4_AQL_letters['AQL_letter_result'];
							
												} // end while S-4
												/* *************************************************************************** */
												/* *************************************************************************** */
												/* *************************************************************************** */
						?>

					<tr>
						<td class="text-center"><?php echo number_format($S1_order_qty_min); ?></td>
						<td class="text-center"><?php if ($S1_order_qty_min != '500001') { echo number_format($S1_order_qty_max); } else { echo 'and over'; } ?></td>
						<td class="text-center"><?php echo $I_AQL_letter_result; ?></td>
						<td class="text-center"><?php echo $II_AQL_letter_result; ?></td>
						<td class="text-center"><?php echo $III_AQL_letter_result; ?></td>
						<td class="text-center"><?php echo $S1_AQL_letter_result; ?></td>
						<td class="text-center"><?php echo $S2_AQL_letter_result; ?></td>
						<td class="text-center"><?php echo $S3_AQL_letter_result; ?></td>
						<td class="text-center"><?php echo $S4_AQL_letter_result; ?></td>
					</tr>

						<?php
						
				} // end while S-1 loop
				
			?>
		  </tbody>
		  <tfoot>
            <tr>
                <th colspan="9">&nbsp;</th>
            </tr>
          <tfoot>
        </table>
    </div>
    
    <!-- now close the panel -->
								</div>
							</div>
						</section>
					</div> <!-- end row! -->
   
   <!-- 
   
   ******************************************************************************************************************** 
   
   -->
    
 <div class="row">
 <!-- START PANEL - 2 -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Single Sampling Plans for Normal Inspection</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->   
    
    <?php 
    
    $SQL_level_array = array(); // BLANK EMPTY ARRAY
    
    // count AQL levels
	$count_levels_sql 		= "SELECT COUNT(DISTINCT `AQL_level`) FROM `AQL_level`";
	// echo $count_levels_sql;
	$count_levels_query 	= mysqli_query($con, $count_levels_sql);
	$count_levels_row 		= mysqli_fetch_row($count_levels_query);
	$total_levels 			= $count_levels_row[0];
    ?>
    
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped mb-none table-condensed">
          <thead>  
            <tr>
                <th class="text-center" rowspan="3">Sample Size Code Letter</th>
                <th class="text-center" rowspan="3">Sample Size</th>
                <th class="text-center" colspan="<?php echo ( $total_levels * 2 ); ?>">Acceptable Quality Levels (Normal Inspection)</th>
            </tr>
            <tr>
            <?php 
            
            $count_levels = 0;
            $get_AQL_levels_SQL = "SELECT `AQL_level` FROM `AQL_level` GROUP BY `AQL_level` ORDER BY `AQL_level` ASC";
            // echo $get_AQL_levels_SQL;
            $result_get_AQL_levels = mysqli_query ( $con, $get_AQL_levels_SQL );
			// while loop
			while ( $row_get_AQL_levels = mysqli_fetch_array ( $result_get_AQL_levels ) ) {
			
				$AQL_level 			= $row_get_AQL_levels['AQL_level'];
				
				$SQL_level_array[] 	= $row_get_AQL_levels['AQL_level'];
				$count_levels = $count_levels + 1;
             ?>
                <th class="text-center" colspan="2"><?php echo $AQL_level; ?></th>
            <?php 
            } // end get AQL levels
            ?>
            </tr>
            <tr>
            <?php 
            	$ac_re_row_count = 0;
            	
            	while ($ac_re_row_count < $total_levels) {
            ?>
                <th class="text-center" title="Acceptance Number">Ac</th>
                <th class="text-center" title="Rejection Number">Re</th>
            <?php 
            	$ac_re_row_count = $ac_re_row_count + 1;
            } // end loop subheaders
            ?>
            </tr>
		  </thead>
		  <tbody>
            <?php
            
            $start_sample_level = 1.9;
            $sample_record_count = 0;
            
            $get_sample_size_code_letter_list_SQL = "SELECT `letter_code` FROM `AQL_level` GROUP BY `letter_code` ORDER BY `letter_code` ASC";
            // echo $get_sample_size_code_letter_list_SQL;
            
            $result_get_sample_size_code_letter_list = mysqli_query ( $con, $get_sample_size_code_letter_list_SQL );
					// while loop
					while ( $row_get_sample_size_code_letter_list = mysqli_fetch_array ( $result_get_sample_size_code_letter_list ) ) {
					
						$sample_size_code_letter 		= $row_get_sample_size_code_letter_list['letter_code'];
            
            
            			// NOW GET THE SAMPLE SIZE LIST:
            			$get_min_sample_size_SQL = "SELECT DISTINCT `sample_size` FROM `AQL_level` WHERE `sample_size` > '" . $start_sample_level . "' ORDER BY `AQL_level`.`sample_size` ASC LIMIT 0,1";
						// echo $get_min_sample_size_SQL;
						$result_get_min_sample_size = mysqli_query ( $con, $get_min_sample_size_SQL );
								// while loop
								while ( $row_get_min_sample_size = mysqli_fetch_array ( $result_get_min_sample_size ) ) {
					
									$min_sample_size 		= $row_get_min_sample_size['sample_size'];
									// now append sample leve for next look-up!
									$start_sample_level = $start_sample_level + ($min_sample_size / 2);
									$sample_record_count = $sample_record_count + 1;
									
								}
						?>

					<tr>
						<td class="text-center"><?php echo $sample_size_code_letter; ?></td>
						<td class="text-center"><?php echo number_format($min_sample_size); ?></td>
						<?php 
							$AQL_level_row_count = 0;
				
							while ($AQL_level_row_count < $total_levels) {
							
							
							// NOW, let's look-up the database to see if we can find a matching FAIL MAX QTY number
							
							// WHAT DATA DO WE HAVE?
							
							// ARRAY VALUE =  $SQL_level_array[$AQL_level_row_count];
							$this_cell_fail_max_qty = '';
							$get_this_cell_SQL = "SELECT * FROM `AQL_level` WHERE `AQL_level` = '" . $SQL_level_array[$AQL_level_row_count] . "' AND `letter_code` LIKE '" . $sample_size_code_letter . "' AND `sample_size` = '" . $min_sample_size . "'";
							// echo '<h4>' . $get_this_cell_SQL . '</h4>';
							$result_get_this_cell = mysqli_query ( $con, $get_this_cell_SQL );
								// while loop
								while ( $row_get_this_cell = mysqli_fetch_array ( $result_get_this_cell ) ) {
								
									$this_cell_ID				= $row_get_this_cell['ID'];
									$this_cell_AQL_level 		= $row_get_this_cell['AQL_level'];
									$this_cell_fail_max_qty 	= $row_get_this_cell['fail_max_qty'];
									$this_cell_letter_code 		= $row_get_this_cell['letter_code'];
									$this_cell_sample_size 		= $row_get_this_cell['sample_size'];
									
								}
						
						
							if ($this_cell_fail_max_qty != '') {
								?>
								<td class="text-center danger" title="ID: <?php echo $this_cell_ID; ?>">
									<?php echo ($this_cell_fail_max_qty - 1); ?>
								</td>
								<td class="text-center success">
									<?php echo ($this_cell_fail_max_qty); ?>
								</td>
								<?php
							}
							else if ( ( $min_sample_size >= 500 ) && ( $SQL_level_array[$AQL_level_row_count] > '0.400' ) ) {
								?>
								<td class="text-center" title="ID: <?php echo $this_cell_ID; ?>">
								 	<span class="text-danger">21</span>
								</td>
								<td class="text-center">
									<span class="text-success">22</span>
								</td>
							<?php
							}
							else { 
								?>
								<td class="text-center" title="ID: <?php echo $this_cell_ID; ?>">
								 	<span class="text-danger">0</span>
								</td>
								<td class="text-center">
									<span class="text-success">1</span>
								</td>
							<?php
							}
							
							$AQL_level_row_count = $AQL_level_row_count + 1;
						} // end loop result cells
						?>
					</tr>

						<?php
						
				} // end get_sample_size_code_letter_list loop
				
			?>
		  </tbody>
		  <tfoot>
            <tr>
                <th colspan="<?php echo (($total_levels * 2) + 2); ?>">&nbsp;</th>
            </tr>
          <tfoot>
        </table>
    </div>
    
    <!-- now close the panel -->
								</div>
							</div>
						</section>
					</div> <!-- end row! -->
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
