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

$ICQ_result_new 				= checkaddslashes($_REQUEST['ICQ_result_new']);
$IQC_report_result_ID 			= checkaddslashes($_REQUEST['IQC_report_result_ID']);
$IQC_report_result_old 			= checkaddslashes($_REQUEST['IQC_report_result_old']);
$IQC_report_result_old_OK_NG 	= checkaddslashes($_REQUEST['IQC_report_result_old_OK_NG']);
$id 							= checkaddslashes($_REQUEST['IQC_report_ID']);

if ($_REQUEST['method_class_ID'] == 2) {
	$update_note = "Changing IQR result # " . $IQC_report_result_ID . " from " . $IQC_report_result_old;
}
else {
	$update_note = "Changing IQR result # " . $IQC_report_result_ID . " from " . $IQC_report_result_old . " (RESULT: " . $IQC_report_result_old_OK_NG . ") to " . $ICQ_result_new . "";
}

$update_table = 'IQC_report_results';

// $edit_SQL = "UPDATE `countries` SET `name_EN` = '".$name_en."', `name_CN` = '".$name_cn."', `code` = '".$code."' WHERE `ID` = '".$id."' ";

$edit_SQL = "UPDATE `" . $update_table . "` SET `test_result`='" . $ICQ_result_new . "',`date_entered`='" . date("Y-m-d H:i:s") . "',`created_by`='" . $_SESSION['user_ID'] . "' WHERE `ID` = '" . $IQC_report_result_ID . "'";

// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: IQC_report_view.php?msg=OK&action=edit&id=" . $id . "#result_" . $IQC_report_result_ID);

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
