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

if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else { $record_id = ''; }

require ('page_functions.php'); 
include 'db_conn.php';
require ('data_functions.php'); 

pagehead($page_id, $record_id); ?>

					<!-- start: page -->
                    <?php 
					if (($_REQUEST['error'] == 'page_not_in_DB')&&($_SESSION["user_level"] > 7)) {
						?>
						<p>The file <strong><?php echo $_REQUEST['filename']; ?></strong> can be <a href="add_page.php?filename=<?php echo $_REQUEST['filename']; ?>">added to the system by clicking here</a>.</p>
                        <p>We have not added this to the bug report list as you are expected to create the page immediately ^_^</p>
						<?php
					}
					else if ($_REQUEST['error'] == 'page_not_in_DB') {
						
						// add this to the wishlist!
						$add_bug_report_SQL = "INSERT INTO `bug_report`(`ID`, `title`, `body`, `created_by`, `referrer_URL`, `URL`, `status`, `date_entered`) VALUES (NULL,'Page not in Database!','A page was missing from the database. Please add the record and specify access rights in order to allow this page to be view.','" . $_SESSION['user_id'] . "','" . $_REQUEST['filename'] . "','user_message.php','1','" . date("Y-m-d H:i:s") . "')";
						
							if (mysqli_query($con, $add_bug_report_SQL)) {	
								$newest_bug_report_ID = mysqli_insert_id($con);
								?><p><strong><a href="view_bug_report.php?id=<?php echo $newest_bug_report_ID; ?>">We have notified the administrator <em>(view bug report)</em></a></strong>. <br />Please continue using this site by using the main menu or search box.</p><?php
							}
							else {
								?><p id="0">Please consider raising a <strong><a class="btn btn-warning simple-ajax-modal" href="info_pop.php?id=feedback&referrer=<?php echo $_SERVER['HTTP_REFERER']; ?>&ref_page=<?php echo $page_filename; ?>&page_id=<?php echo $page_id; ?>">bug report</a></strong> or continue using the site as normal.</p><?php
							}
					}
					
					// 404
					else if ($_REQUEST['error']=='404') {
						if (isset($_REQUEST['failed_file'])) {
							// add this as a log in the report table!
							
							// add this to the wishlist!
							
							$server_vars = print_r($_SERVER, true);
							$session_vars = print_r($_SESSION, true);    
							
							$add_bug_report_SQL = "INSERT INTO `bug_report`(`ID`, `title`, `body`, `created_by`, `referrer_URL`, `URL`, `status`, `date_entered`, `admin_remarks`) VALUES (NULL,'404 - Page not found!','The requested page could not be found. Please consider creating this page or removing any links to it!','" . $_SESSION['user_id'] . "','" . $_REQUEST['failed_file'] . "','user_message.php','1','" . date("Y-m-d H:i:s") . "', 'Server Variables: 

" . strip_tags($server_vars) . "

Session Variables: 

" . strip_tags($session_vars) . "')";
							
							// echo "<h3>".$add_bug_report_SQL."</h3>";
							if (mysqli_query($con, $add_bug_report_SQL)) {	
								$newest_bug_report_ID = mysqli_insert_id($con);
								?><p><strong><a href="view_bug_report_profile.php?id=<?php echo $newest_bug_report_ID; ?>">We have notified the administrator <em>(view bug report)</em>.</a></strong><br /> Please continue using this site by using the main menu or search box.</p><?php
							}
							else {
								?><p id="1">Please consider raising a <strong><a class="btn btn-warning simple-ajax-modal" href="info_pop.php?id=feedback&referrer=<?php echo $_SERVER['HTTP_REFERER']; ?>&ref_page=<?php echo $page_filename; ?>&page_id=<?php echo $page_id; ?>">bug report</a></strong> or continue using the site as normal.</p>
								
								
								
								
								
								<?php
							}
							
						}
						else {
							?><p id="2">Please consider raising a <strong><a class="btn btn-warning simple-ajax-modal" href="info_pop.php?id=feedback&referrer=<?php echo $_SERVER['HTTP_REFERER']; ?>&ref_page=<?php echo $page_filename; ?>&page_id=<?php echo $page_id; ?>">bug report</a></strong> or continue using the site as normal.</p><?php
						}	
					}
						
					////////////////////////////////////////////////
					
					?>
					<!-- end: page -->
				
<?php pagefooter($page_id, $record_id); ?>