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

$PO_ID = $_REQUEST['PO_ID'];
$part_rev_ID = $_REQUEST['part_rev_ID']; // use this to find the part #
$batch_number = $_REQUEST['batch_number'];
$user_ID = $_REQUEST['user_ID'];
$date_added = $_REQUEST['date_added'] . " 00:00:00";

// get part revision info:
$get_part_rev_SQL = "SELECT * FROM  `part_revisions` WHERE  `ID` =" . $part_rev_ID;
$result_get_part_rev = mysqli_query($con,$get_part_rev_SQL);
// while loop
while($row_get_part_rev = mysqli_fetch_array($result_get_part_rev)) {
	
	// now print each record:  
	$rev_id = $row_get_part_rev['ID'];
	$rev_part_id = $row_get_part_rev['part_ID'];
	$rev_number = $row_get_part_rev['revision_number'];
	$rev_remarks = $row_get_part_rev['remarks'];
	$rev_date = $row_get_part_rev['date_approved'];
	$rev_user = $row_get_part_rev['user_ID'];
															
}

$update_note = "Adding a new batch to the system.";

$add_batch_SQL = "INSERT INTO `part_batch`(`ID`, `PO_ID`, `part_ID`, `batch_number`, `part_rev`) VALUES (NULL,'".$PO_ID."','".$rev_part_id."','".$batch_number."','".$part_rev_ID."')";

// echo $add_movement_SQL;

if (mysqli_query($con, $add_batch_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "INSERT # " . $record_id . " OK";
	
		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_batch','" . $record_id . "','1','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
			header("Location: purchase_order_view.php?id=".$PO_ID."&msg=OK&action=add&new_record_id=".$record_id."");
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_batch_SQL . "</h4>";
}

?>