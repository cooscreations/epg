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
	header("Location: login.php"); // send them to the Login page.
}

/* 

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/
$id = $_REQUEST['id'];

$update_note = "Deleting a part type record in the system.";

$delete_part_type_SQL = "DELETE FROM `part_type` WHERE `ID` = '".$id."' ";


if (mysqli_query($con, $delete_part_type_SQL)) {
	
	
    $record_delete_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_type','" . $id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'DELETE')";
		
		if (mysqli_query($con, $record_delete_SQL)) {	
				
            header("Location: part_types.php?msg=OK&action=delete");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_delete_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to delete existing part type record with SQL: <br />" . $delete_part_type_SQL . "</h4>";
}

?>