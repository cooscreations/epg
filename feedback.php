<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

$sort = '';
if (isset($_REQUEST['sort'])){ $sort = $_REQUEST['sort']; }

$sort_dir = 'ASC';
if (isset($_REQUEST['sort_dir'])){ $sort_dir = $_REQUEST['sort_dir']; }

// pull the header and template stuff:
pagehead();
// default: show open bugs and in-progress bugs.

$add_SQL_0 = "";

if (isset($_REQUEST['user_id'])) {
	$user_id = $_REQUEST['user_id'];
	$add_SQL_0 = " AND `created_by` = '" . $user_id . "'";
}
else {
	$user_id = '';
}

// get the UNREAD bug count:
$count_new_bugs_SQL = "SELECT COUNT(DISTINCT `referrer_URL`) FROM `bug_report` WHERE `status` = '1'".$add_SQL_0.""; // counting PENDING / NEW (1) BUGS
$count_new_bugs_query = mysqli_query($con, $count_new_bugs_SQL);
$count_new_bugs_row = mysqli_fetch_row($count_new_bugs_query);
// Here we have the total row count
$total_new_bugs = $count_new_bugs_row[0];

// get the ALL BUGS COUNT:
$count_all_bugs_SQL = "SELECT COUNT(DISTINCT `referrer_URL`) FROM  `bug_report` WHERE 1".$add_SQL_0.""; // counting ALL BUGS
$count_all_bugs_query = mysqli_query($con, $count_all_bugs_SQL);
$count_all_bugs_row = mysqli_fetch_row($count_all_bugs_query);
// Here we have the total row count
$total_all_bugs = $count_all_bugs_row[0];

// get the CLOSED BUGS:
$count_closed_bugs_SQL = "SELECT COUNT(DISTINCT `referrer_URL`) FROM  `bug_report` WHERE `status` = '0'".$add_SQL_0.""; // counting CLOSED (status 0) BUGS
$count_closed_bugs_query = mysqli_query($con, $count_closed_bugs_SQL);
$count_closed_bugs_row = mysqli_fetch_row($count_closed_bugs_query);
// Here we have the total row count
$total_closed_bugs = $count_closed_bugs_row[0];

// get the OPEN BUGS:
$count_open_bugs_SQL = "SELECT COUNT(DISTINCT `referrer_URL`) FROM  `bug_report` WHERE `status` = '2'".$add_SQL_0.""; // counting OPEN (2) BUGS
$count_open_bugs_query = mysqli_query($con, $count_open_bugs_SQL);
$count_open_bugs_row = mysqli_fetch_row($count_open_bugs_query);
// Here we have the total row count
$total_open_bugs = $count_open_bugs_row[0];

?>
					<!-- start: page -->

                    <?php

					if ($user_id != '') {
						?>

						<h2>Showing Bugs Created by <?php echo get_creator($user_id); ?> <small>(Unique pages: <?php echo $total_all_bugs; ?>)</small></h2>

						<?
					}

					?>

                    <section class="panel panel-danger">
                            <header class="panel-heading">
                                <div class="panel-actions">
                                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                                </div>

                                <span class="pull-left label label-<?php if ($total_new_bugs != 0) { ?>warning<? } else { ?>success<?php } ?>"><?php echo $total_new_bugs; ?></span>

                                <h2 class="panel-title"><i class="fa fi-skull fa-2x"></i> New Bugs</h2>
                                <p class="panel-subtitle">
                                  These bugs were recently added and we will get around to fixing them soon.
                                </p>
                            </header>
                            <div class="panel-body">
                        <table class="table table-bordered table-striped table-condensed mb-none">
                          <thead>
                              <tr class="dark">
                                <th scope="col">Type</th>
                                <th scope="col">Title</th>
                                <th scope="col">Creator</th>
                                <th scope="col">Date</th>
                                <th scope="col">Referrer URL</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
									$total_new_records = 0;
									if ($total_new_bugs>0) { // got bugs? :)
										// now get the latest UNREAD BUGS:
										$get_new_bugs_SQL = "SELECT * FROM `bug_report` WHERE `status` = '1'".$add_SQL_0." GROUP BY `referrer_URL` ORDER BY `date_entered` DESC";
										// DEBUG:
										// 	echo "<h4>SQL: " . $get_new_bugs_SQL . "</h4>";

										$result_get_new_bugs = mysqli_query($con,$get_new_bugs_SQL);
										// while loop
										while($row_get_new_bugs = mysqli_fetch_array($result_get_new_bugs)) {

												$bug_id = $row_get_new_bugs['ID'];
												$bug_title = $row_get_new_bugs['title'];
												$bug_body = $row_get_new_bugs['body'];
												$bug_created_by = $row_get_new_bugs['created_by']; // use get_creator($user_id) function!
												$bug_referrer_URL = $row_get_new_bugs['referrer_URL'];
												$bug_URL = $row_get_new_bugs['URL'];
												$bug_status = $row_get_new_bugs['status'];
												$bug_date_entered = $row_get_new_bugs['date_entered'];


												?>
                                                    <tr>
                                                      <td class="text-center">
                                                        <a href="feedback_view.php?id=<?php echo $bug_id; ?>" class="clearfix">
                                                            <span class="fa-stack fa-lg">
                                                <?php

												$remove_this_many_chars = 10; // /cosmosys/

												if ($bug_title == '404 - Page not found!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-danger"></i>
                                                    		<i class="fa fi-skull fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'Page not in Database!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-warning"></i>
                                                                <i class="fa fa-question fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'Log-in Error') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-primary"></i>
                                                                <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'User Feedback') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-secondary"></i>
                                                              <i class="fa fa-bullhorn fa-stack-1x fa-inverse"></i>
												<?php }
												else { // default bug notification ?>
                                                              <i class="fa fa-circle fa-stack-2x text-info"></i>
                                                      			<i class="fa fa-info fa-stack-1x fa-inverse"></i>
												<?php

												}
												// now close the </li>
										?>
                                                            </span>
                                                        </a>
                                                      </td>
                                                      <td class="text-center">
													  	<a href="feedback_view.php?id=<?php echo $bug_id; ?>" class="clearfix">
															<?php echo $bug_title; ?>
                                                        </a>
                                                      </td>
                                                      <td class="text-center"><?php get_creator($bug_created_by); ?></td>
                                                      <td class="text-center"><?php echo substr($bug_date_entered, 0, 10); ?></td>
                                                      <td><?php

														  $ref_len = strlen($bug_referrer_URL);


														  if (substr($bug_referrer_URL,0,28) == 'http://www.cooscreations.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 22 );
														  }
														  else if (substr($bug_referrer_URL,0,24) == 'http://cooscreations.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 18 );
														  }
														  else if (substr($bug_referrer_URL,0,5) != '/epg/') {
															  $remove_this_many_chars = 0;
														  }
														  else { /* do nothing... */ }
														  // substract URL if it exists and '/cosmosys/'from the front of the from total length to get what we want...
														  $ref_keep_chars = ($ref_len - $remove_this_many_chars);
														  $final_ref_URL = substr($bug_referrer_URL,$remove_this_many_chars,$ref_keep_chars); // voila!

													   ?><a href="<?php echo $final_ref_URL; ?>"><?php echo $final_ref_URL; ?> <i class="fa fa-external-link"></i></a></td>
                                                    </tr>
											<?php

                              				$total_new_records = $total_new_records + 1;
											} // end get new bugs
										}
										else {
											?>
											<tr>
                                              <td>
													<span class="fa-stack fa-lg">
                                                      <i class="fa fa-circle fa-stack-2x text-success"></i>
                                                      <i class="fa fa-check fa-stack-1x fa-inverse"></i>
                                                    </span>
                                              </td>
                                              <td>
													<span class="title">No new bugs</span>
                                              </td>
                                              <td class="text-center">-</td>
                                              <td class="text-center">-</td>
                                              <td class="text-center">-</td>
											</tr>
											<?php
										////////////////////////////////////////////////////////////////////
                                // end WHILE loop:
                                }
                                // now close the table:
                                ?>
                                </tbody>
                    </table>




                        <!-- START DATA FOOTER... -->
                                <div class="row">
                                  <div class="col-md-4"><strong>Showing <?php echo $total_new_records; ?> records</strong>
                                  </div>
                                  <div class="col-md-8">
								  	<?php if ($_SESSION['user_level'] > 1) { ?>
                                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-success pull-right" onClick="document.location = this.value" value="add_bug_report.php"><i class="fa fa-plus-square"></i> Add New Bug Report</button>
                                    <?php }
                                    else { // empty div?!
                                        echo "&nbsp;";
                                    }?></div>
                                </div>
                                <!-- END DATA FOOTER -->
                              </div>
                            </section>


                    <section class="panel panel-primary">
                            <header class="panel-heading">
                                <div class="panel-actions">
                                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                                </div>

                                <span class="pull-left label label-<?php if ($total_open_bugs != 0) { ?>danger<? } else { ?>success<?php } ?>"><?php echo $total_open_bugs; ?></span>

                                <h2 class="panel-title"><i class="fa fa-bug fa-2x"></i> Open Bugs</h2>
                                <p class="panel-subtitle">
                                  We are aware of these bugs and are working on a fix right now - thank you for your patience!
                                </p>
                            </header>
                            <div class="panel-body">
                        <table class="table table-bordered table-striped table-condensed mb-none">
                          <thead>
                              <tr class="dark">
                                <th scope="col">Type</th>
                                <th scope="col">Title</th>
                                <th scope="col">Creator</th>
                                <th scope="col">Date</th>
                                <th scope="col">Referrer URL</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
									$total_open_records = 0;
									if ($total_open_bugs>0) { // got bugs? :)
										// now get the latest UNREAD BUGS:
										$get_new_bugs_SQL = "SELECT * FROM `bug_report` WHERE `status` = '2'".$add_SQL_0." GROUP BY `referrer_URL` ORDER BY `date_entered` DESC";

										$result_get_new_bugs = mysqli_query($con,$get_new_bugs_SQL);
										// while loop
										while($row_get_new_bugs = mysqli_fetch_array($result_get_new_bugs)) {

												$bug_id = $row_get_new_bugs['ID'];
												$bug_title = $row_get_new_bugs['title'];
												$bug_body = $row_get_new_bugs['body'];
												$bug_created_by = $row_get_new_bugs['created_by'];
												$bug_referrer_URL = $row_get_new_bugs['referrer_URL'];
												$bug_URL = $row_get_new_bugs['URL'];
												$bug_status = $row_get_new_bugs['status'];
												$bug_date_entered = $row_get_new_bugs['date_entered'];
												?>
                                                    <tr>
                                                      <td class="text-center">
                                                        <a href="feedback_view.php?id=<?php echo $bug_id; ?>" class="clearfix">
                                                            <span class="fa-stack fa-lg">
                                                <?php

												$remove_this_many_chars = 10; // /cosmosys/

												if ($bug_title == '404 - Page not found!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-danger"></i>
                                                    		<i class="fa fi-skull fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'Page not in Database!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-warning"></i>
                                                                <i class="fa fa-question fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'Log-in Error') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-primary"></i>
                                                                <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'User Feedback') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-secondary"></i>
                                                              <i class="fa fa-bullhorn fa-stack-1x fa-inverse"></i>
												<?php }
												else {
													// default bug notification ?>
                                                                <i class="fa fa-circle fa-stack-2x text-info"></i>
                                                      			<i class="fa fa-info fa-stack-1x fa-inverse"></i>
												<?php }
												// now close the </li>
										?>
                                                            </span>
                                                        </a>
                                                      </td>
                                                      <td class="text-center">
													  	<a href="feedback_view.php?id=<?php echo $bug_id; ?>" class="clearfix">
															<?php echo $bug_title; ?>
                                                        </a>
                                                      </td>
                                                      <td class="text-center"><?php get_creator($bug_created_by); ?></td>
                                                      <td class="text-center"><?php echo substr($bug_date_entered, 0, 10); ?></td>
                                                      <td><?php

														  $ref_len = strlen($bug_referrer_URL);


														  if (substr($bug_referrer_URL,0,22) == 'http://www.cosmodg.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 22 );
														  }
														  else if (substr($bug_referrer_URL,0,18) == 'http://cosmodg.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 18 );
														  }
														  else if (substr($bug_referrer_URL,0,10) != '/cosmosys/') {
															  $remove_this_many_chars = 0;
														  }
														  else { /* do nothing... */ }
														  // substract URL if it exists and '/cosmosys/'from the front of the from total length to get what we want...
														  $ref_keep_chars = ($ref_len - $remove_this_many_chars);
														  $final_ref_URL = substr($bug_referrer_URL,$remove_this_many_chars,$ref_keep_chars); // voila!

													   ?><a href="<?php echo $final_ref_URL; ?>"><?php echo $final_ref_URL; ?> <i class="fa fa-external-link"></i></a></td>
                                                    </tr>
											<?php

                              				$total_open_records = $total_open_records + 1;
											} // end get new bugs
										}
										else {
											?>
											<tr>
                                              <td>
													<span class="fa-stack fa-lg">
                                                      <i class="fa fa-circle fa-stack-2x text-success"></i>
                                                      <i class="fa fa-check fa-stack-1x fa-inverse"></i>
                                                    </span>
                                              </td>
                                              <td>
													<span class="title">No open bugs</span>
                                              </td>
                                              <td class="text-center">-</td>
                                              <td class="text-center">-</td>
                                              <td class="text-center">-</td>
											</tr>
											<?php
										////////////////////////////////////////////////////////////////////
                                // end WHILE loop:
                                }
                                // now close the table:
                                ?>
                                </tbody>
                    </table>




                        <!-- START DATA FOOTER... -->
                                <div class="row">
                                  <div class="col-md-4"><strong>Showing <?php echo $total_open_records; ?> records</strong>
                                  </div>
                                  <div class="col-md-8">
								  	<?php if ($_SESSION['user_level'] > 1) { ?>
                                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-success pull-right" onClick="document.location = this.value" value="add_bug_report.php"><i class="fa fa-plus-square"></i> Add New Bug Report</button>
                                    <?php }
                                    else { // empty div?!
                                        echo "&nbsp;";
                                    }?></div>
                                </div>
                                <!-- END DATA FOOTER -->
                              </div>
                            </section>


                    <section class="panel panel-success">
                            <header class="panel-heading">
                                <div class="panel-actions">
                                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                                </div>

                                <span class="pull-left label label-<?php if ($total_closed_bugs == 0) { ?>danger<? } else { ?>primary<?php } ?>"><?php echo $total_closed_bugs; ?></span>

                                <h2 class="panel-title"><i class="fa fa-check fa-2x"></i> Fixed Bugs</h2>
                                <p class="panel-subtitle">
                                  These bugs have been fixed and hopefully won't bother us again!
                                </p>
                            </header>
                            <div class="panel-body">
                        <table class="table table-bordered table-striped table-condensed mb-none">
                          <thead>
                              <tr class="dark">
                                <th scope="col">Type</th>
                                <th scope="col">Title</th>
                                <th scope="col">Creator</th>
                                <th scope="col">Date</th>
                                <th scope="col">Referrer URL</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
									$total_closed_records = 0;
									if ($total_closed_bugs>0) { // got bugs? :)
										// now get the latest UNREAD BUGS:
										$get_new_bugs_SQL = "SELECT * FROM `bug_report` WHERE `status` = '0'".$add_SQL_0." GROUP BY `referrer_URL` ORDER BY `date_entered` DESC";

										$result_get_new_bugs = mysqli_query($con,$get_new_bugs_SQL);
										// while loop
										while($row_get_new_bugs = mysqli_fetch_array($result_get_new_bugs)) {

												$bug_id = $row_get_new_bugs['ID'];
												$bug_title = $row_get_new_bugs['title'];
												$bug_body = $row_get_new_bugs['body'];
												$bug_created_by = $row_get_new_bugs['created_by'];
												$bug_referrer_URL = $row_get_new_bugs['referrer_URL'];
												$bug_URL = $row_get_new_bugs['URL'];
												$bug_status = $row_get_new_bugs['status'];
												$bug_date_entered = $row_get_new_bugs['date_entered'];
												?>
                                                    <tr>
                                                      <td class="text-center">
                                                        <a href="feedback_view.php?id=<?php echo $bug_id; ?>" class="clearfix">
                                                            <span class="fa-stack fa-lg">
                                                <?php

												$remove_this_many_chars = 10; // /cosmosys/

												if ($bug_title == '404 - Page not found!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-danger"></i>
                                                    		  <i class="fa fi-skull fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'Page not in Database!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-warning"></i>
                                                              <i class="fa fa-question fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'Log-in Error') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-primary"></i>
                                                              <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_title == 'User Feedback') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-secondary"></i>
                                                              <i class="fa fa-bullhorn fa-stack-1x fa-inverse"></i>
												<?php }
												else {
													// default bug notification ?>
                                                              <i class="fa fa-circle fa-stack-2x text-info"></i>
                                                      		  <i class="fa fa-info fa-stack-1x fa-inverse"></i>
												<?php }
												// now close the </li>
										?>
                                                            </span>
                                                        </a>
                                                      </td>
                                                      <td class="text-center">
													  	<a href="feedback_view.php?id=<?php echo $bug_id; ?>" class="clearfix">
															<?php echo $bug_title; ?>
                                                        </a>
                                                      </td>
                                                      <td class="text-center"><?php get_creator($bug_created_by); ?></td>
                                                      <td class="text-center"><?php echo substr($bug_date_entered, 0, 10); ?></td>
                                                      <td><?php

														  $ref_len = strlen($bug_referrer_URL);


														  if (substr($bug_referrer_URL,0,22) == 'http://www.cosmodg.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 22 );
														  }
														  else if (substr($bug_referrer_URL,0,18) == 'http://cosmodg.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 18 );
														  }
														  else if (substr($bug_referrer_URL,0,10) != '/cosmosys/') {
															  $remove_this_many_chars = 0;
														  }
														  else { /* do nothing... */ }
														  // substract URL if it exists and '/cosmosys/'from the front of the from total length to get what we want...
														  $ref_keep_chars = ($ref_len - $remove_this_many_chars);
														  $final_ref_URL = substr($bug_referrer_URL,$remove_this_many_chars,$ref_keep_chars); // voila!

													   ?><a href="<?php echo $final_ref_URL; ?>"><?php echo $final_ref_URL; ?> <i class="fa fa-external-link"></i></a></td>
                                                    </tr>
											<?php

                              				$total_closed_records = $total_closed_records + 1;
											} // end get new bugs
										}
										else {
											?>
											<tr>
                                              <td>
													<span class="fa-stack fa-lg">
                                                      <i class="fa fa-circle fa-stack-2x text-success"></i>
                                                      <i class="fa fa-check fa-stack-1x fa-inverse"></i>
                                                    </span>
                                              </td>
                                              <td>
													<span class="title">No Fixed Bugs</span>
                                              </td>
                                              <td class="text-center">-</td>
                                              <td class="text-center">-</td>
                                              <td class="text-center">-</td>
											</tr>
											<?php
										////////////////////////////////////////////////////////////////////
                                // end WHILE loop:
                                }
                                // now close the table:
                                ?>
                                </tbody>
                    </table>




                        <!-- START DATA FOOTER... -->
                                <div class="row">
                                  <div class="col-md-4"><strong>Showing <?php echo $total_closed_records; ?> records</strong>
                                  </div>
                                  <div class="col-md-8">
								  	<?php if ($_SESSION['user_level'] > 1) { ?>
                                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-success pull-right" onClick="document.location = this.value" value="add_bug_report.php"><i class="fa fa-plus-square"></i> Add New Bug Report</button>
                                    <?php }
                                    else { // empty div?!
                                        echo "&nbsp;";
                                    }?></div>
                                </div>
                                <!-- END DATA FOOTER -->
                              </div>
                            </section>




					<!-- end: page -->


<?php
// now close the page out:
pagefoot($page_id);

?>
