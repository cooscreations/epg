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
        <h2>Inspection Methods</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <li><span>Inspection Methods</span></li>
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
    
    
    <?php add_button(0, 'inspection_method_add'); ?>
    
    <div class="table-responsive">
        <table
            class="table table-bordered table-striped table-condensed mb-none">
            <tr>
            	<th class="text-center"><i class="fa fa-gear btn btn-default"></i></th>
                <th class="text-center">Name</th>
                <th class="text-center">名字</th>
                <th class="text-center">Desc.</th>
                <th class="text-center">Status</th>
            </tr>

            <?php
			  $get_methods_SQL = "SELECT * FROM  `inspection_method` ORDER BY `name_EN` ASC";
			  // echo $get_methods_SQL;

					$method_count = 0;

					$result_get_methods = mysqli_query ( $con, $get_methods_SQL );
					// while loop
					while ( $row_get_methods = mysqli_fetch_array ( $result_get_methods ) ) {
					
						$method_ID 				= $row_get_methods['ID'];
						$method_name_EN 		= $row_get_methods['name_EN'];
						$method_name_CN 		= $row_get_methods['name_CN'];
						$method_description 	= $row_get_methods['description'];
						$method_record_status 	= $row_get_methods['record_status'];

						?>

            <tr>
            <td class="text-center">
                    
					<!-- ********************************************************* -->
					<!-- START THE ADMIN POP-UP PANEL OPTIONS FOR THIS RECORD SET: -->
					<!-- ********************************************************* -->
			
					<?php 
			
					// VARS YOU NEED TO WATCH / CHANGE:
					$add_to_form_name 	= '';						// OPTIONAL - use if there are more than one group of admin button GROUPS on the page. It's prettier with a trailing '_' :)
					$form_ID 			= $method_ID;				// REQUIRED - What is driving each pop-up's uniqueness? MAY be record_id, may not!
					$edit_URL 			= 'inspection_method_edit'; // REQUIRED - specify edit page URL
					$add_URL 			= 'inspection_method_add'; 	// REQURED - specify add page URL
					$table_name 		= 'inspection_method';		// REQUIRED - which table are we updating?
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
                <td><a
					href="inspection_method_edit.php?id=<?php echo $method_ID; ?>"><?php echo $method_name_EN; ?></a></td>
                <td><a
					href="inspection_method_edit.php?id=<?php echo $method_ID; ?>"><?php if (($method_name_CN!='')&&($method_name_CN!='中文名')) { echo $method_name_CN; } else { echo '<span class="text-danger">没有中文名</span>'; } ?></a></td>
                <td class="text-center"><?php echo $method_description; ?></td>
                <td class="text-center"><?php echo $method_record_status; ?></td>
            </tr>

            <?php

									$con_count = $con_count + 1;
								} // end while loop
								?>

            <tr>
                <th colspan="5">TOTAL: <?php echo $method_count; ?></th>
            </tr>
        </table>
    </div>
    
    <?php add_button(0, 'inspection_method_add'); ?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
