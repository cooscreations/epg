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

$inspection_method_ID 			= checkaddslashes($_REQUEST['inspection_method_ID']);		
$part_rev_ID 					= checkaddslashes($_REQUEST['part_rev_ID']);
$drawing_QC_ID 					= checkaddslashes($_REQUEST['drawing_QC_ID']);			
$crit_dim_dimension_minimum 	= checkaddslashes($_REQUEST['crit_dim_dimension_minimum']);
$crit_dim_dimension_maximum 	= checkaddslashes($_REQUEST['crit_dim_dimension_maximum']);
$crit_dim_measurement_type		= checkaddslashes($_REQUEST['crit_dim_measurement_type']);
$crit_dim_specification_notes 	= checkaddslashes($_REQUEST['crit_dim_specification_notes']);
$crit_dim_inspection_level 		= checkaddslashes($_REQUEST['crit_dim_inspection_level']);
$crit_dim_AQL_level 			= checkaddslashes($_REQUEST['crit_dim_AQL_level']);
$crit_dim_remarks 				= checkaddslashes($_REQUEST['crit_dim_remarks']);
$record_status 					= checkaddslashes($_REQUEST['record_status']);
$form_action 					= checkaddslashes($_REQUEST['form_action']);
$next_step 						= checkaddslashes($_REQUEST['next_step']);


$table_name = "part_rev_critical_dimensions";
$update_note = "Adding a new Critical Dimension to the system.";

$add_SQL = "INSERT INTO `" . $table_name . "`(
`ID`, 
`part_revision_ID`, 
`drawing_QC_ID`, 
`dimension_type_ID`, 
`dimension_minimum`, 
`dimension_maximum`, 
`specification_notes`, 
`inspection_method_ID`, 
`inspection_level`, 
`AQL_level`, 
`record_status`, 
`remarks`) 
VALUES (NULL,
'" . $part_rev_ID . "',
'" . $drawing_QC_ID . "',
'" . $crit_dim_measurement_type . "',
'" . $crit_dim_dimension_minimum . "',
'" . $crit_dim_dimension_maximum . "',
'" . $crit_dim_specification_notes . "',
'" . $inspection_method_ID . "',
'" . $crit_dim_inspection_level . "',
'" . $crit_dim_AQL_level . "',
'" . $record_status . "',
'" . $crit_dim_remarks . "')";

// echo '<h1>SQL is: '.$add_SQL.'</h1>';

if (mysqli_query($con, $add_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

	// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $table_name . "','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
	// echo $record_edit_SQL;

	if (mysqli_query($con, $record_edit_SQL)) {
		// AWESOME! We added the change record to the database

			// regular add
		header("Location: part_revision_critical_dimensions.php?msg=OK&action=add&new_record_id=".$record_id."");
		exit();

	}
	else {
		echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
	}

}
else {
	echo "<h4>Failed to add new record with SQL: <br />" . $add_SQL . "</h4>";
}

?>