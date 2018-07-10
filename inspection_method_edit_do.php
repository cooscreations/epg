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
$id 			= checkaddslashes($_REQUEST['id']);
$name_en 		= checkaddslashes($_REQUEST['name_en']);
$name_cn 		= checkaddslashes($_REQUEST['name_cn']);
$description 	= checkaddslashes($_REQUEST['description']);
$record_status 	= checkaddslashes($_REQUEST['record_status']);

$update_note = "Editing an Inspection Method in the system.";

$edit_SQL = "UPDATE `inspection_method` SET `name_EN` = '".$name_en."', `name_CN` = '".$name_cn."', `description` = '".$description."', `record_status` = '".$record_status."' WHERE `ID` = '".$id."' ";


// echo $edit_SQL;

if (mysqli_query($con, $edit_SQL)) {

    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'inspection_method','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add
            header("Location: inspection_methods.php?msg=OK&action=edit&method_id=" . $id . "");

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
