<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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

$record_id = 0;

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: IQC_reports.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	// now get the country info:
    $get_record_SQL = "SELECT * FROM `IQC_report` WHERE `ID` = " . $record_id;
    // echo $get_record_SQL;

    $result_get_record = mysqli_query($con,$get_record_SQL);

    // while loop
    while($row_get_record = mysqli_fetch_array($result_get_record)) {
        $record_id = $row_get_record['ID'];
		$IQC_report_num = $row_get_record['IQC_report_num'];
		$batch_ID = $row_get_record['batch_ID'];
		$remarks = $row_get_record['remarks'];
		$test_result = $row_get_record['test_result'];
		$NCR_num = $row_get_record['NCR_num'];
		$reviewer_ID = $row_get_record['reviewer_ID'];
		$review_date = $row_get_record['review_date'];
		$inspector_ID = $row_get_record['inspector_ID'];
		$inspection_date = $row_get_record['inspection_date'];
		$record_status = $row_get_record['record_status'];

    } // end get info WHILE loop
}


// pull the header and template stuff:
pagehead();

?>
<!-- start: page -->

	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			 <form class="form-horizontal form-bordered" action="IQC_report_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit IQC Report Details:</h2>
                    </header>
                    <div class="panel-body">
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">IQC Report #:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="IQC_report_num" value="<?php echo $IQC_report_num; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch #:</label>
                            <div class="col-md-5">
                                <?php batch_num_dropdown($batch_ID); ?>
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->

                        <div class="form-group">
                            <label class="col-md-3 control-label">Remarks:</label>
                            <div class="col-md-5">
                                <textarea class="form-control" rows="3" id="textareaDefault" name="remarks"><?php echo $remarks; ?></textarea>
                            </div>
                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">NCR #:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="NCR_num" value="<?php echo $NCR_num; ?>" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                      <!-- finish form row -->
                      
                      <!-- THIS IS DIFFERENT TO THE ADD FORM! -->
                    <!-- Start a new form row: -->
                        <div class="form-group">
                            <label class="col-md-3 control-label text-danger">Final Test Result:</label>
                            <div class="col-md-5">
                    		  <select data-plugin-selectTwo class="form-control populate" name="test_result">
								<option value="4"<?php if ($test_result == '4') { ?> selected="selected"<?php } ?>>ACCEPTED</option>
								<option value="3"<?php if ($test_result == '3') { ?> selected="selected"<?php } ?>>ACCEPTED UNDER SPECIAL CASE</option>
								<option value="2"<?php if ($test_result == '2') { ?> selected="selected"<?php } ?>>SORTING</option>
								<option value="1"<?php if ($test_result == '1') { ?> selected="selected"<?php } ?>>REJECTED</option>
							  </select>
						    </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                      <!-- finish form row -->
                    
                      <!-- END DIFFERENCE WITH ADD FORM -->
                      <!-- Start a new form row: -->
                        <div class="form-group">
							<label class="col-md-3 control-label">Inspected By:</label>
							<div class="col-md-5">
								<?php creator_drop_down($inspector_ID, 'inspected_by'); ?>
							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
							<label class="col-md-3 control-label">Date Inspected:</label>
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_inspected" value="<?php echo $inspection_date; ?>">
								</div>
							</div>
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
							<label class="col-md-3 control-label">Reviewed By:</label>
							<div class="col-md-5">
								<?php creator_drop_down($reviewer_ID, 'reviewed_by'); ?>
							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
							<label class="col-md-3 control-label">Date Reviewed:</label>
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_reviewed" value="<?php echo $review_date; ?>">
								</div>
							</div>
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                      <!-- finish form row -->

					    <div class="form-group">
							<label class="col-md-3 control-label">Record Status:</label>
							<div class="col-md-5">
								<?php echo record_status_drop_down($record_status); ?>
							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>



                    </div>
                    <footer class="panel-footer">
                        <?php form_buttons('IQC_report_view.php', $record_id); ?>
                        <input type="hidden" name="record_id" value="<?php echo $record_id; ?>" />
                    </footer>
                </section>
                <!-- now close the form -->
            </form>



		</div>

	</div>




	<!-- now close the panel -->
	<!-- end row! -->

	<!-- end: page -->
<?php
// now close the page out:
pagefoot ( $page_id );

?>
