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


$part_batch_movement_ID 	= checkaddslashes($_REQUEST['part_batch_movement_ID']);
$batch_ID 					= checkaddslashes($_REQUEST['batch_ID']);
$value_direction 			= checkaddslashes($_REQUEST['value_direction']);
$status_ID 					= checkaddslashes($_REQUEST['status_ID']);
$amount 					= checkaddslashes($_REQUEST['amount']);
$remarks 					= checkaddslashes($_REQUEST['remarks']);
$created_by 				= checkaddslashes($_REQUEST['created_by']);
$date_added 				= check_date_time($_REQUEST['date_added']);
$record_status 				= checkaddslashes($_REQUEST['record_status']);
$PO_ID 						= checkaddslashes($_REQUEST['PO_ID']);
$next_step 					= checkaddslashes($_REQUEST['next_step']);

// establish if it's coming in or going out...
if ($_REQUEST['value_direction'] == 'in') {
	$amount_in 		= $_REQUEST['amount'];
	$amount_out 	= 0;
	$update_note 	= "Adding parts to the system";
	$status_ID 		= $_REQUEST['status_ID'];
}
else if ($_REQUEST['value_direction'] == 'out') {
	$amount_in 		= 0;
	$amount_out 	= $_REQUEST['amount'];
	$update_note 	= "Removing parts from the system";
	// get previous status_ID:
	$get_existing_batch_SQL = "SELECT * FROM `part_batch_movement` WHERE `part_batch_ID` = " . $_REQUEST['batch_ID'];
	$result_get_existing_batch = mysqli_query($con,$get_existing_batch_SQL);
	// while loop
	while($row_get_existing_batch = mysqli_fetch_array($result_get_existing_batch)) {
		// now print each record:
		$status_ID = $row_get_existing_batch['part_batch_status_ID'];
	}
}


// $add_movement_SQL = "INSERT INTO  `part_batch_mpvement` (`ID`, `name_EN`, `name_CN`, `created_by`, `status`, `date_entered`, `staff_grade`, `description`, `reports_to`, `approved_by`, `reporting`, `duties`, `other_functions`, `qualifications`, `required`, `preferred`, `licenses`, `physical`, `dept_ID`, `client_ID`, `vendor_ID`) VALUES (NULL ,  '".realStrip($_POST['name_EN'])."',  '".realStrip($_POST['name_CN'])."',  '" . $_SESSION['user_id'] . "',  '".realStrip($_POST['status'])."',  '" . date("Y-m-d H:i:s") . "',  '".realStrip($_POST['staff_grade_ID'])."',  '".realStrip($_POST['summary'])."',  '".realStrip($_POST['reports_to_JD_id'])."',  '".realStrip($_POST['approved_by'])."',  '".realStrip($_POST['reporting'])."',  '".realStrip($_POST['duties'])."',  '".realStrip($_POST['other_functions'])."',  '".realStrip($_POST['qualifications'])."',  '".realStrip($_POST['required'])."',  '".realStrip($_POST['preferred'])."',  '".realStrip($_POST['licenses'])."',  '".realStrip($_POST['physical'])."',  '".realStrip($_POST['dept_ID'])."',  '0', '112');";


$add_movement_SQL = "INSERT INTO `part_batch_movement`(`ID`, `part_batch_ID`, `amount_in`, `amount_out`, `part_batch_status_ID`, `remarks`, `user_ID`, `date`) VALUES (NULL,'". $batch_ID ."','" . $amount_in . "','" . $amount_out . "','" . $status_ID . "','" . $remarks . "','" . $created_by . "','" . $date_added . "')";

// echo $add_movement_SQL;

if (mysqli_query($con, $add_movement_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'part_batch_movement','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database
			
			// NOW WORK OUT WHERE TO SEND THEM:
			
			if ($_REQUEST['next_step'] == 'view') {
				header("Location: batch_view.php?id=". $batch_ID ."&msg=OK&action=add&new_record_id=". $record_id ."&next_step=view");
				exit();
			}
			else {
				// ADD AGAIN!
				header("Location: part_movement_add.php?batch_id=". $batch_ID ."&msg=OK&action=add&new_record_id=". $record_id ."&next_step=add");
				exit();
			}

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $add_movement_SQL . "</h4>";
}

?>
