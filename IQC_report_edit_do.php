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

if (!isset($_REQUEST['id'])) {
	header("Location: IQC_reports.php?msg=NG&action=view&error=no_id"); // send them to the IQC report page.
	exit();
}

/*

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/

$IQC_report_num 	= checkaddslashes($_REQUEST['IQC_report_num']);
$batch_ID 			= checkaddslashes($_REQUEST['batch_ID']);
$remarks 			= checkaddslashes($_REQUEST['remarks']);
$test_result 		= checkaddslashes($_REQUEST['test_result']); // TO BE CONFIRMED 
$NCR_num 			= checkaddslashes($_REQUEST['NCR_num']);
$reviewed_by 		= checkaddslashes($_REQUEST['reviewed_by']);
if ($reviewed_by == '') {
	$reviewed_by = 0;
}
$date_reviewed 		= check_date_time($_REQUEST['date_reviewed']);
if ($date_reviewed == '') {
	$date_reviewed = '0000-00-00 00:00:00';
}
$inspected_by 		= checkaddslashes($_REQUEST['inspected_by']);
if ($inspected_by == '') {
	$inspected_by = 0;
}		
$date_inspected 	= check_date_time($_REQUEST['date_inspected']);	
if ($date_inspected == '') {
	$date_inspected = '0000-00-00 00:00:00';
}
$record_status 		= checkaddslashes($_REQUEST['record_status']);
$id 				= checkaddslashes($_REQUEST['id']);

$update_note = "Editing an IQC report in the system.";

$update_table = 'IQC_report';

$edit_SQL = "UPDATE `" . $update_table . "` SET `IQC_report_num`='" . $IQC_report_num . "', `batch_ID`='" . $batch_ID . "', `remarks`='" . $remarks . "', `test_result`='" . $test_result  . "', `NCR_num`='" . $NCR_num . "', `reviewer_ID`='" . $reviewed_by . "', `review_date`='" . $date_reviewed . "', `inspector_ID`='" . $inspected_by . "', `inspection_date`='" . $date_inspected . "', `record_status`='" . $record_status . "' WHERE `ID` = '".$id."'";

// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: IQC_report_view.php?msg=OK&action=edit&id=" . $id . "");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing record with SQL: <br />" . $edit_SQL . "</h4>";
}

?>
