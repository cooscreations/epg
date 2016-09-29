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

$part_ID = $_REQUEST['part_ID'];
$rev_number = $_REQUEST['rev_number'];
$user_ID = $_REQUEST['user_ID'];
$date_added = $_REQUEST['date_added'] . " 00:00:00";
$remarks = $_REQUEST['remarks'];

$id 					= checkaddslashes($_REQUEST['id']);
$part_ID 				= checkaddslashes($_REQUEST['part_ID']);
$revision_number 		= checkaddslashes($_REQUEST['revision_number']);
$part_rev_material_ID 	= checkaddslashes($_REQUEST['part_rev_material_ID']);
$price_USD 				= checkaddslashes($_REQUEST['price_USD']);
$weight_g 				= checkaddslashes($_REQUEST['weight_g']);
$part_rev_status_ID 	= checkaddslashes($_REQUEST['part_rev_status_ID']);
$remarks 				= checkaddslashes($_REQUEST['remarks']);
$date_added 			= check_date_time($_REQUEST['date_added']);
$created_by 			= checkaddslashes($_REQUEST['created_by']);
$record_status 			= checkaddslashes($_REQUEST['record_status']);

$update_note = "Adding a new part revision to the system.";

// $add_revision_SQL = "INSERT INTO `part_revisions`(`ID`, `part_ID`, `revision_number`, `remarks`, `date_approved`, `user_ID`) VALUES (NULL,'".$part_ID."','".$rev_number."','".$remarks."','".$date_added."','".$user_ID."')";

$add_record_SQL = "INSERT INTO `part_revisions`(
`ID`, 
`part_ID`, 
`revision_number`, 
`remarks`, 
`date_approved`, 
`user_ID`, 
`price_USD`, 
`weight_g`, 
`status_ID`, 
`material_ID`, 
`treatment_ID`, 
`treatment_notes`, 
`record_status`) VALUES (NULL,'" 
. $part_ID . "','" 
. $revision_number . "','" 
. $remarks . "','" 
. $date_added . "','" 
. $created_by . "','" 
. $price_USD . "','" 
. $weight_g . "','" 
. $part_rev_status_ID . "','" 
. $part_rev_material_ID . "','0','No treatment notes at this stage','" 
. $record_status . "')";

// echo $add_movement_SQL;

if (mysqli_query($con, $add_record_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_revisions','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

			if (isset($_REQUEST['PO_ID'])) {
				// they were trying to create a new PO batch, let's send them to a more useful place! :)
				header("Location: part_batch_add.php?PO_ID=".$_REQUEST['PO_ID']."&part_id=".$part_ID."&msg=OK&action=add&new_record_id=".$record_id."");
			}
			else {
				// regular add - send them to the revisions list for that part
				header("Location: part_revisions.php?part_id=".$part_ID."&msg=OK&action=add&new_record_id=".$record_id."");
			}

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_revision_SQL . "</h4>";
}

?>
