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
	header("Location: login.php"); // send them to the Login page.
}

$page_id = 17;

// pull the header and template stuff:
pagehead($page_id);

$record_id = 0;

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
<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Add A New Part Revision<?php if ($record_id != 0) { ?> to Part # <? echo $part_code . " - " . $part_name_EN . " / " . $part_name_CN; } ?></h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
									<li>
										<a href="part_revisions.php">All Part Revisions</a>
									</li>
								<li><span>Add New Part Revision Record</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					
					<div class="row">
						<div class="col-md-12">
						
						<!-- START THE FORM! -->
						<form class="form-horizontal form-bordered" action="part_revision_add_do.php" method="post">
						
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Add Part Revision Record Details:</h2>
								</header>
								<div class="panel-body">
								
								<div class="form-group">
												<label class="col-md-3 control-label">Part #:</label>
												<div class="col-md-5">
													<select data-plugin-selectTwo class="form-control populate" name="part_ID">
													<?php 
													// get parts list
													$get_parts_list_SQL = "SELECT * FROM `parts` ORDER BY `part_code` ASC";
													// echo $get_parts_SQL;
														
													$result_get_parts_list = mysqli_query($con,$get_parts_list_SQL);
													// while loop
													while($row_get_parts_list = mysqli_fetch_array($result_get_parts_list)) {
													
														$list_part_id = $row_get_parts_list['ID'];
																		  
														// GET PART TYPE:
																		  	
														$get_parts_list_type_SQL = "SELECT * FROM  `part_type` WHERE  `ID` ='" . $row_get_parts_list['type_ID'] . "'";
														// echo $get_part_type_SQL;
	
														$result_get_parts_list_type = mysqli_query($con,$get_parts_list_type_SQL);
														// while loop
														while($row_get_parts_list_type = mysqli_fetch_array($result_get_parts_list_type)) {
															$parts_list_type_EN = $row_get_parts_list_type['name_EN'];
															$parts_list_type_CN = $row_get_parts_list_type['name_CN'];
														}
					  	
														// GET PART CLASSIFICATION:
					  	
														$get_parts_list_class_SQL = "SELECT * FROM  `part_classification` WHERE  `ID` ='" . $row_get_parts_list['classification_ID'] . "'";
														// echo $get_part_class_SQL;
		
														$result_get_parts_list_class = mysqli_query($con,$get_parts_list_class_SQL);
														// while loop
														while($row_get_parts_list_class = mysqli_fetch_array($result_get_part_class)) {
															$parts_list_class_EN = $row_get_parts_list_class['name_EN'];
															$parts_list_class_CN = $row_get_parts_list_class['name_CN'];
														}
													?>
													
													<option value="<?php echo $list_part_id; ?>"<?php if ($record_id == $list_part_id) { ?> selected=""<?php } ?>><?php echo $row_get_parts_list['part_code']; ?> - <?php echo $row_get_parts_list['name_EN']; ?> / <?php echo $row_get_parts_list['name_CN']; ?></option>
													
													<?php
													} // END WHILE LOOP
													
													?>
													</select>
												</div>
												<div class="col-md-1">
													<a href="part_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>
											</div>
											
											
								<div class="form-group">
									<label class="col-md-3 control-label">Revision #:</label>
									<div class="col-md-5">
										<input type="text" class="form-control" id="inputDefault" placeholder="A1" name="rev_number" />
									</div>
									
									<div class="col-md-1">
										&nbsp;
									</div>
								</div>	
											
											<div class="form-group">
												<label class="col-md-3 control-label">Remarks:</label>
												<div class="col-md-5">
													<textarea class="form-control" rows="3" id="textareaDefault" name="remarks"></textarea>
												</div>
												
									
												<div class="col-md-1">
													&nbsp;
												</div>
											</div>
								
								<div class="form-group">
												<label class="col-md-3 control-label">User:</label>
												<div class="col-md-5">
													<select data-plugin-selectTwo class="form-control populate" name="user_ID">
													<?php 
													// get batch list
													$get_user_list_SQL = "SELECT * FROM `users`";
													$result_get_user_list = mysqli_query($con,$get_user_list_SQL);
													// while loop
													while($row_get_user_list = mysqli_fetch_array($result_get_user_list)) {
	
														// now print each record:  
														$user_id = $row_get_user_list['ID']; 
														$user_first_name = $row_get_user_list['first_name'];
														$user_last_name = $row_get_user_list['last_name'];
														$user_name_CN = $row_get_user_list['name_CN'];
													?>
														<option value="<?php echo $user_id; ?>"><?php echo $user_first_name . " " . $user_last_name; if (($user_name_CN != '') && ($user_name_CN != '中文名')) { echo $user_name_CN; }?></option>
														
														<?php 
														}
														?>
													</select>
												</div>
												
												<div class="col-md-1">
													<a href="user_add.php" class="mb-xs mt-xs mr-xs btn btn-success pull-right"><i class="fa fa-plus-square"></i></a>
												</div>
												
											</div>
											
											
											<div class="form-group">
												<label class="col-md-3 control-label">Date:</label>
												<div class="col-md-5">
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input type="text" data-plugin-datepicker class="form-control" placeholder="YYYY-MM-DD" name="date_added">
													</div>
												</div>
												
									
												<div class="col-md-1">
													&nbsp;
												</div>
											</div>
											
											
					 
								</div>
								
								
								<footer class="panel-footer">
										<?php 
										if (isset($_REQUEST['PO_ID'])) {
											?>
											<input type="hidden" value="<?php echo $_REQUEST['PO_ID']; ?>" name="PO_ID" />
											<?php
										}
										?>
										<button type="submit" class="btn btn-success">Submit </button>
										<button type="reset" class="btn btn-default">Reset</button>
									</footer>
							</section>
										<!-- now close the form -->
										</form>
						
						
						</div>
						
						</div>
						
						
					
					
								<!-- now close the panel --><!-- end row! -->
					 
					<!-- end: page -->
				</section>
				
<!-- : END MAIN PAGE BODY -->

<?php 
// now close the page out:
pagefoot($page_id);

?>