<?php

/* 
NOTES:

There are a few tables that refer to the DB name directly (primarily when looking up all tables), including:

1. search.php
2. upload-file.php


*/

// Create connection

// $dbhost = 'localhost:3036';
$dbhost = 'localhost';
$dbuser = 'cl11-epg';
$dbpass = '9tEt7K!s7';
$dbname = 'cl11-epg';

$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);

mysql_set_charset('UTF8');

// Check connection
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
  }
 else {
	// DEBUG - REMOVED THIS AS IT WAS CAUSING CLASHES WITH HEADER();
	// echo "<!-- DB Connection Successful. -->\n";	
	}
	
	/* change character set to utf8 */

if (!mysqli_set_charset($con, "utf8")) {
	// DEBUG - REMOVED THIS AS IT WAS CAUSING CLASHES WITH HEADER();
	// echo "<p> Error loading character set utf8: " . mysqli_error($con) . " </p>";
} else {
	// DEBUG - REMOVED THIS AS IT WAS CAUSING CLASHES WITH HEADER();
	// echo "<p> Current character set: " . mysqli_character_set_name($con) . " </p>";
}


?>