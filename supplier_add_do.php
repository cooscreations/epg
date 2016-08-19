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
$sup_epg_supplier_ID = $_REQUEST['epg_supplier_ID'];
$sup_en = $_REQUEST['name_en'];
if($_REQUEST['name_cn'] == ''){
	$sup_cn = '中文名';
}else{
	$sup_cn = $_REQUEST['name_cn'];
}
$sup_web = $_REQUEST['sup_website'];
$sup_supplier_status = $_REQUEST['supplier_status'];
$sup_part_classification = $_REQUEST['part_classification'];
$sup_items_supplied = $_REQUEST['items_supplied'];
$sup_part_type_ID = $_REQUEST['part_type_ID'];
$sup_certifications = $_REQUEST['certifications'];
$sup_certification_expiry_date = $_REQUEST['certification_expiry_date'];
$sup_evaluation_date = $_REQUEST['evaluation_date'];
$sup_address_EN = $_REQUEST['address_EN'];
$sup_address_CN = $_REQUEST['address_CN'];
$sup_country_ID = $_REQUEST['country_ID'];
$sup_contact_person = $_REQUEST['contact_person'];
$sup_mobile_phone = $_REQUEST['mobile_phone'];
$sup_telephone = $_REQUEST['telephone'];
$sup_fax = $_REQUEST['fax'];
$sup_email_1 = $_REQUEST['email_1'];
$sup_email_2 = $_REQUEST['email_2'];


$update_note = "Adding a new supplier to the system.";

$add_supplier_SQL = "INSERT INTO `suppliers`(`ID`, `epg_supplier_ID`, `name_EN`, `name_CN`, `website`, `supplier_status`, `part_classification`, `items_supplied`, `part_type_ID`, `certifications`, `certification_expiry_date`, `evaluation_date`, `address_EN`, `address_CN`, `country_ID`, `contact_person`, `mobile_phone`, `telephone`, `fax`, `email_1`, `email_2`) VALUES (NULL,'".$sup_epg_supplier_ID."', '".$sup_en."','".$sup_cn."','".$sup_web."', '".$sup_supplier_status."', '".$sup_part_classification."', '".$sup_items_supplied."', '".$sup_part_type_ID."', '".$sup_certifications."', '".$sup_certification_expiry_date."', '".$sup_evaluation_date."', '".$sup_address_EN."', '".$sup_address_CN."', '".$sup_country_ID."', '".$sup_contact_person."', '".$sup_mobile_phone."', '".$sup_telephone."', '".$sup_fax."', '".$sup_email_1."', '".$sup_email_2."')";


// echo $add_supplier_SQL;

if (mysqli_query($con, $add_supplier_SQL)) {

	$record_id = mysqli_insert_id($con);

	// echo "INSERT # " . $record_id . " OK";

		// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'suppliers','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				// regular add - send them to the revisions list for that part
				header("Location: suppliers.php?msg=OK&action=add&new_record_id=".$record_id."");

			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to add ne supplier with SQL: <br />" . $add_supplier_SQL . "</h4>";
}

?>
