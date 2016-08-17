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
$id = $_REQUEST['id'];
$table_name = $_REQUEST['table_name'];
$src_page = $_REQUEST['src_page'];
$update_note = "Marking record as deleted in the system.";

$delete_SQL = "UPDATE `".$table_name."` set `record_status`=0 WHERE `ID` = '".$id."' ";

echo $delete_SQL;

if (mysqli_query($con, $delete_SQL)) {


		// AWESOME! We added the record
    $record_delete_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'".$table_name."','" . $id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'DELETE')";
    // echo $record_delete_SQL;

		if (mysqli_query($con, $record_delete_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add - send them to the revisions list for that part
            header("Location: ".$src_page."?msg=OK&action=delete");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_delete_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to delete existing record with SQL: <br />" . $delete_SQL . "</h4>";
}

?>
