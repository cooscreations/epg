<?php
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
/* ////// */     session_start ();     /* ////// */
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
//   now check the user is OK to view this page  //
/* /////// require ('page_access.php');  /*/////*/
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////
// ////////////////////////////////////////////////

header ( 'Content-Type: text/html; charset=utf-8' );
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

// THIS IS A LOOK-UP RECORD PAGE - GET THE RECORD INFO FIRST:
if (isset($_REQUEST['id'])) {
	$record_id = $_REQUEST['id'];
}
else {
	header("Location: documents.php?msg=NG&action=view&error=no_id");
	exit();
}

$get_doc_SQL = "SELECT * FROM  `documents` WHERE `ID` = '" . $record_id . "'";
$result_get_doc = mysqli_query($con,$get_doc_SQL);
// while loop
while($row_get_doc = mysqli_fetch_array($result_get_doc)) {

		// now print each record:
		$doc_id 				= $row_get_doc['ID']; // same as $record_id
		$doc_name_EN 			= $row_get_doc['name_EN'];
		$doc_name_CN 			= $row_get_doc['name_CN'];
		$doc_filename 			= $row_get_doc['filename'];
		$doc_filetype_ID 		= $row_get_doc['filetype_ID'];
		$doc_file_location 		= $row_get_doc['file_location'];
		$doc_lookup_table 		= $row_get_doc['lookup_table'];
		$doc_lookup_ID 			= $row_get_doc['lookup_ID'];
		$doc_document_category 	= $row_get_doc['document_category'];
		$doc_record_status 		= $row_get_doc['record_status'];
		$doc_created_by 		= $row_get_doc['created_by'];
		$doc_date_created 		= $row_get_doc['date_created'];
		$doc_filesize_bytes 	= $row_get_doc['filesize_bytes'];
		$doc_document_icon 		= $row_get_doc['document_icon'];
		$doc_document_remarks 	= $row_get_doc['document_remarks'];
		$doc_doc_revision 		= $row_get_doc['doc_revision'];
		

		// SPECIFY FULL FILE LOCATION:
		
		$file_path = '';
		
		if ($doc_document_category == 5) {
			// this is a part photo -  let's link to it
			$file_path = 'assets/images/';
			$full_file_path = 'http://120.24.71.207/' .  $doc_file_location . '/' . $doc_filename;
		}
		else {
			// DEFAULT?
			$file_path = '';
			$full_file_path = 'http://120.24.71.207/' .  $doc_file_location . '/' . $doc_filename;
		}
		
		// GET DOC CATEGORY
		
		$get_this_doc_cat_SQL = "SELECT * FROM `document_categories` WHERE `ID` = '" . $doc_document_category . "'";
		$result_get_this_doc_cat = mysqli_query($con,$get_this_doc_cat_SQL);
		// while loop
		while($row_get_this_doc_cat = mysqli_fetch_array($result_get_this_doc_cat)) {

				// now print each record:
				$doc_cat_id 			= $row_get_this_doc_cat['ID'];
				$doc_cat_name_EN 		= $row_get_this_doc_cat['name_EN'];
				$doc_cat_name_CN 		= $row_get_this_doc_cat['name_CN'];
				$doc_cat_record_status 	= $row_get_this_doc_cat['record_status'];
				
		}
		
		// GET FILETYPE
		
		$get_this_filetype_SQL = "SELECT * FROM `document_filetype` WHERE `ID` = '" . $doc_filetype_ID . "'";
		$result_get_this_filetype = mysqli_query($con,$get_this_filetype_SQL);
		// while loop
		while($row_get_this_filetype = mysqli_fetch_array($result_get_this_filetype)) {

				// now print each record:
				$filetype_id 			= $row_get_this_filetype['ID'];
				$filetype_type_name_EN 	= $row_get_this_filetype['type_name_EN'];
				$filetype_type_name_CN 	= $row_get_this_filetype['type_name_CN'];
				$filetype_default_icon 	= $row_get_this_filetype['default_icon'];
				$filetype_record_status = $row_get_this_filetype['record_status'];
				$filetype_created_by 	= $row_get_this_filetype['created_by'];
				$filetype_created_date 	= $row_get_this_filetype['created_date'];
				
		}
		
		// COMPARE TRUE FILESIZE WITH DB INFO - just to be sure...
		/*
		$php_calculate_filesize = filesize($full_file_path);
		if ($php_calculate_filesize != $doc_filesize_bytes) {
			// let's update the database with the true size of the file!
			$update_file_size_SQL = "";
		}
		
		function human_filesize($bytes, $decimals = 2) {
		  $sz = 'BKMGTP';
		  $factor = floor((strlen($bytes) - 1) / 3);
		  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}
		*/
}

$page_id = 22;

// pull the header and template stuff:
pagehead ( $page_id );
?>



<!-- START MAIN PAGE BODY : -->

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Document Record</h2>

        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                <li><a href="documents.php">All Documents</a></li>
                <li><span>Document Record</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i
                class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <?php

			// run notifications function:
			$msg = 0;
			if (isset ( $_REQUEST ['msg'] )) {
				$msg = $_REQUEST ['msg'];
			}
			$action = 0;
			if (isset ( $_REQUEST ['action'] )) {
				$action = $_REQUEST ['action'];
			}
			$change_record_id = 0;
			if (isset ( $_REQUEST ['new_record_id'] )) {
				$change_record_id = $_REQUEST ['new_record_id'];
			}
			$page_record_id = 0;
			if (isset ( $record_id )) {
				$page_record_id = $record_id;
			}

			// now run the function:
			notify_me ( $page_id, $msg, $action, $change_record_id, $page_record_id );
	?>

    <!-- start: page -->
    
    
    <?php add_button(0, 'upload_file'); ?>
    
    <!-- START PANEL - ORDER INFORMATION -->
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">
				<span class="label label-primary label-sm text-normal va-middle mr-sm"><i class="fa fa-info"></i></span>
				<span class="va-middle">Document Details</span>
			</h2>
		</header>
		<div class="panel-body">
			<div class="content">
				<!-- PANEL CONTENT HERE -->
				<div class="table-responsive">
				 <table class="table table-bordered table-striped table-hover table-condensed mb-none">
				  <tbody>
					<tr>
						<th class="text-center">ID</th>
						<td class="text-center">
							<?php echo $doc_id; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Name</th>
						<td class="text-center">
							<?php echo $doc_name_EN; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">中文名</th>
						<td class="text-center">
							<?php 
							if (($doc_name_CN!='')&&($doc_name_CN!='中文名')) {
								echo $doc_name_CN; 
							}
							else {
								?>
								<span class="text-danger">没有中文名！</span>
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Filename</th>
						<td class="text-center">
							<?php echo $doc_filename; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">File Type</th>
						<td class="text-center">
						  <i class="fa fa-<?php echo $filetype_default_icon; ?>"></i> 
							<?php 
							
								echo $filetype_type_name_EN; 
							
								if (($filetype_type_name_CN!='')&&($filetype_type_name_CN!='中文名')) {
									echo ' / ' . $filetype_type_name_CN;
								}
								
							?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Location</th>
						<td class="text-center">
						  <i class="fa fa-folder-open"></i> 
							<?php echo $doc_file_location; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Complete Location</th>
						<td class="text-center">
						  <a href="<?php echo $full_file_path; ?>">
							<?php echo $full_file_path; ?>
						  </a>
						</td>
					</tr>
					<tr>
						<th class="text-center"><abbr title="Database">DB</abbr> Table / Look-up ID</th>
						<td class="text-center">
							<?php echo $doc_lookup_table; ?> (ID. #: <?php echo $doc_lookup_ID; ?>)
							<?php 
				
							if ($doc_lookup_table == 'part_revisions') {
				
								// get the part number!
								$this_rev_part_num_SQL = "SELECT `part_ID` FROM `part_revisions` WHERE `ID` = '" . $doc_lookup_ID . "'";
								$result_this_rev_part_num = mysqli_query($con,$this_rev_part_num_SQL);
								// while loop
								while($row_this_rev_part_num = mysqli_fetch_array($result_this_rev_part_num)) {

										// now print each record:
										$part_id 			= $row_this_rev_part_num['part_ID'];
										echo '<br />';
										part_num($part_id);
										echo ' ';
										part_rev($doc_lookup_ID);
							
								}
							}
				
							?>
					
						</td>
					</tr>
					<tr>
						<th class="text-center">Document Category</th>
						<td class="text-center">
							<?php 
								echo $doc_cat_name_EN; 
							
								if (($doc_cat_name_CN!='')&&($doc_cat_name_CN!='中文名'))	 {
									echo ' / ' . $doc_cat_name_CN;
								}
							?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Record Status</th>
						<td class="text-center">
							<?php record_status($doc_record_status); ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Created by</th>
						<td class="text-center">
							<?php get_creator($doc_created_by); ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Date Created</th>
						<td class="text-center">
							<?php echo $doc_date_created; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">File Size (Bytes)</th>
						<td class="text-center">
							DB: <?php echo $doc_filesize_bytes; ?>
							<!--
							<br />
							PHP: <?php echo $php_calculate_filesize; ?>
							-->
						</td>
					</tr>
					<tr>
						<th class="text-center">Doc. Icon</th>
						<td class="text-center">
							<?php echo $doc_document_icon; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Remarks</th>
						<td class="text-center">
							<?php echo $doc_document_remarks; ?>
						</td>
					</tr>
					<tr>
						<th class="text-center">Doc. Revision</th>
						<td class="text-center">
							<?php echo $doc_doc_revision; ?>
						</td>
					</tr>
					</tbody>
				</table>
				</div>
				<!-- END PANEL CONTENT -->
    		</div>
    	</div>
    </section>
    
    
    <?php add_button(0, 'upload_file'); ?>
    
    <!-- end: page -->
</section>

<!-- : END MAIN PAGE BODY -->

<?php
// now close the page out:
pagefoot ( $page_id );

?>
