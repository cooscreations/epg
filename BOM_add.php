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
		<h2>Add A New Bill of Materials (BOM)</h2>

		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="index.php"> <i class="fa fa-home"></i>
				</a></li>
				<li><a href="BOM.php">All BOM</a></li>
				<li><span>Add New BOM</span></li>
			</ol>

			<a class="sidebar-right-toggle" data-open="sidebar-right"><i
				class="fa fa-chevron-left"></i></a>
		</div>
	</header>

	<!-- start: page -->

	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			 <form class="form-horizontal form-bordered" action="BOM_add_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Add Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Part Revision Assembly:</label>
                            <div class="col-md-5">
                                <?php part_rev_drop_down(0,10); ?>
                            </div>
                            
							<div class="col-md-1">
								<a href="part_add.php?part_type_ID=10" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
							</div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">BOM Type:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="BOM_type">
									<option value="" selected="selected">Select Type:</option>
									<option value="sub">Sub-Assembly</option>
									<option value="final">Final Assembly</option>
									</select>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Parent BOM:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="parent_BOM_ID">
									<option value="0" selected="selected">Ignore / No Parent:</option>
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
										
										$get_this_part_ID_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $BOM_part_rev_ID . "'";
										$result_get_this_part_ID = mysqli_query($con,$get_this_part_ID_SQL);
										// while loop
										while($row_get_this_part_ID = mysqli_fetch_array($result_get_this_part_ID)) {
											$this_part_ID 			= $row_get_this_part_ID['part_ID'];	
										}
										
										?>
										<option value="<?php echo $BOM_ID; ?>">BOM #<?php echo $BOM_ID; ?> - <?php part_name($this_part_ID, 0); ?></option>
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
                            <label class="col-md-3 control-label">Entry Order:</label>
                            <div class="col-md-5">
                                <select data-plugin-selectTwo class="form-control populate" name="entry_order">
									<option value="0" selected="selected">Select List Position:</option>
									<?php 
									$list_start = 1;
									$list_finish = 101;
									while ($list_start < $list_finish) {
										?>
										<option value="<?php echo $list_start; ?>"><?php echo $list_start; ?></option>
										<?php 
										$list_start = $list_start + 1;
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
                        <?php form_buttons('BOM.php', $record_id, $add_VARS = ''); ?>
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
