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

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/

$IQC_report_num 	= checkaddslashes($_REQUEST['IQC_report_num']);
$batch_ID 			= checkaddslashes($_REQUEST['batch_ID']);
$remarks 			= checkaddslashes($_REQUEST['remarks']);
$NCR_num 			= checkaddslashes($_REQUEST['NCR_num']);
$inspected_by 		= checkaddslashes($_REQUEST['inspected_by']);		
$date_inspected 	= check_date_time($_REQUEST['date_inspected']);	
$reviewed_by 		= checkaddslashes($_REQUEST['reviewed_by']);
if ($reviewed_by == '') {
	$reviewed_by = 0;
}
$date_reviewed 		= check_date_time($_REQUEST['date_reviewed']);
if ($date_reviewed == '') {
	$date_reviewed = '0000-00-00 00:00:00';
}
$test_result 		= '2'; // TO BE CONFIRMED 

$add_table = 'IQC_report';
$update_note = "Adding a new IQC report to the system.";

// echo 'OK to proceed...';

$add_SQL = "INSERT INTO `" . $add_table . "`(`ID`, `IQC_report_num`, `batch_ID`, `remarks`, `test_result`, `NCR_num`, `reviewer_ID`, `review_date`, `inspector_ID`, `inspection_date`, `record_status`) VALUES (NULL,'" . $IQC_report_num . "','" . $batch_ID . "','" . $remarks . "','" . $test_result . "','" . $NCR_num . "','" . $reviewed_by . "','" . $date_reviewed . "','" . $inspected_by . "','" . $date_inspected . "','2')";

// echo $add_SQL;

if (mysqli_query($con, $add_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

	// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $add_table . "','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
	// echo $record_edit_SQL;

	if (mysqli_query($con, $record_edit_SQL)) {
		// AWESOME! We added the change record to the database

			// regular add
		header("Location: IQC_report_view.php?msg=OK&action=add&id=".$record_id."");

		exit();

	}
	else {
		echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
	}

}
else {
	echo "<h4>Failed to add new record with SQL: <br />" . $add_SQL . "</h4>";
}

?>
