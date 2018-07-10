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

require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

/*
// DEBUG - POST ALL FORM VARIABLES:
foreach ($_POST as $key => $value)
 echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
*/


/*

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/

$update_table = "IQC_report_results";

$IQC_report_ID 		= checkaddslashes($_REQUEST['IQC_report_ID']);
$crit_dim_ID 		= checkaddslashes($_REQUEST['crit_dim_ID']);
$method_class_ID 	= checkaddslashes($_REQUEST['method_class_ID']);
$date_entered 		= date("Y-m-d H:i:s");
$created_by 		= $_SESSION['user_ID'];

$update_note 		= 'Adding a new test result.'; 


if ($method_class_ID == 2) {

			$result_to_write = $_REQUEST['ICQ_result_new'];

			// what do we do for a new count (easy!)
			$insert_this_result_SQL = "INSERT INTO `" . $update_table . "`(`ID`, `IQC_report_ID`, `crit_dim_ID`, `test_result`, `date_entered`, `created_by`, `record_status`) VALUES (NULL,'" . $IQC_report_ID . "','" . $crit_dim_ID . "','" . $result_to_write . "','" . $date_entered . "','" . $created_by . "','2');";
			if (mysqli_query($con, $insert_this_result_SQL)) {

				$record_id = mysqli_insert_id($con);

				// AWESOME! We added the record
				$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
				// echo $record_edit_SQL;

				if (mysqli_query($con, $record_edit_SQL)) {
					// AWESOME! We added the change record to the database

					header("Location: IQC_report_view.php?msg=OK&action=add_results&id=".$IQC_report_ID."");
					exit();

				}
				else {
					echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
				}
				
			}
			

}
else {

			// now cycle through the test results:
			$this_result = 1;
			$num_results_needed = $_REQUEST['num_results_needed'];
			$redirect = 'NG';
			$results_entered = 0;
			$ignore_results = 0;

			// debug:
			// echo "<h2>Num Results Needed First: " . $num_results_needed . "</h2>";

			while ($this_result <= $num_results_needed) {

				$result_to_write = $_REQUEST['IQC_result_' . $this_result . ''];
				// echo "<h3>RESULT #" . $this_result . " IS: " . $result_to_write . "</h3>";

				if ($result_to_write == '') {
					// debug:
					// echo '<h3>BLANK ENTRY - add to ignore results count</h3>';
					// blank entry - let's ignore it!
					$ignore_results = $ignore_results + 1;
				}
				else {
					// result is not blank, let's add it:

					// now run the results:
					$insert_this_result_SQL = "INSERT INTO `" . $update_table . "`(`ID`, `IQC_report_ID`, `crit_dim_ID`, `test_result`, `date_entered`, `created_by`, `record_status`) VALUES (NULL,'" . $IQC_report_ID . "','" . $crit_dim_ID . "','" . $result_to_write . "','" . $date_entered . "','" . $created_by . "','2');";
					// debug:
					// echo "<h3>SQL is: " . $insert_this_result_SQL . "</h3>";
		
					if (mysqli_query($con, $insert_this_result_SQL)) {
			
						$record_id = mysqli_insert_id($con);
			
						// AWESOME! We added the record
						$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
						// echo $record_edit_SQL;

						if (mysqli_query($con, $record_edit_SQL)) {
							// AWESOME! We added the change record to the database
			
							$results_entered = $results_entered + 1;
							$redirect = 'OK';

						}
						else {
							echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
							$redirect = 'NG';
						}
		
					}
					else {
						echo "<h4>Failed to add new record with SQL: <br />" . $insert_this_result_SQL . "</h4>";
						$redirect = 'NG';
					}
		
				} // end found result
	
				// append the results count:
				$this_result = $this_result + 1;
			}


			if ( ( $redirect == 'OK' ) && ( ( $results_entered + $ignore_results ) == $num_results_needed ) ) {
			// OK to send them on now...
				header("Location: IQC_report_view.php?msg=OK&action=add_results&id=".$IQC_report_ID."");
				exit();
			}
			else {
				echo '<h3>ERROR: Redirect NG or Results Not Enough (' . $num_results_needed . ' needed / ' . $ignore_results . ' ignored / ' . $results_entered . ' entered)</h3>';
			}

}


// debug:
// echo "<h2>Num Results Needed Last: " . $num_results_needed . "</h2>";
// echo "<h2>Ignore Results: " . $ignore_results . "</h2>";
?>
