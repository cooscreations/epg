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

// pull the header and template stuff:
pagehead();

?>
	<!-- start: page -->

	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			 <form class="form-horizontal form-bordered" action="IQC_report_add_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Add IQC Report Details:</h2>
                    </header>
                    <div class="panel-body">
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">IQC Report #:</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="inputDefault" name="IQC_report_num" />
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
                                <?php batch_num_dropdown(); ?>
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
                                <textarea class="form-control" rows="3" id="textareaDefault" name="remarks">Please help to update this record.</textarea>
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
                                <input type="text" class="form-control" id="inputDefault" name="NCR_num" />
                            </div>

                            <div class="col-md-1">
                                &nbsp;
                            </div>
                        </div>
                      <!-- finish form row -->
                    
                      <!-- Start a new form row: -->
                        <div class="form-group">
							<label class="col-md-3 control-label">Inspected By:</label>
							<div class="col-md-5">
								<?php creator_drop_down($_SESSION['user_id'], 'inspected_by'); ?>
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
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_inspected">
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
								<?php creator_drop_down(0, 'reviewed_by'); ?>
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
									<input type="text" data-plugin-datepicker data-plugin-options='{"todayHighlight": "true"}' class="form-control" placeholder="YYYY-MM-DD" name="date_reviewed">
								</div>
							</div>
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
                      <!-- finish form row -->



                    </div>
                    <footer class="panel-footer">
                        <button type="submit" class="btn btn-success">Submit </button>
                        <button type="reset" class="btn btn-default">Reset</button>
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
