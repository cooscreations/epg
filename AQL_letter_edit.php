<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* //////// require ('page_access.php'); */// ///*/
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
	header("Location: AQL_letters.php?msg=NG&action=view&error=no_id");
	exit();
}

if ($record_id != 0) {
	// now get the record info:
    $get_AQL_letter_SQL = "SELECT * FROM `AQL_letter` WHERE `ID` = '" . $record_id . "'";
    // echo $get_AQL_letter_SQL;

    $result_get_AQL_letter = mysqli_query($con,$get_AQL_letter_SQL);

    // while loop
    while($row_get_AQL_letter = mysqli_fetch_array($result_get_AQL_letter)) {
        $AQL_letter_ID 					= $row_get_AQL_letter['ID'];	// same as record_id
		$AQL_letter_AQL_code 			= $row_get_AQL_letter['AQL_code'];
		$AQL_letter_order_qty_min 		= $row_get_AQL_letter['order_qty_min'];
		$AQL_letter_order_qty_max 		= $row_get_AQL_letter['order_qty_max'];
		$AQL_letter_AQL_letter_result 	= $row_get_AQL_letter['AQL_letter_result'];
		$AQL_letter_status 				= $row_get_AQL_letter['status'];

    } // end get info WHILE loop
}

// pull the header and template stuff:
pagehead ();

?>

    <!-- start: page -->
 
	<div class="row">
		<div class="col-md-12">

			<!-- START THE FORM! -->
			 <form class="form-horizontal form-bordered" action="AQL_letter_edit_do.php" method="post">

                <section class="panel">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                            <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                        </div>

                        <h2 class="panel-title">Edit AQL Letter Details:</h2>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
							<label class="col-md-3 control-label">AQL Code:</label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="AQL_code" required>

											<option value="S-1" <?php if ($AQL_letter_AQL_code == 'S-1') { ?> selected="selected"<?php } ?>>
												S-1
											</option>

											<option value="S-2" <?php if ($AQL_letter_AQL_code == 'S-2') { ?> selected="selected"<?php } ?>>
												S-2
											</option>

											<option value="S-3" <?php if ($AQL_letter_AQL_code == 'S-3') { ?> selected="selected"<?php } ?>>
												S-3
											</option>

											<option value="S-4" <?php if ($AQL_letter_AQL_code == 'S-4') { ?> selected="selected"<?php } ?>>
												S-4
											</option>

											<option value="I" <?php if ($AQL_letter_AQL_code == 'I') { ?> selected="selected"<?php } ?>>
												I
											</option>

											<option value="II" <?php if ($AQL_letter_AQL_code == 'II') { ?> selected="selected"<?php } ?>>
												II
											</option>

											<option value="III" <?php if ($AQL_letter_AQL_code == 'III') { ?> selected="selected"<?php } ?>>
												III
											</option>
								</select>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
						
						
                        <div class="form-group">
							<label class="col-md-3 control-label">Lot / Batch Size:</label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="order_qty_min" required>

											<option value="2" <?php if ($AQL_letter_order_qty_min == '2') { ?> selected="selected"<?php } ?>>
												2 - 8
											</option>

											<option value="9" <?php if ($AQL_letter_order_qty_min == '9') { ?> selected="selected"<?php } ?>>
												9 - 15
											</option>

											<option value="16" <?php if ($AQL_letter_order_qty_min == '16') { ?> selected="selected"<?php } ?>>
												16 - 25
											</option>

											<option value="26" <?php if ($AQL_letter_order_qty_min == '26') { ?> selected="selected"<?php } ?>>
												26 - 50
											</option>

											<option value="51" <?php if ($AQL_letter_order_qty_min == '51') { ?> selected="selected"<?php } ?>>
												51 - 90
											</option>

											<option value="91" <?php if ($AQL_letter_order_qty_min == '91') { ?> selected="selected"<?php } ?>>
												91 - 150
											</option>

											<option value="151" <?php if ($AQL_letter_order_qty_min == '151') { ?> selected="selected"<?php } ?>>
												151 - 280
											</option>

											<option value="281" <?php if ($AQL_letter_order_qty_min == '281') { ?> selected="selected"<?php } ?>>
												281 - 500
											</option>

											<option value="501" <?php if ($AQL_letter_order_qty_min == '501') { ?> selected="selected"<?php } ?>>
												501 - 1,200
											</option>

											<option value="1201" <?php if ($AQL_letter_order_qty_min == '1201') { ?> selected="selected"<?php } ?>>
												1,201 - 3,200
											</option>

											<option value="3201" <?php if ($AQL_letter_order_qty_min == '3201') { ?> selected="selected"<?php } ?>>
												3,201 - 10,000
											</option>

											<option value="10001" <?php if ($AQL_letter_order_qty_min == '10001') { ?> selected="selected"<?php } ?>>
												10,001 - 35,000
											</option>

											<option value="35001" <?php if ($AQL_letter_order_qty_min == '35001') { ?> selected="selected"<?php } ?>>
												35,001 - 150,000
											</option>

											<option value="150001" <?php if ($AQL_letter_order_qty_min == '150001') { ?> selected="selected"<?php } ?>>
												150,001 - 500,000
											</option>

											<option value="500001" <?php if ($AQL_letter_order_qty_min == '500001') { ?> selected="selected"<?php } ?>>
												500,001+
											</option>
								</select>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
						
						
                        <div class="form-group">
							<label class="col-md-3 control-label">Result:</label>
							<div class="col-md-5">
								<select data-plugin-selectTwo class="form-control populate" name="AQL_letter_result" required>

									<option value="A" <?php if ($AQL_letter_AQL_letter_result == 'A') { ?> selected="selected"<?php } ?>>
										A
									</option>

									<option value="B" <?php if ($AQL_letter_AQL_letter_result == 'B') { ?> selected="selected"<?php } ?>>
										B
									</option>

									<option value="C" <?php if ($AQL_letter_AQL_letter_result == 'C') { ?> selected="selected"<?php } ?>>
										C
									</option>

									<option value="D" <?php if ($AQL_letter_AQL_letter_result == 'D') { ?> selected="selected"<?php } ?>>
										D
									</option>

									<option value="E" <?php if ($AQL_letter_AQL_letter_result == 'E') { ?> selected="selected"<?php } ?>>
										E
									</option>

									<option value="F" <?php if ($AQL_letter_AQL_letter_result == 'F') { ?> selected="selected"<?php } ?>>
										F
									</option>

									<option value="G" <?php if ($AQL_letter_AQL_letter_result == 'G') { ?> selected="selected"<?php } ?>>
										G
									</option>

									<option value="H" <?php if ($AQL_letter_AQL_letter_result == 'H') { ?> selected="selected"<?php } ?>>
										H
									</option>

									<option value="I" <?php if ($AQL_letter_AQL_letter_result == 'I') { ?> selected="selected"<?php } ?>>
										I
									</option>

									<option value="J" <?php if ($AQL_letter_AQL_letter_result == 'J') { ?> selected="selected"<?php } ?>>
										J
									</option>

									<option value="K" <?php if ($AQL_letter_AQL_letter_result == 'K') { ?> selected="selected"<?php } ?>>
										K
									</option>

									<option value="L" <?php if ($AQL_letter_AQL_letter_result == 'L') { ?> selected="selected"<?php } ?>>
										L
									</option>

									<option value="M" <?php if ($AQL_letter_AQL_letter_result == 'M') { ?> selected="selected"<?php } ?>>
										M
									</option>

									<option value="N" <?php if ($AQL_letter_AQL_letter_result == 'N') { ?> selected="selected"<?php } ?>>
										N
									</option>

									<option value="O" <?php if ($AQL_letter_AQL_letter_result == 'O') { ?> selected="selected"<?php } ?>>
										O
									</option>

									<option value="P" <?php if ($AQL_letter_AQL_letter_result == 'P') { ?> selected="selected"<?php } ?>>
										P
									</option>

									<option value="Q" <?php if ($AQL_letter_AQL_letter_result == 'Q') { ?> selected="selected"<?php } ?>>
										Q
									</option>

									<option value="R" <?php if ($AQL_letter_AQL_letter_result == 'R') { ?> selected="selected"<?php } ?>>
										R
									</option>

									<option value="S" <?php if ($AQL_letter_AQL_letter_result == 'S') { ?> selected="selected"<?php } ?>>
										S
									</option>

									<option value="T" <?php if ($AQL_letter_AQL_letter_result == 'T') { ?> selected="selected"<?php } ?>>
										T
									</option>

									<option value="U" <?php if ($AQL_letter_AQL_letter_result == 'U') { ?> selected="selected"<?php } ?>>
										U
									</option>

									<option value="V" <?php if ($AQL_letter_AQL_letter_result == 'V') { ?> selected="selected"<?php } ?>>
										V
									</option>

									<option value="W" <?php if ($AQL_letter_AQL_letter_result == 'W') { ?> selected="selected"<?php } ?>>
										W
									</option>

									<option value="X" <?php if ($AQL_letter_AQL_letter_result == 'X') { ?> selected="selected"<?php } ?>>
										X
									</option>

									<option value="Y" <?php if ($AQL_letter_AQL_letter_result == 'Y') { ?> selected="selected"<?php } ?>>
										Y
									</option>

									<option value="Z" <?php if ($AQL_letter_AQL_letter_result == 'Z') { ?> selected="selected"<?php } ?>>
										Z
									</option>
								</select>
							</div>

							<div class="col-md-1">
								&nbsp;
							</div>
						</div>
			
                        <div class="form-group">
							<label class="col-md-3 control-label">Record Status:</label>
							<div class="col-md-5">
								<?php echo record_status_drop_down($AQL_letter_status); ?>
							</div>
							
							<div class="col-md-1">
								&nbsp;
							</div>
						</div>



                    </div>
                    <footer class="panel-footer">
                        <?php form_buttons('AQL_letters', $record_id); ?>
                        
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
