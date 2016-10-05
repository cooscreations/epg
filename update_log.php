<?php
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
/*//////*/      session_start();        /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//  now check the user is OK to view this page  //
/*//////*/ require ('page_access.php'); /*//////*/
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


$page_id = 8;

// pull the header and template stuff:
pagehead($page_id); 


// PAGINATION

$table = 'update_log';
$filter = "";
$total_rows = 0;

// This first query is just to get the total count of rows
	
$count_rows_SQL = "SELECT COUNT(ID) FROM `" . $table . "`";
// echo "<h1>SQL: " . $count_rows_SQL . "</h1>";
$count_rows_query = mysqli_query($con, $count_rows_SQL);
$count_rows_row = mysqli_fetch_row($count_rows_query);
// Here we have the total row count
$total_rows = $count_rows_row[0];

// debug:
// echo "<h1>Total Rows: ".$total_rows."</h1>";

$rpp = 50;
// This tells us the page number of our last page
$last = ceil($total_rows/$rpp);

// debug:
// echo "<h2>Last: ".$last." (total rows / rrp (".$rpp."))</h2>";

// This makes sure $last cannot be less than 1
if($last < 1){
	$last = 1;
}
?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Update Log</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Updates</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->

                    <div class="inner-toolbar clearfix">
						<ul>
							<li>
								<button type="button" class="btn btn-primary" onClick="document.location = this.value" value="update_log.php"><i class="fa fa-eye m-none"></i> Show All</button>
							</li>
							<li class="right">
								<ul class="nav nav-pills nav-pills-primary">
									<li>
										<label>Type</label>
									</li>
									<li class="active">
										<a href="#general" data-toggle="tab">General</a>
									</li>
									<li>
										<a href="#auth" data-toggle="tab">Auth. Lists</a>
									</li>
									<li>
										<a href="#dev" data-toggle="tab">Developers</a>
									</li>
									<li>
										<a href="#system" data-toggle="tab">System</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>

					<section class="panel">
						<div class="panel-body tab-content">


                        	<!-- START TAB -->
							<div id="general" class="tab-pane active">
								<table class="table table-striped table-no-more table-bordered  mb-none">
									<thead>
										<tr class="dark">
											<th style="width: 10%"><span class="text-normal text-sm">Type</span></th>
											<th style="width: 10%"><span class="text-normal text-sm">Action</span></th>
											<th style="width: 15%"><span class="text-normal text-sm">Date</span></th>
											<th><span class="text-normal text-sm">Message</span></th>
										</tr>
									</thead>
									<tbody class="log-viewer">
                                    <?php

                                    $where_SQL = '';

                                    if (isset($_REQUEST['user_id'])) {
                                    	$where_SQL= " AND `user_ID` = " . $_REQUEST['user_id'];
                                    }
                                    else if (isset($_REQUEST['table_name'])) {
                                    	$where_SQL= " AND `table_name` = " . $_REQUEST['table_name'];
                                    }
                                    
									
									// *********************  PAGINATION  *******************
									// now check for pagination
									
									if (isset($_REQUEST['page'])) {
										
										if ($_REQUEST['page']=='all') {
											// show ALL RESULTS IN ONE PAGE...
											// no need to update the SQL query...
										}
										else {
										
											// page 1 (default) = 0, 30
											// page 2 (2) = 30, 30
											// page 3 (3) = 60, 30 ------  sooooo... it's page number - 1 * rpp :)
											
											$limit_val = (($_REQUEST['page'] - 1)*$rpp);
											
											$add_SQL .= " LIMIT ".$limit_val.", ".$rpp;	
											
											$show_from_to = "".$limit_val." - ".($limit_val+$rpp)."";
											
										} // end else show limited results....
									}
									else {
									
										// By default, let's show the first 30 rows (see $rpp above):
										$add_SQL = $add_SQL." LIMIT 0, ".$rpp;	
											
										$show_from_to = "0 - ".$rpp."";
									
									}
									// ____________________ END PAGINATION ___________________________________________

                                    // get general updates:
									$get_general_updates_SQL = "SELECT * FROM `update_log` WHERE `update_type` = 'general'" . $where_SQL . " ORDER BY `update_date` DESC" . $add_SQL;
									// DEBUG
									// echo '<h4>SQL: ' . $get_general_updates_SQL . '</h4>';

									$update_count = 0;

									$result_get_general_updates = mysqli_query($con,$get_general_updates_SQL);
									// while loop
									while($row_get_general_updates = mysqli_fetch_array($result_get_general_updates)) {

											// now print the result:
											$general_update_ID = $row_get_general_updates['ID'];
											$general_update_table_name = $row_get_general_updates['table_name'];
											$general_update_update_ID = $row_get_general_updates['update_ID'];
											$general_update_user_ID = $row_get_general_updates['user_ID'];
											$general_update_notes = $row_get_general_updates['notes'];
											$general_update_update_date = $row_get_general_updates['update_date'];
											$general_update_update_type = $row_get_general_updates['update_type'];
											$general_update_action = $row_get_general_updates['update_action'];

											$update_count = $update_count + 1;

									?>
										<tr>
											<td data-title="Type" class="pt-md pb-md">
												<i class="fa fa-<?php
													if ($general_update_update_date == 'general') { ?>table<?php }
													else if ($general_update_update_date == 'car_sys') { ?>taxi<?php }
													?> fa-fw text-muted text-md va-middle"></i>
												<span class="va-middle"><?php echo $general_update_update_type; ?></span>
											</td>
											<td data-title="Action" class="pt-md pb-md">
                                            	<?php
													if ($general_update_action == 'INSERT') {
														?>
														<i class="fa fa-plus-square fa-fw text-success text-md va-middle"></i>
														<?php
													}
													else if ($general_update_action == 'UPDATE') {
														?>
														<i class="fa fa-pencil-square-o fa-fw text-warning text-md va-middle"></i>
														<?php
													}
													else if ($general_update_action == 'DELETE') {
														?>
														<i class="fa fa-times fa-fw text-danger text-md va-middle"></i>
														<?php
													}
													else {
														// ???
														?>
														<i class="fa fa-question-circle fa-fw text-muted text-md va-middle"></i>
														<?php
													}
												?>
												<span class="va-middle"><?php echo $general_update_action; ?></span>
											</td>
											<td data-title="Date" class="pt-md pb-md">
												<?php echo $general_update_update_date; ?>
											</td>
											<td data-title="Message" class="pt-md pb-md">
												<?php
												// show the creator name and link:
												get_creator($general_update_user_ID);

												if ($general_update_action == 'UPDATE') {
													?> updated table <?php
												}
												else if ($general_update_action == 'INSERT') {
													?> added a record to table <?php
												}
												else if ($general_update_action == 'DELETE') {
													?> deleted a record from table <?php
												}
												?>'<?php echo $general_update_table_name; ?>', record #<?php echo $general_update_update_ID; ?>.
                                                <br />
                                                <strong>NOTE:</strong> <em>"<?php echo $general_update_notes; ?>"</em>
											</td>
										</tr>
                                   <?
									} // end while loop...
								   ?>
									</tbody>
									<tfoot>
										<tr>
										  <th colspan="4" class="text-left">
										  	TOTAL RECORDS: <?php echo $update_count; ?>
										  </th>
										</tr>
										
										<tr>
										  <td colspan="4" class="text-center">
										  <?php 
											if ($total_rows>$rpp) {
												// run function in page_functions.php:
												$URL = "update_log"; // updates
												// $ last is specified above
												if (isset($_REQUEST['sort'])) {
													$sort = $_REQUEST['sort'];
												}
												else { $sort = ''; }
												
												if (isset($_REQUEST['page'])) {
													$show_page = $_REQUEST['page']; 
												}
												else { 
													$show_page = '1';
												}
											}
											// now call the function!
											pagination($URL, $last, $sort, $show_page, $filter);
											?>
										  </td>
										</tr>
									</tfoot>
								</table>
							</div>
                            <!-- END TAB -->


                        	<!-- START TAB -->
							<div id="auth" class="tab-pane">
								<table class="table table-striped table-no-more table-bordered  mb-none">
									<thead>
										<tr class="dark">
											<th style="width: 10%"><span class="text-normal text-sm">Type</span></th>
											<th style="width: 10%"><span class="text-normal text-sm">Action</span></th>
											<th style="width: 15%"><span class="text-normal text-sm">Date</span></th>
											<th><span class="text-normal text-sm">Message</span></th>
										</tr>
									</thead>
									<tbody class="log-viewer">
                                    <?php
                                    // get auth updates:
									$get_auth_updates_SQL = "SELECT * FROM `update_log` WHERE `update_type` = 'auth' ORDER BY `update_date` DESC";
									$result_get_auth_updates = mysqli_query($con,$get_auth_updates_SQL);
									// while loop
									while($row_get_auth_updates = mysqli_fetch_array($result_get_auth_updates)) {

											// now print the result:
											$auth_update_ID = $row_get_auth_updates['ID'];
											$auth_update_table_name = $row_get_auth_updates['table_name'];
											$auth_update_update_ID = $row_get_auth_updates['update_ID'];
											$auth_update_user_ID = $row_get_auth_updates['user_ID'];
											$auth_update_notes = $row_get_auth_updates['notes'];
											$auth_update_update_date = $row_get_auth_updates['update_date'];
											$auth_update_update_type = $row_get_auth_updates['update_type'];
											$auth_update_action = $row_get_auth_updates['update_action'];


									?>
										<tr>
											<td data-title="Type" class="pt-md pb-md">
												<i class="fa fa-key fa-fw text-muted text-md va-middle"></i>
												<span class="va-middle"><?php echo $auth_update_update_type; ?></span>
											</td>
											<td data-title="Action" class="pt-md pb-md">
                                            	<?php
													if ($auth_update_action == 'INSERT') {
														?>
														<i class="fa fa-plus-square fa-fw text-success text-md va-middle"></i>
														<?php
													}
													else if ($auth_update_action == 'UPDATE') {
														?>
														<i class="fa fa-pencil-square-o fa-fw text-warning text-md va-middle"></i>
														<?php
													}
													else if ($auth_update_action == 'DELETE') {
														?>
														<i class="fa fa-times fa-fw text-danger text-md va-middle"></i>
														<?php
													}
													else {
														// ???
														?>
														<i class="fa fa-question-circle fa-fw text-muted text-md va-middle"></i>
														<?php
													}
												?>
												<span class="va-middle"><?php echo $auth_update_action; ?></span>
											</td>
											<td data-title="Date" class="pt-md pb-md">
												<?php echo $auth_update_update_date; ?>
											</td>
											<td data-title="Message" class="pt-md pb-md">
												<?php
												if (($auth_update_user_ID!='') && ($auth_update_user_ID!=0)) {
													get_creator($auth_update_user_ID);
												}
												else { ?><em>(No User)</em><?php }

												if ($auth_update_action == 'UPDATE') {
													?> updated table <?php
												}
												else if ($auth_update_action == 'INSERT') {
													?> added a record to table <?php
												}
												else if ($auth_update_action == 'DELETE') {
													?> deleted a record from table <?php
												}
												?>'<?php echo $auth_update_table_name; ?>', record #<?php echo $auth_update_update_ID; ?>.
                                                <br />
                                                <strong>NOTE:</strong> <em>"<?php echo $auth_update_notes; ?>"</em>
											</td>
										</tr>
                                   <?
									} // end while loop...
								   ?>
									</tbody>
								</table>
							</div>
                            <!-- END TAB -->




                        	<!-- START TAB -->
							<div id="dev" class="tab-pane">
								<table class="table table-striped table-no-more table-bordered  mb-none">
									<thead>
										<tr class="dark">
											<th style="width: 10%"><span class="text-normal text-sm">Type</span></th>
											<th style="width: 10%"><span class="text-normal text-sm">Action</span></th>
											<th style="width: 15%"><span class="text-normal text-sm">Date</span></th>
											<th><span class="text-normal text-sm">Message</span></th>
										</tr>
									</thead>
									<tbody class="log-viewer">
                                    <?php
                                    // get dev updates:
									$get_dev_updates_SQL = "SELECT * FROM `update_log` WHERE `update_type` = 'dev' ORDER BY `update_date` DESC";
									$result_get_dev_updates = mysqli_query($con,$get_dev_updates_SQL);
									// while loop
									while($row_get_dev_updates = mysqli_fetch_array($result_get_dev_updates)) {

											// now print the result:
											$dev_update_ID = $row_get_dev_updates['ID'];
											$dev_update_table_name = $row_get_dev_updates['table_name'];
											$dev_update_update_ID = $row_get_dev_updates['update_ID'];
											$dev_update_user_ID = $row_get_dev_updates['user_ID'];
											$dev_update_notes = $row_get_dev_updates['notes'];
											$dev_update_update_date = $row_get_dev_updates['update_date'];
											$dev_update_update_type = $row_get_dev_updates['update_type'];
											$dev_update_action = $row_get_dev_updates['update_action'];


									?>
										<tr>
											<td data-title="Type" class="pt-md pb-md">
												<i class="fa fa-cog fa-fw text-muted text-md va-middle"></i>
												<span class="va-middle"><?php echo $dev_update_update_type; ?></span>
											</td>
											<td data-title="Action" class="pt-md pb-md">
                                            	<?php
													if ($dev_update_action == 'INSERT') {
														?>
														<i class="fa fa-plus-square fa-fw text-success text-md va-middle"></i>
														<?php
													}
													else if ($dev_update_action == 'UPDATE') {
														?>
														<i class="fa fa-pencil-square-o fa-fw text-warning text-md va-middle"></i>
														<?php
													}
													else if ($dev_update_action == 'DELETE') {
														?>
														<i class="fa fa-times fa-fw text-danger text-md va-middle"></i>
														<?php
													}
													else {
														// ???
														?>
														<i class="fa fa-question-circle fa-fw text-muted text-md va-middle"></i>
														<?php
													}
												?>
												<span class="va-middle"><?php echo $dev_update_action; ?></span>
											</td>
											<td data-title="Date" class="pt-md pb-md">
												<?php echo $dev_update_update_date; ?>
											</td>
											<td data-title="Message" class="pt-md pb-md">
												<?php
												if (($dev_update_user_ID!='') && ($dev_update_user_ID!=0)) {
													get_creator($dev_update_user_ID);
												}
												else { ?><em>(No User)</em><?php }

												if ($dev_update_action == 'UPDATE') {
													?> updated table <?php
												}
												else if ($dev_update_action == 'INSERT') {
													?> added a record to table <?php
												}
												else if ($dev_update_action == 'DELETE') {
													?> deleted a record from table <?php
												}
												?>'<?php echo $dev_update_table_name; ?>', record #<?php echo $dev_update_update_ID; ?>.
                                                <br />
                                                <strong>NOTE:</strong> <em>"<?php echo $dev_update_notes; ?>"</em>
											</td>
										</tr>
                                   <?
									} // end while loop...
								   ?>
									</tbody>
								</table>
							</div>
                            <!-- END TAB -->




                        	<!-- START TAB -->
							<div id="system" class="tab-pane">
								<table class="table table-striped table-no-more table-bordered  mb-none">
									<thead>
										<tr class="dark">
											<th style="width: 10%"><span class="text-normal text-sm">Type</span></th>
											<th style="width: 10%"><span class="text-normal text-sm">Action</span></th>
											<th style="width: 15%"><span class="text-normal text-sm">Date</span></th>
											<th><span class="text-normal text-sm">Message</span></th>
										</tr>
									</thead>
									<tbody class="log-viewer">
                                    <?php
                                    // get dev updates:
									$get_sys_updates_SQL = "SELECT * FROM `update_log` WHERE `update_type` = 'system' ORDER BY `update_date` DESC";
									$result_get_sys_updates = mysqli_query($con,$get_sys_updates_SQL);
									// while loop
									while($row_get_sys_updates = mysqli_fetch_array($result_get_sys_updates)) {

											// now print the result:
											$sys_update_ID = $row_get_sys_updates['ID'];
											$sys_update_table_name = $row_get_sys_updates['table_name'];
											$sys_update_update_ID = $row_get_sys_updates['update_ID'];
											$sys_update_user_ID = $row_get_sys_updates['user_ID'];
											$sys_update_notes = $row_get_sys_updates['notes'];
											$sys_update_update_date = $row_get_sys_updates['update_date'];
											$sys_update_update_type = $row_get_sys_updates['update_type'];
											$sys_update_action = $row_get_sys_updates['update_action'];


									?>
										<tr>
											<td data-title="Type" class="pt-md pb-md">
												<i class="fa fa-database fa-fw text-muted text-md va-middle"></i>
												<span class="va-middle"><?php echo $sys_update_update_type; ?></span>
											</td>
											<td data-title="Action" class="pt-md pb-md">
                                            	<?php
													if ($sys_update_action == 'INSERT') {
														?>
														<i class="fa fa-plus-square fa-fw text-success text-md va-middle"></i>
														<?php
													}
													else if ($sys_update_action == 'UPDATE') {
														?>
														<i class="fa fa-pencil-square-o fa-fw text-warning text-md va-middle"></i>
														<?php
													}
													else if ($sys_update_action == 'DELETE') {
														?>
														<i class="fa fa-times fa-fw text-danger text-md va-middle"></i>
														<?php
													}
													else if ($sys_update_action == 'BACKUP') {
														?>
														<i class="fa fa-database fa-fw text-primary text-md va-middle"></i>
														<?php
													}
													else {
														// ???
														?>
														<i class="fa fa-question-circle fa-fw text-muted text-md va-middle"></i>
														<?php
													}
												?>
												<span class="va-middle"><?php echo $sys_update_action; ?></span>
											</td>
											<td data-title="Date" class="pt-md pb-md">
												<?php echo $sys_update_update_date; ?>
											</td>
											<td data-title="Message" class="pt-md pb-md">
												<?php

												if ($sys_update_action == 'BACKUP') {

													$pre_text = substr($sys_update_notes,0,50);
													$link_text = substr($sys_update_notes,50,46);
													$post_text = '.';

													// now build the link - this is messy!

													echo $pre_text;
													echo '<a href="'.$link_text.'" target="_blank" title="RIGHT CLICK > SAVE AS">' . $link_text . '</a>';
													echo $post_text;

												}
												else {
													echo $sys_update_notes;
												}
												?>
											</td>
										</tr>
                                   <?
									} // end while loop...
								   ?>
									</tbody>
								</table>
							</div>
                            <!-- END TAB -->



						</div>
					</section>
					<!-- end: page -->

				</section>

<?php pagefooter($page_id, $record_id); ?>
