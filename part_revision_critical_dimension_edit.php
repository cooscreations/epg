<?php 

// WOOHOO! THIS IS BEING HANDLED BY THE ADD FORM! YEAH!

if (isset($_REQUEST['id'])) {

			$add_URL = '';

		if (isset($_REQUEST['part_id'])) {
			$add_URL .= '&part_id=' . $_REQUEST['part_id'] . '';
		}

		if (isset($_REQUEST['part_rev_id'])) {
			$add_URL .= '&part_rev_id=' . $_REQUEST['part_rev_id'] . '';
		}

		if (isset($_REQUEST['inspection_method_ID'])) {
			$add_URL .= '&inspection_method_ID=' . $_REQUEST['inspection_method_ID'] . '';
		}

		header("Location: part_revision_critical_dimension_add.php?id=" . $_REQUEST['id'] . $add_URL . "");
		exit();
		
}
else {

		header("Location: part_revision_critical_dimensions.php?msg=NG&action=edit&error=no_id");
		exit();
		
}

?>