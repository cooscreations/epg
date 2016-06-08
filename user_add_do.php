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

$id = $_REQUEST['id'];
$user_fn = $_REQUEST['fn_text'];
$user_mn = $_REQUEST['mn_text'];
$user_ln = $_REQUEST['ln_text'];
$user_cn = $_REQUEST['cn_text'];
$user_email = $_REQUEST['email_text'];
$user_pwd = md5($_REQUEST['pwd_text']);
$user_level = $_REQUEST['level_text'];
$user_pos = $_REQUEST['pos_text'];

$update_note = "Adding a new user to the system.";

$add_user_SQL = "INSERT INTO `users`(`ID`, `first_name`, `middle_name`, `last_name`, `name_CN`, `email`, `password`, `user_level`, `position`) VALUES (NULL,'".$user_fn."','".$user_mn."','".$user_ln."','".$user_cn."','".$user_email."','".$user_pwd."','".$user_level."','".$user_pos."')";


// echo $add_movement_SQL;

if (mysqli_query($con, $add_user_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "INSERT # " . $record_id . " OK";
	
		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'users','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add
				header("Location: users.php?msg=OK&action=add&new_record_id=".$record_id."");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_user_SQL . "</h4>";
}



?>