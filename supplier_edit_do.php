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
$sup_id = $_REQUEST['sup_id'];
$sup_en = $_REQUEST['name_en'];
$sup_cn = $_REQUEST['name_cn'];
$sup_web = $_REQUEST['sup_website'];

$update_note = "Editing a supplier in the system.";

$edit_supplier_SQL = "UPDATE `suppliers` SET `name_EN` = '".$sup_en."', `name_CN` = '".$sup_cn."', `website` = '".$sup_web."' WHERE `ID` = '".$sup_id."' ";


// echo $edit_supplier_SQL;

if (mysqli_query($con, $edit_supplier_SQL)) {
	
		// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'suppliers','" . $sup_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add - send them to the revisions list for that part	
				header("Location: suppliers.php?msg=OK&action=edit");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing supplier with SQL: <br />" . $edit_supplier_SQL . "</h4>";
}

?>