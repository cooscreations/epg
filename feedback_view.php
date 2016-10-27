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
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

if ((isset($_REQUEST['id']))&&($_REQUEST['id']!='')&&($_REQUEST['id']!=0)) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: feedback.php?msg=NG&action=view_bug_report_profile&error=no_id");
	exit();
}

$page_id = 66; // in theory we shouldn't need this, rather look it up based on the filename?!

// pull the header and template stuff:
pagehead($page_id);


// get bug report info
$get_bug_report_SQL = "SELECT * FROM  `bug_report` WHERE  `ID` = " . $record_id;

$result_get_bug_report = mysqli_query($con,$get_bug_report_SQL);
// while loop
while($row_get_bug_report = mysqli_fetch_array($result_get_bug_report)) {

		// now print each record:
		$bug_report_ID 				= $row_get_bug_report['ID'];
		$bug_report_title 			= $row_get_bug_report['title'];
		$bug_report_body 			= $row_get_bug_report['body'];
		$bug_report_created_by 		= $row_get_bug_report['created_by'];
		$bug_report_referrer_URL 	= $row_get_bug_report['referrer_URL'];
		$bug_report_URL 			= $row_get_bug_report['URL'];
		$bug_report_status 			= $row_get_bug_report['status'];
		$bug_report_date_entered 	= $row_get_bug_report['date_entered'];
		$bug_report_date_closed 	= $row_get_bug_report['date_closed'];
		$bug_report_admin_remarks 	= $row_get_bug_report['admin_remarks'];
		$bug_report_closed_by 		= $row_get_bug_report['closed_by'];


		// find out how many TOTAL bugs we have realted to this page:
		$count_page_bug_freq_sql = "SELECT COUNT( ID ) FROM  `bug_report` WHERE `referrer_URL` LIKE  '" . $bug_report_referrer_URL . "' AND `status` != '0' AND `ID` != '" . $bug_report_ID . "'";
		// echo "<h3>".$count_page_bug_freq_sql."</h3>";
		$count_page_bug_freq_query = mysqli_query($con, $count_page_bug_freq_sql);
		$count_page_bug_freq_row = mysqli_fetch_row($count_page_bug_freq_query);
		// Here we have the total row count
		$total_page_bug_freq = $count_page_bug_freq_row[0];

	}// END WHILE LOOP
?>



<!-- START MAIN PAGE BODY : -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Feedback Report</h2>

						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><a href="feedback.php">All Feedback</a></li>
								<li><span>Feedback Report</span></li>
							</ol>

							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
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

					<!-- START MAIN COLUMN: -->
                    <div class="col-md-8">

                    <!-- start a panel -->
                    <section class="panel panel-<?php if ($bug_report_status == 0) { ?>success<? } else if ($bug_report_status == 1) { ?>danger<? } else { ?>primary<? } ?>">
                        <header class="panel-heading">
                          <div class="panel-actions"> <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a> <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a> </div>
                          <h2 class="panel-title"><span class="fa-stack fa-lg">
                                                <?php

												$remove_this_many_chars = 10; // /cosmosys/

												if ($bug_report_title == '404 - Page not found!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-danger"></i>
                                                    		<i class="fa fi-skull fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_report_title == 'Page not in Database!') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-warning"></i>
                                                                <i class="fa fa-question fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_report_title == 'Log-in Error') { ?>
                                                              <i class="fa fa-circle fa-stack-2x text-primary"></i>
                                                                <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
												<?php }
												else if ($bug_report_title == 'User Feedback') { ?>
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
                          <?php echo $bug_report_title; ?></h2>
                          <p class="panel-subtitle">
                                  Created: <strong><?php echo substr($bug_report_date_entered,0,10); ?></strong> | Status: <strong><?php if ($bug_report_status == 0) { ?>CLOSED<? } else if ($bug_report_status == 1) { ?>NEW<? } else { ?>IN PROGRESS<? } ?></strong>
                          </p>
                        </header>
                        <div class="panel-body">
                          <div class="content">
                            <ul class="simple-user-list">
                            <li>
                            
                                <figure class="image rounded">
									<?php get_img('users', $bug_report_created_by, 1, 50); ?>
								</figure>

                                <span class="title"><?php
								if ($bug_report_created_by != 0) {
									echo get_creator($bug_report_created_by);
								}
								else {
									?>No User Data Found<?php
								}?></span>
                                <span class="message truncate">
                                	Bug Report Opener
                                	<a href="view_bug_report.php?user_id=<?php echo $bug_report_created_by; ?>">
                                    	<small>
                                        	(VIEW ALL)
                                        </small>
                                    </a>
                                </span>
                            </li>

							<li>
                                <figure class="image rounded">
                                  <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x text-success"></i>
                                    <i class="fa fa-link fa-stack-1x fa-inverse"></i>
                                  </span>
                                </figure>

                                <span class="title"><?php

														  $ref_len = strlen($bug_report_referrer_URL);


														  if (substr($bug_report_referrer_URL,0,22) == 'http://www.cosmodg.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 22 );
														  }
														  else if (substr($bug_report_referrer_URL,0,18) == 'http://cosmodg.com') {
															 $remove_this_many_chars = ($remove_this_many_chars + 18 );
														  }
														  else if (substr($bug_report_referrer_URL,0,10) != '/cosmosys/') {
															  $remove_this_many_chars = 0;
														  }
														  else { /* do nothing... */ }
														  // substract URL if it exists and '/cosmosys/'from the front of the from total length to get what we want...
														  $ref_keep_chars = ($ref_len - $remove_this_many_chars);
														  $final_ref_URL = substr($bug_report_referrer_URL,$remove_this_many_chars,$ref_keep_chars); // voila!

													   ?><a href="<?php echo $final_ref_URL; ?>"><?php echo $final_ref_URL; ?></a>

                                                       <span class="label label-warning" title="This URL appears in <?php echo $total_page_bug_freq; ?> other bug reports - please see the list on the right">
                                                       <?php echo $total_page_bug_freq; ?>
                                                       </span>

                                                       </span>
                                <span class="message truncate">Referring URL</span>
                            </li>


                            <li>
                                <figure class="image rounded">
                                  <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x text-info"></i>
                                    <i class="fa fa-info fa-stack-1x fa-inverse"></i>
                                  </span>
                                </figure>

                                <span class="title">
                                	<span style="font-size:xxl; font-weight:bold;">
                                    	"
                                    </span>
									<?php echo $bug_report_body; ?>
                                	<span style="font-size:xxl; font-weight:bold;">
                                    	"
                                    </span>
                                </span>
                                <span class="message truncate">Description</span>
                            </li>


                            <li>

                                <figure class="image rounded">
                                  <span class="fa-stack fa-lg">
                                    <?php
									if ($bug_report_status == 1) {
										?>
                                        <i class="fa fa-circle fa-stack-2x text-danger"></i>
                                    	<i class="fa fa-exclamation fa-stack-1x fa-inverse"></i><?php
									}
									else if ($bug_report_status == 2) {
										?>
                                        <i class="fa fa-circle fa-stack-2x text-primary"></i>
                                    	<i class="fa fa-clock-o fa-stack-1x fa-inverse"></i><?php
									}
									else {
										?>
                                        <i class="fa fa-circle fa-stack-2x text-success"></i>
                                    	<i class="fa fa-check fa-stack-1x fa-inverse"></i><?php
									}
									?>
                                  </span>
                                </figure>

                                <span class="title"><?php echo $bug_report_status == 1 ? '<span class="text-danger">NEW / UNREAD</span>' : ($bug_report_status == 2 ? '<span class="text-primary">OPEN / PENDING CLOSURE</span>' : '<span class="text-success">CLOSED</span>')?></span>
                                <span class="message truncate">Status</span>
                            </li>

                            <?php if ( ($bug_report_closed_by != '') && ($bug_report_closed_by != 0) ) { ?>
                            <li>
                                <figure class="image rounded">
									<?php get_img('users', $bug_report_closed_by, 1, 50); ?>
								</figure>

                                <span class="title"><?php echo get_creator($bug_report_closed_by); ?></span>
                                <span class="message truncate">Bug Report Closer</span>
                            </li>
                            <li>
                                <figure class="image rounded">
                                    <span class="fa-stack fa-lg">
                                        <i class="fa fa-circle fa-stack-2x text-default"></i>
                                        <i class="fa fa-calendar fa-stack-1x fa-inverse"></i>
                                      </span>
                                </figure>

                                <span class="title"><?php echo substr($bug_report_date_closed,0,10); ?></span>
                                <span class="message truncate">Date Closed</span>
                            </li>
                            <?php } // end found a closer...

							else {
								?>

								<li>
                                <figure class="image rounded">
                                        <?php get_img('users', $_SESSION['user_ID'], 1, 50); ?>
									</figure>

                                <span class="title"><?php echo get_creator($_SESSION['user_ID']); ?></span>
                                <span class="message truncate">This could be you! Click here to <a href="edit_bug_report.php?id=<?php echo $record_id; ?>"><strong>CLAIM THIS BUG</strong></a> and make our data system a better place.</span>
                            </li>

								<?
							}

							?>

                            </ul>
                            </div>
                            </div>
                            </section>


                      <?php if ($bug_report_admin_remarks != '') { ?>

                    <!-- start a panel -->
                    <section class="panel panel-warning">
                        <header class="panel-heading">
                          <div class="panel-actions"> <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a> <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a> </div>
                          <h2 class="panel-title">
                          	<i class="fa fa-cogs"></i>
                            Admin Remarks
						  </h2>
                          <p class="panel-subtitle">All the geeky stuff...</p>
                        </header>
                        <div class="panel-body">
                          <div class="content">
                            <?php
							if (substr($bug_report_admin_remarks,0,6) == 'Server') {
								echo '<pre>' . $bug_report_admin_remarks . '</pre>';
							}
							else {
								echo htmlspecialchars_decode($bug_report_admin_remarks);
							}
                            ?>
                            </div>
                            </div>
                            </section>

                            <?php
					  } // end found  bug_report_admin_remarks
					  ?>


                    <!-- END MAIN COLUMN -->
                    </div>

                    <!-- START RIGHT COL -->
                    <div class="col-md-4">
                    	<!-- SIDE PANEL -->

                        <?php admin_icons($record_id, '0', 'edit_bug_report', 'delete_bug_report_profile', 'bug_report'); ?>

                        <ul class="simple-card-list mb-xlg">
                          <li class="primary">
                            <a href="view_bug_report.php" title="Click to go back to the list of bugs">
                              <i class="fa fa-list fa-2x pull-right"></i>
                              <h3>View Bug List</h3>
                            </a>
                            <p>Return to the list of bugs</p>
                          </li>

                          <li class="success">
                              <i class="fa fa-eye fa-2x pull-right"></i>
                              <h3><?php echo ($total_views + 1); ?></h3>
                            </a>
                            <p>TOTAL PAGE VIEWS</p>
                          </li>

                        </ul>




                        <!-- start a panel -->
                    <section class="panel panel-dark">
                        <header class="panel-heading">
                          <div class="panel-actions"> <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a> <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a> </div>
                          <h2 class="panel-title">
                          Other Bugs Related to This Page
						  </h2>
                          <p class="panel-subtitle">Other bugs were found associated to this URL</p>
                        </header>
                        <div class="panel-body">
                          <div class="content">

                     <?php if ( $total_page_bug_freq > 0 ) { // OTHER BUGS FOUND ON THIS PAGE! ?>

                           <table class="table table-bordered table-striped table-condensed mb-none">
                           <thead>
                              <tr class="dark">
                                <th scope="col">Type</th>
                                <th scope="col">Title</th>
                                <th scope="col">Creator</th>
                                <th scope="col">Date</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
									if ($total_page_bug_freq > 0) { // got bugs? :)
										// now get the latest related bugs:
										$get_new_bugs_SQL = "SELECT * FROM `bug_report` WHERE `referrer_URL` LIKE  '" . $bug_report_referrer_URL . "' AND `status` != '0' AND `ID` != '" . $bug_report_ID . "' ORDER BY `date_entered` DESC";

										// echo '<h2>SQL: ' . $get_new_bugs_SQL . '</h2>';

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
                                                        <a href="view_bug_report_profile.php?id=<?php echo $bug_id; ?>" class="clearfix" title="STATUS: <?php echo $bug_status; ?>">
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
                                                      <td class="text-center"><?php
													  if ($bug_body != ''){
													  	?><a data-toggle="popover" data-container="body" data-placement="top" title="Details:" data-content="<?php echo $bug_body; ?>"><?php
														}

													  echo $bug_title;

													  if ($bug_body != '') {
													  	?> <i class="fa fa-info-circle info"></i></a><?php
													  } ?></td>
                                                      <td class="text-center">
                                                      	<figure class="image rounded">
                                                            <a href="view_user_profile.php?id=<?php echo $bug_created_by; ?>"><?php
                                                        // default:
                                                        $show_upload_link = false;

                                                        // check image file exists:

                                                        $image_file_name = 'images/users/user_' . $bug_created_by . '.jpg';

                                                        if (file_exists($image_file_name)) {
                                                            $show_img = $image_file_name;
                                                            $show_upload_link = false;
                                                        }
                                                        else {

                                                            $show_upload_link = true;
                                                            $show_img = 'images/users/user_0.jpg';
                                                        }

                                                        ?>
                                                        <img src="<?php echo $show_img; ?>" class="img-circle" alt="Click to view">
                                                        </a>
                                                        </figure></td>
                                                      <td class="text-center"><?php
													  if ($_SESSION['user_level'] == 8) { // admin quick-link to edit!
													  		?><a href="edit_bug_report.php?id=<?php echo $bug_id; ?>" class="clearfix text-danger" title="STATUS: <?php echo $bug_status; ?>"><?php
															echo substr($bug_date_entered, 0, 10);
															?></a><?php
													  }
													  else {
														  	echo substr($bug_date_entered, 0, 10);
													  }
													  ?></td>
                                                    </tr>
											<?php

                              				$total_new_records = $total_new_records + 1;
											} // end get new bugs
									}
											?>
                                            </tbody>
                                            </table>
                            <?php
							} // END FOUND OTHER BUGS ON THIS PAGE...
							else {
								?>

                                <p class="text-success"><i class="fa fa-check"></i> There are no other bugs (NEW / OPEN) on this page. Well done!</p>

								<?php
							} // end NO BUGS FOUND note
							?>

                            </div>
                            </div>
                            </section>



                        <!-- END SIDE PANEL! -->
                    <!-- end the right column -->
                    </div>

					<!-- end: page -->

					</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot($page_id);

?>
