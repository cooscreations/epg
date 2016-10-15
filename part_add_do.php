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


// First, let's capture the form data:

$name_EN 				= checkaddslashes($_REQUEST['name_EN']);
$name_CN 				= checkaddslashes($_REQUEST['name_CN']);
$part_code 				= checkaddslashes($_REQUEST['part_code']);
$part_desc 				= checkaddslashes($_REQUEST['part_desc']);
$part_type_ID 			= checkaddslashes($_REQUEST['part_type_ID']);
$classificiation_ID 	= checkaddslashes($_REQUEST['classificiation_ID']);
$product_type_ID 		= checkaddslashes($_REQUEST['product_type_ID']);
$is_finished_product 	= checkaddslashes($_REQUEST['is_finished_product']);
if ($is_finished_product == '') { $is_finished_product = 0; }
$sup_ID 				= checkaddslashes($_REQUEST['sup_ID']);
$created_by 			= checkaddslashes($_REQUEST['created_by']);
$record_status 			= checkaddslashes($_REQUEST['record_status']);

$first_rev_number		= checkaddslashes($_REQUEST['part_rev_number']);

// VALIDATION

$validation_result = 0;

// 1. CHECK PART NUMBER DOESN'T ALREADY EXIST:
$count_parts_sql 	= "SELECT COUNT( ID ) FROM  `parts` WHERE `record_status` = '2' AND `part_code` = " . $part_code;
echo $count_parts_sql;
$count_parts_query 	= mysqli_query($con, $count_parts_sql);
$count_parts_row 	= mysqli_fetch_row($count_parts_query);
$total_rows 		= $count_parts_row[0];

$add_error_VARS = '';

if ($total_rows != 0) {

	// let's get the part ID:
	
	$get_existing_part_ID_SQL = "SELECT * FROM `parts` WHERE `part_code` = '".$part_code."'";
	$result_get_existing_part_ID = mysqli_query($con,$get_existing_part_ID_SQL);
	// while loop
	while($row_get_existing_part_ID = mysqli_fetch_array($result_get_existing_part_ID)) {

		// NOW WRITE THE DATA:
		$existing_part_ID 			= $row_get_existing_part_ID['ID'];
	}


	// PART FOUND!!!
	// echo 'OH NO, PART FOUND!';
	$add_error_VARS .= '&error=duplicate&field=part_number&name_EN='.$name_EN.
	'&name_CN='.$name_CN.
	'&part_code='.$part_code.
	'&part_desc='.$part_desc.
	'&part_type_ID='.$part_type_ID.
	'&classification_ID='.$classificiation_ID.
	'&product_type_ID='.$product_type_ID.
	'&is_finished_product='.$is_finished_product.
	'&sup_ID='.$sup_ID.
	'&created_by='.$created_by.
	'&record_status='.$record_status.
	'&existing_part_ID='.$existing_part_ID.'';
}
else if ($part_type_ID == 0) {
	echo 'OH NO, EMPTY DATA!';
}
else {
	$validation_result = 1;
}

///////////////////////////////////////////////////////////////////////////////////////////////

if ($validation_result == 0) { 
	// VALIDATION FAILED!
	header("Location: part_add.php?msg=NG" . $add_error_VARS);
	exit();
}
else {	// VALIDATION PASSED

	$update_note = "Adding a new Part to the system.";

	$add_SQL = "INSERT INTO `parts`(`ID`, `part_code`, `name_EN`, `name_CN`, `description`, `type_ID`, `classification_ID`, `default_suppler_ID`, `record_status`, `product_type_ID`, `created_by`, `is_finished_product`) VALUES (NULL,'" . 
	$part_code . "','" . 
	$name_EN . "','" . 
	$name_CN . "','" . 
	$part_desc . "','" . 
	$part_type_ID . "','" . 
	$classificiation_ID . "','" . 
	$sup_ID . "','" . 
	$record_status . "','" . 
	$product_type_ID . "','" . 
	$created_by . "','" . 
	$is_finished_product . "')";

	echo $add_SQL;


	if (mysqli_query($con, $add_SQL)) {

		$record_id = mysqli_insert_id($con);

		echo "INSERT # " . $record_id . " OK<br /><br />";

			// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'parts','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
			// echo $record_edit_SQL;

			if (mysqli_query($con, $record_edit_SQL)) {
				// AWESOME! We added the change record to the database

					// regular add
				header("Location: part_view.php?msg=OK&action=add&id=".$record_id."&first_rev_number=" . $first_rev_number);

				exit();

			}
			else {
				echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
			}

	}
	else {
		echo "<h4>Failed to add new part with SQL: <br />" . $add_SQL . "</h4>";
	}
}

?>
