
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



// username and password sent from form
$username = $_REQUEST['username'];
$pwd = $_REQUEST['pwd'];


// To protect MySQL injection (more detail about MySQL injection)
$username = stripslashes($username);
$pwd = stripslashes($pwd);



//encrypted password
$pwd = md5($pwd);

$sql = "SELECT * FROM `users` WHERE email='$username' and password='$pwd'";

$result = mysqli_query ( $con, $sql );


$count = $result->num_rows;

if ($count == 1) {
	// update session.
	$_SESSION['username']= $username;
	
	//Update last login date in users table.
	$update_last_login_SQL = "UPDATE `users` SET `last_login_date` = SYSDATE() WHERE email='$username' ";
	
	if (mysqli_query($con, $update_last_login_SQL)) {
		header ( "Location: index.php" );
	}
	
} else {
	//Show error.
	header("Location: login.php?msg=NG&error=invalid_login");
}
?>