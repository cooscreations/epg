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

/* -- NO USER SESSIONS YET...
if (isset($_SESSION['user_id'])) {
	header("Location: user_home.php"); // send them to the user home...
}
*/

/* 

THIS IS AN INVISIBLE PAGE THAT CHECKS / VALIDATES THE FORM DATA, ENTERS IT IN TO THE DATABASE AND THEN REDIRECTS TO SOMEWHERE ELSE

*/
$user_id = $_REQUEST['id'];

$update_note = "Deleting a user order to the system.";

$delete_user_SQL = "DELETE FROM `users` WHERE `ID` = '".$user_id."' ";


// echo $delete_user_SQL;

if (mysqli_query($con, $delete_user_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "DELETE # " . $record_id . " OK";
	
		// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'users','" . $user_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'DELETE')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add - send them to the revisions list for that part	
            header("Location: users.php?msg=DELETEOK&action=delete&deleted_record_id=".$user_id."");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $delete_user_SQL . "</h4>";
}

?>