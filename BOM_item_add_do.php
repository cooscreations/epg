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

$update_table = "product_BOM_items";

$product_BOM_ID 	= checkaddslashes($_REQUEST['product_BOM_ID']);
$part_rev_ID 		= checkaddslashes($_REQUEST['part_rev_ID']);
$parent_ID			= checkaddslashes($_REQUEST['parent_ID']);
$entry_order		= checkaddslashes($_REQUEST['entry_order']);
$usage_qty			= checkaddslashes($_REQUEST['usage_qty']);

if ($parent_ID == '') { $parent_ID = 0; }

$update_note = "Adding a new BOM Item to the system.";

$add_SQL = "INSERT INTO `" . $update_table . "`(`ID`, `product_BOM_ID`, `part_rev_ID`, `parent_ID`, `created_by`, `date_entered`, `record_status`, `entry_order`, `usage_qty`) VALUES (NULL,'" . $product_BOM_ID . "','" . $part_rev_ID . "','" . $parent_ID . "','" . $_SESSION['user_ID'] . "','" . date("Y-m-d H:i:s") . "','2','" . $entry_order . "','" . $usage_qty . "')";

// echo $add_SQL;

if (mysqli_query($con, $add_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

	// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
	// echo $record_edit_SQL;

	if (mysqli_query($con, $record_edit_SQL)) {
		// AWESOME! We added the change record to the database

		// regular add
		header("Location: BOM_view.php?msg=OK&action=add_BOM_item&id=".$product_BOM_ID."");
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
