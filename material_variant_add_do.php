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

$material_ID = $_REQUEST['material_ID'];
$material_variant_type_ID = $_REQUEST['material_variant_type_ID']; 
$name_EN = $_REQUEST['name_EN'];
$name_CN = $_REQUEST['name_CN'];
$description = $_REQUEST['description'];
$color_code = $_REQUEST['color_code'];

$update_note = "Adding a new material variant to the system.";

$add_record_SQL = "INSERT INTO `material_variant`(`ID`, `material_ID`, `variant_type`, `name_EN`, `name_CN`, `description`, `code`) VALUES (NULL,'".$material_ID."','".$material_variant_type_ID."','".$name_EN."','".$name_CN."','".$description."','".$color_code."')";

// echo $add_movement_SQL;

if (mysqli_query($con, $add_record_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "INSERT # " . $record_id . " OK";
	
		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_batch','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
			header("Location: material_view.php?id=".$material_ID."&msg=OK&action=add&new_record_id=".$record_id."");
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to insert material variant with SQL: <br />" . $add_record_SQL . "</h4>";
}

?>