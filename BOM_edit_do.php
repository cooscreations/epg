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
$id 				= checkaddslashes($_REQUEST['id']);
$BOM_part_rev_ID 	= checkaddslashes($_REQUEST['part_rev_ID']);
$BOM_date_created 	= check_date_time($_REQUEST['date_created']);
$BOM_record_status 	= checkaddslashes($_REQUEST['record_status']);
$BOM_created_by 	= checkaddslashes($_REQUEST['created_by']);
$BOM_type 			= checkaddslashes($_REQUEST['BOM_type']);
$BOM_parent_BOM_ID 	= checkaddslashes($_REQUEST['parent_BOM_ID']);
$BOM_entry_order 	= checkaddslashes($_REQUEST['entry_order']);

$update_table = 'product_BOM';

$update_note = "Editing a BOM record in the system.";

$edit_SQL = "UPDATE `product_BOM` SET`part_rev_ID`='" . $BOM_part_rev_ID . "',`date_entered`='" . $BOM_date_created . "',`record_status`='" . $BOM_record_status . "',`created_by`='" . $BOM_created_by . "',`BOM_type`='" . $BOM_type . "',`parent_BOM_ID`='" . $BOM_parent_BOM_ID . "',`entry_order`='" . $BOM_entry_order . "' WHERE `ID` = '" . $id . "'";

// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: BOM_view.php?msg=OK&action=edit&id=" . $id . "");

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
