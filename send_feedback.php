<?php
// THIS IS AN INVISIBLE PAGE...

session_start();
include 'db_conn.php';


//add the recipient's address here
$myemail = 'coos@cooscreations.com, vinay11283@gmail.com';
 
//grab named inputs from html then post to #thanks
if (isset($_POST['name'])) {
$name = strip_tags($_POST['name']);
$email = strip_tags($_POST['email']);
$message = strip_tags($_POST['message']);
$referrer = strip_tags($_POST['referrer']);
$server_vars = strip_tags($_POST['server_vars']);
$session_vars = strip_tags($_POST['session_vars']);
 
//generate email and send!
$to = $myemail;
$email_subject = "Feedback from: $name";
$email_body = '
	<div style="font-family:sans-serif;">' . "\r\n" . '
	<a href="http://www.cooscreations.com/epg/index.php"><img src="http://www.cooscreations.com/epg/assets/images/logo.png" title="logo" style="border:0;" /></a>' . "\r\n" . '
	<h1>new feedback</h1>' . "\r\n" . '
	<p>You have received a new feedback message from <a href="mailto:' . $email . '">' . $name . '</a>.</p>' . "\r\n" . '
	<p><strong>Message:</strong></p>' . "\r\n" . '
	<pre>' . $message .'</pre>' . "\r\n" . '
	<p>Referrer:<a href="' . $referrer . '" target="_blank">' . $referrer . '</a></p>' . "\r\n" . '
	<hr />' . "\r\n" . '
	<h2>Server Variables</h2>' . "\r\n" . '
	<pre>'.$server_vars.'</pre>' . "\r\n" . '
	<hr />' . "\r\n" . '
	<h2>Session Variables</h2>' . "\r\n" . '
	<pre>'.$session_vars.'</pre>' . "\r\n" . '
	<hr />' . "\r\n" . '
	<p>To view this and all other feedback, please log in to <a href="http://www.cooscreations.com/epg/"><strong>epg</strong>dms</a></p>' . "\r\n" . '
	<p style="font-size:x-small; text-align:center;"><strong>epg</strong>dms - &copy; 2015 - ' . date("Y") . ' EPG All Rights Reserved.</p>' . "\r\n" . '
	</div>';
	
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= "From: $email\n";
$headers .= "Reply-To: $email";
mail($to,$email_subject,$email_body,$headers);
}

// now log the feedback in the bug_report table:
$log_bug_SQL = "INSERT INTO `bug_report` (`ID`, `title`, `body`, `created_by`, `referrer_URL`, `URL`, `status`, `date_entered`, `date_closed`, `admin_remarks`, `closed_by`) VALUES (NULL, 'User Feedback', '" . addslashes($message) . "', '" . $_SESSION['user_id'] . "', '" . addslashes($referrer) . "', 'info_pop.php', '1', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', 'Server Variables: 

" . $server_vars . "

Session Variables: 

" . $session_vars . "', '0');";
						
							if (mysqli_query($con, $log_bug_SQL)) {	
								$newest_bug_report_ID = mysqli_insert_id($con);
								
								
								/*
								// UPDATE - gamification:
								
								// let's check to see if this is their first, 10th, 50th or 100th bug report
								
								$count_bugs_SQL = "SELECT COUNT(ID) FROM `bug_report` WHERE `created_by` ='" . $_SESSION['user_id'] . "'";
								$count_bugs_query = mysqli_query($con, $count_bugs_SQL);
								$count_bugs_row = mysqli_fetch_row($count_bugs_query);
								// Here we have the total row count
								$total_bugs = $count_bugs_row[0];
								
								$add_URL = '';
								
								
								if ($total_bugs == 1) {
										// give them an award for creating their first bug!
										$winner_SQL = "INSERT INTO `user_awards`(`ID`, `award_ID`, `user_ID`, `date_entered`, `awarded_by`, `award_notes`) VALUES (NULL,'6','" . $_REQUEST['user_id'] . "','" . date("Y-m-d H:i:s") . "','2','Thank you for improving cosmosys! You can also win bug awards at higher increments, so don't stop there!')";
					
										if (mysqli_query($con, $winner_SQL)) {
											$newest_award_ID = mysqli_insert_id($con);
											// now update the update log!
											$record_update_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'user_awards','" . $newest_award_ID . "','" . $_REQUEST['user_id'] . "','User won the \'Bug Hunter\' Academy Award for reporting their first bug.','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
											
											$add_URL = "&award_ID=".$newest_award_ID;
											
										} // end do update
								}
								else if ($total_bugs == 10) {
									// award!
										// give them an award for creating their tenth bug!
										$winner_SQL = "INSERT INTO `user_awards`(`ID`, `award_ID`, `user_ID`, `date_entered`, `awarded_by`, `award_notes`) VALUES (NULL,'7','" . $_REQUEST['user_id'] . "','" . date("Y-m-d H:i:s") . "','2','YEAH! Keep going! You can also win bug awards at higher increments, so don't stop there!')";
					
										if (mysqli_query($con, $winner_SQL)) {
											$newest_award_ID = mysqli_insert_id($con);
											// now update the update log!
											$record_update_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'user_awards','" . $newest_award_ID . "','" . $_REQUEST['user_id'] . "','User won the \'Bug Buster\' Academy Award for reporting their 10th bug.','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
											
											$add_URL = "&award_ID=".$newest_award_ID;
											
										} // end do update
								}
								else if ($total_bugs == 50) {
									// award!
										// give them an award for creating their tenth bug!
										$winner_SQL = "INSERT INTO `user_awards`(`ID`, `award_ID`, `user_ID`, `date_entered`, `awarded_by`, `award_notes`) VALUES (NULL,'8','" . $_REQUEST['user_id'] . "','" . date("Y-m-d H:i:s") . "','2','Amazing! You found 50 problems! You can also win bug awards at higher increments, so don't stop there!')";
					
										if (mysqli_query($con, $winner_SQL)) {
											$newest_award_ID = mysqli_insert_id($con);
											// now update the update log!
											$record_update_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'user_awards','" . $newest_award_ID . "','" . $_REQUEST['user_id'] . "','User won the \'Bug Blaster\' Academy Award for reporting their 50th bug!','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
											
											$add_URL = "&award_ID=".$newest_award_ID;
											
										} // end do update
								}
								else if ($total_bugs == 100) {
									// award!
										// give them an award for creating their tenth bug!
										$winner_SQL = "INSERT INTO `user_awards`(`ID`, `award_ID`, `user_ID`, `date_entered`, `awarded_by`, `award_notes`) VALUES (NULL,'9','" . $_REQUEST['user_id'] . "','" . date("Y-m-d H:i:s") . "','2','WOW! Keep going! You can also win bug awards at higher increments, so don't stop there!')";
					
										if (mysqli_query($con, $winner_SQL)) {
											$newest_award_ID = mysqli_insert_id($con);
											// now update the update log!
											$record_update_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'user_awards','" . $newest_award_ID . "','" . $_REQUEST['user_id'] . "','User won the \'Bug Breaker\' Academy Award for reporting their 100th bug!','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
											
											$add_URL = "&award_ID=".$newest_award_ID;
											
										} // end do update
								}
								else if ($total_bugs == 500) {
									// award!
										// give them an award for creating their tenth bug!
										$winner_SQL = "INSERT INTO `user_awards`(`ID`, `award_ID`, `user_ID`, `date_entered`, `awarded_by`, `award_notes`) VALUES (NULL,'10','" . $_REQUEST['user_id'] . "','" . date("Y-m-d H:i:s") . "','2','We never thought we'd see it - but you are a LEGEND! Thank you for helping us to improve in 500 different ways!')";
					
										if (mysqli_query($con, $winner_SQL)) {
											$newest_award_ID = mysqli_insert_id($con);
											// now update the update log!
											$record_update_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'user_awards','" . $newest_award_ID . "','" . $_REQUEST['user_id'] . "','User won the \'Bug Master\' Academy Award for reporting their 500th bug!','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
											
											$add_URL = "&award_ID=".$newest_award_ID;
											
										} // end do update
								}
								else {
									
									
								}
								
								if ($record_update_SQL != '') {
									if (mysqli_query($con, $record_update_SQL)) { 
										// award recorded OK!
									}
									else {
										echo "<h4>Error with SQL: " . $record_update_SQL . "</h4>";
									}
								}
								
								*/
								
								// now just record the feedback:
								
								$record_feedback_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'bug_report','" . $newest_bug_report_ID . "','" . $_REQUEST['user_id'] . "','User provided feedback','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
								
								if (mysqli_query($con, $record_feedback_SQL)) { 
									// feedback recorded OK!
								}
								else {
									echo "<h4>Error with SQL: " . $record_feedback_SQL . " on line " . __LINE__ . "</h4>";
								}
								
								
								// now check to see if the last 4 characters are '.php'
								if (substr($referrer, -4) == '.php') {
									header("Location:" . $referrer . "?msg=OK&action=feedback_sent&feedback_id=" . $newest_bug_report_ID . $add_URL);
									exit();
								
								}
								else {
									header("Location:" . $referrer . "&msg=OK&action=feedback_sent&feedback_id=" . $newest_bug_report_ID . $add_URL);
									exit();
								}
								
							}
							else {
								// bug not logged?! odd!
							}


?>