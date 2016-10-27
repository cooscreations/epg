<?php 
// This page is designed to enable the (clunky) cancel / back form_button function from part_revision_edit.php?id=xx, as it was sending the use to the wrong page

$part_ID = $_REQUEST['part_id'];
$rev_ID = $_REQUEST['id'];

// NOW REDIRECT THEM!
header("Location: part_view.php?id=".$part_ID."&rev_id=" . $rev_ID . "");
exit();

?>