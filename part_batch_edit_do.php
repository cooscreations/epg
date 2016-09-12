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
$part_batch_id = $_REQUEST['part_batch_id'];
$PO_ID = $_REQUEST['PO_ID'];
$part_rev_ID = $_REQUEST['part_rev_ID']; // use this to find the part #
$batch_number = $_REQUEST['batch_number'];
$user_ID = $_REQUEST['user_ID'];
$date_added = $_REQUEST['date_added'] . " 00:00:00";
$sup_ID = $_REQUEST['sup_ID'];
$record_status = $_REQUEST['record_status'];

$update_note = "Updating a part batch in the system.";

$edit_part_batch_SQL = "UPDATE `part_batch` SET `PO_ID` = '".$PO_ID."', `part_rev` = '".$part_rev_ID."', `batch_number` = '".$batch_number."', `supplier_ID` = '".$sup_ID."', `record_status` = '" . $record_status . "' WHERE `ID` = '".$part_batch_id."' ";

if (mysqli_query($con, $edit_part_batch_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_batch','" . $part_batch_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";

		if (mysqli_query($con, $record_edit_SQL)) {

				header("Location: batch_log.php?msg=OK&action=edit");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing part batch with SQL: <br />" . $edit_part_batch_SQL . "</h4>";
}

?>
