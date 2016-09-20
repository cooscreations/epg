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
$part_batch_movement_ID 	= checkaddslashes($_REQUEST['part_batch_movement_ID']);
$batch_ID 					= checkaddslashes($_REQUEST['batch_ID']);
$value_direction 			= checkaddslashes($_REQUEST['value_direction']);
$status_ID 					= checkaddslashes($_REQUEST['status_ID']);
$amount 					= checkaddslashes($_REQUEST['amount']);
$remarks 					= checkaddslashes($_REQUEST['remarks']);
$created_by 				= checkaddslashes($_REQUEST['created_by']);
$date_added 				= check_date_time($_REQUEST['date_added']);
$record_status 				= checkaddslashes($_REQUEST['record_status']);
$PO_ID 						= checkaddslashes($_REQUEST['PO_ID']);
$next_step 					= checkaddslashes($_REQUEST['next_step']);


// establish if it's coming in or going out...
if ($value_direction == 'in') {
	$amount_in = $amount;
	$amount_out = 0;
}
else if ($value_direction == 'out') {
	$amount_in = 0;
	$amount_out = $amount;
	// get previous status_ID:
	$get_existing_batch_SQL = "SELECT * FROM `part_batch_movement` WHERE `part_batch_ID` = " . $batch_ID;
	$result_get_existing_batch = mysqli_query($con,$get_existing_batch_SQL);
	// while loop
	while($row_get_existing_batch = mysqli_fetch_array($result_get_existing_batch)) {
		// now print each record:
		$status_ID = $row_get_existing_batch['part_batch_status_ID'];
	}
}

$update_note = "Editing a Part Batch Movement in the system.";

$edit_SQL = "UPDATE `part_batch_movement` SET `part_batch_ID`='" . $batch_ID . "',`amount_in`='" . $amount_in . "',`amount_out`='" . $amount_out . "',`part_batch_status_ID`='" . $status_ID . "',`remarks`='" . $remarks . "',`user_ID`='" . $created_by . "',`date`='" . $date_added . "',`record_status`='" . $record_status . "' WHERE `ID` = '" . $part_batch_movement_ID . "'";
// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_batch_movement','" . $part_batch_movement_ID . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: batch_view.php?id=" . $batch_ID . "&msg=OK&action=edit");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing country with SQL: <br />" . $edit_SQL . "</h4>";
}
?>
