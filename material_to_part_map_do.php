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

NOTE: This is for the multi-dimensional mapping of multiple materials to multiple part revisions!!!

*/


if (!isset($_REQUEST['user_id'])){
	header("Location: users.php?msg=NG&action=view&error=no_id");
	exit();
}

$record_id 				= checkaddslashes($_REQUEST['user_id']);
$user_fn 				= checkaddslashes($_REQUEST['fn_text']);
$user_mn 				= checkaddslashes($_REQUEST['mn_text']);
$user_ln 				= checkaddslashes($_REQUEST['ln_text']);
$user_email 			= checkaddslashes($_REQUEST['email_text']);
$user_pwd 				= md5($_REQUEST['pwd_text']);
$user_existing_pwd		= $_REQUEST['existing_password'];
$user_level 			= checkaddslashes($_REQUEST['level_text']);
$user_pos 				= checkaddslashes($_REQUEST['pos_text']);
$user_mobile_number 	= checkaddslashes($_REQUEST['mobile_number']);
$user_facebook 			= checkaddslashes($_REQUEST['facebook']);
$user_linkedin 			= checkaddslashes($_REQUEST['linkedin']);
$user_twitter 			= checkaddslashes($_REQUEST['twitter']);
$user_wechat 			= checkaddslashes($_REQUEST['wechat']);
$user_skype 			= checkaddslashes($_REQUEST['skype']);
$user_record_status		= $_REQUEST['record_status'];

if($user_cn == ''){
	$user_cn 			= '中文名';
}
else{
	$user_cn 			= checkaddslashes($_REQUEST['cn_text']);
}

if (($user_pwd != $user_existing_pwd)&&($user_pwd != '')) {
	// WE ARE UPDATING THE PASSWORD!
	$password_to_DB = $user_pwd;
}
else {
	// WE ARE NOT UPDATING THE PASSWORD!
	$password_to_DB = $user_existing_pwd;
}

$update_note = "Editing a user to the system.";

$edit_record_SQL = "UPDATE `users` SET `first_name`='" . $user_fn . "',`middle_name`='" . $user_mn . "',`last_name`='" . $user_ln . "',`name_CN`='" . $user_cn . "',`email`='" . $user_email . "',`password`='" . $password_to_DB . "',`user_level`='" . $user_level . "',`position`='" . $user_pos . "',`facebook_profile`='" . $user_facebook . "',`twitter_profile`='" . $user_twitter . "',`linkedin_profile`='" . $user_linkedin . "',`skype_profile`='" . $user_skype . "',`wechat_profile`='" . $user_wechat . "',`record_status`='" . $user_record_status . "',`mobile_number`='" . $user_mobile_number . "' WHERE ID = '" . $record_id . "'";



// echo $edit_record_SQL;

if (mysqli_query($con, $edit_record_SQL)) {

	// echo "UPDATE # " . $record_id . " OK";

		// AWESOME! We added the record
		$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'users','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'UPDATE')";
		// echo $record_edit_SQL;

		if (mysqli_query($con, $record_edit_SQL)) {
			// AWESOME! We added the change record to the database

				if ($_REQUEST['next_step'] == 'view_record') {
					// regular add - send them to the updated record
					header("Location: user_view.php?msg=OK&action=edit&id=".$record_id."");
					exit();
				}
				else {
					// send them to the list of users
					header("Location: users.php?msg=UPDATEOK&action=edit&id=".$record_id."");
					exit();
				}

		}
		else {
			echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
		}

}
else {
	echo "<h4>Failed to update existing user with SQL: <br />" . $edit_record_SQL . "</h4>";
}
?>
