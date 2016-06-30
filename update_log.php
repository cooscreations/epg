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


$page_id = 8;

// pull the header and template stuff:
pagehead($page_id); ?>



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
                                    
                                    // get general updates:
									$get_general_updates_SQL = "SELECT * FROM `update_log` WHERE `update_type` = 'general'" . $where_SQL . " ORDER BY `update_date` DESC";
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
												if (($general_update_user_ID!='') && ($general_update_user_ID!=0)) {
													// now get the user info:
													$get_user_SQL = "SELECT * FROM `users` WHERE `ID` = " . $general_update_user_ID;

													$result_get_user = mysqli_query($con,$get_user_SQL);

													// while loop
													while($row_get_user = mysqli_fetch_array($result_get_user)) {
														$user_ID = $row_get_user['ID'];
														$user_fn = $row_get_user['first_name'];
														$user_mn = $row_get_user['middle_name'];
														$user_ln = $row_get_user['last_name'];
														$user_name_cn = $row_get_user['name_CN'];
														$user_email = $row_get_user['email'];
														//$user_pwd = _base64_decrypt($row_get_user['password']); May not need this. Why woud we display the password plain text ?
														$user_level = $row_get_user['user_level'];
														$user_position = $row_get_user['position'];
														$user_last_login_date = $row_get_user['last_login_date'];
														$user_facebook = $row_get_user['facebook_profile'];	
														$user_linkedin = $row_get_user['linkedin_profile'];	
														$user_twitter = $row_get_user['twitter_profile'];	
														$user_wechat = $row_get_user['wechat_profile'];	
														$user_skype = $row_get_user['skype_profile'];	
	
													} // end get user info WHILE loop
													
													?>
													<a href="user_view.php?id=<?php echo $general_update_user_ID; ?>" title="Click to view this user profile">
													<?php
													echo  $user_fn . "  " . $user_ln;
													
													if (($user_name_cn!='')&&($user_name_cn!='中文名')) { 
														echo " / " . $user_name_cn;
													}
													?>
													</a>
													<?php
													
												}
												else { ?><em>(No User)</em><?php }
												
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