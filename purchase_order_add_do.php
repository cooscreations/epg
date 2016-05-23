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

$po_number = $_REQUEST['po_number'];
$user_ID = $_REQUEST['user_ID'];
$date_added = $_REQUEST['date_added'] . " 00:00:00";
$description = $_REQUEST['description'];

$update_note = "Adding a new purchase order to the system.";

$add_purchaseorder_SQL = "INSERT INTO `purchase_orders`(`ID`, `PO_number`, `created_date`, `description`) VALUES (NULL,'".$po_number."','".$date_added."','".$description."')";


// echo $add_movement_SQL;

if (mysqli_query($con, $add_purchaseorder_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "INSERT # " . $record_id . " OK";
	
		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'purchase_orders','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add - send them to the revisions list for that part	
				header("Location: purchase_orders.php?po_number=".$po_number."&msg=OK&action=add&new_record_id=".$record_id."");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_purchaseorder_SQL . "</h4>";
}

?>