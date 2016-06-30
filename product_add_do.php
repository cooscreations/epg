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

$name_en = $_REQUEST['name_en'];
$name_cn = $_REQUEST['name_cn'];
$description = $_REQUEST['description'];
$product_type_ID = $_REQUEST['product_type_ID'];

$update_note = "Adding a new product to the system.";

$add_product_SQL = "INSERT INTO `products`(`ID`, `name_EN`, `name_CN`, `description`, `product_type_ID`) VALUES (NULL,'".$name_en."','".$name_cn."','".$description."','".$product_type_ID."')";


if (mysqli_query($con, $add_product_SQL)) {
	
	$record_id = mysqli_insert_id($con);
	
	// echo "INSERT # " . $record_id . " OK";
	
		// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'products','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;
		
		if (mysqli_query($con, $record_edit_SQL)) {	
			// AWESOME! We added the change record to the database
				
				// regular add - send them to the revisions list for that part	
				header("Location: products.php?msg=OK&action=add&new_record_id=".$record_id."");
			
			exit();
			
		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}
		
}
else {
	echo "<h4>Failed to add new product with SQL: <br />" . $add_product_SQL . "</h4>";
}

?>