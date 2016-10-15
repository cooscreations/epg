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


$record_id 						= $_REQUEST['id'];
$sup_epg_supplier_ID 			= checkaddslashes($_REQUEST['epg_supplier_ID']);
$sup_en 						= checkaddslashes($_REQUEST['name_en']);
if($_REQUEST['name_cn'] == ''){
	$sup_cn 					= '中文名';
}else{
	$sup_cn				 		= checkaddslashes($_REQUEST['name_cn']);
}
$sup_web 						= checkaddslashes($_REQUEST['sup_website']);
$sup_supplier_status 			= checkaddslashes($_REQUEST['supplier_status']);
$sup_part_classification 		= checkaddslashes($_REQUEST['part_classification']);
$sup_items_supplied 			= checkaddslashes($_REQUEST['items_supplied']);
$sup_part_type_ID 				= checkaddslashes($_REQUEST['part_type_ID']);
$sup_certifications 			= checkaddslashes($_REQUEST['certifications']);
$sup_certification_expiry_date 	= check_date_time($_REQUEST['certification_expiry_date']);
$sup_evaluation_date 			= check_date_time($_REQUEST['evaluation_date']);
$sup_address_EN 				= checkaddslashes($_REQUEST['address_EN']);
$sup_address_CN 				= checkaddslashes($_REQUEST['address_CN']);
$sup_country_ID 				= checkaddslashes($_REQUEST['country_ID']);
$sup_contact_person 			= checkaddslashes($_REQUEST['contact_person']);
$sup_mobile_phone 				= checkaddslashes($_REQUEST['mobile_phone']);
$sup_telephone 					= checkaddslashes($_REQUEST['telephone']);
$sup_fax 						= checkaddslashes($_REQUEST['fax']);
$sup_email_1 					= checkaddslashes($_REQUEST['email_1']);
$sup_email_2 					= checkaddslashes($_REQUEST['email_2']);
$record_status					= checkaddslashes($_REQUEST['record_status']);
$sup_controlled					= checkaddslashes($_REQUEST['controlled']);

$update_note = "Editing a supplier in the system.";
$update_table = 'suppliers';

// $edit_supplier_SQL = "UPDATE `" . $update_table . "` SET `name_EN` = '".$sup_en."', `name_CN` = '".$sup_cn."', `website` = '".$sup_web."' WHERE `ID` = '".$sup_id."' ";

$edit_record_SQL = "UPDATE `" . $update_table . "` SET 
`epg_supplier_ID`='" . $sup_epg_supplier_ID . "',
`name_EN`='" . $sup_en . "',
`name_CN`='" . $sup_cn . "',
`website`='" . $sup_web . "',
`supplier_status`='" . $sup_supplier_status . "',
`part_classification`='" . $sup_part_classification . "',
`items_supplied`='" . $sup_items_supplied . "',
`part_type_ID`='" . $sup_part_type_ID . "',
`certifications`='" . $sup_certifications . "',
`certification_expiry_date`='" . $sup_certification_expiry_date . "',
`evaluation_date`='" . $sup_evaluation_date . "',
`address_EN`='" . $sup_address_EN . "',
`address_CN`='" . $sup_address_CN . "',
`country_ID`='" . $sup_country_ID . "',
`contact_person`='" . $sup_contact_person . "',
`mobile_phone`='" . $sup_mobile_phone . "',
`telephone`='" . $sup_telephone . "',
`fax`='" . $sup_fax . "',
`email_1`='" . $sup_email_1 . "',
`email_2`='" . $sup_email_2 . "',
`record_status`='" . $record_status . "',
`controlled`='" . $sup_controlled . "' 
WHERE `ID` = '" . $record_id . "'";

// echo $edit_supplier_SQL;

if (mysqli_query($con, $edit_record_SQL)) {

		// AWESOME! We added the record
    $record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'" . $update_table . "','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

			// regular add - send them to the revisions list for that part
			header("Location: supplier_view.php?msg=OK&action=edit&id=" . $record_id . "");
			exit();

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing record with SQL: <br />" . $edit_record_SQL . "</h4>";
}

?>
