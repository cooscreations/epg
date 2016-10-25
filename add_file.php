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

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}
?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<meta name="keywords" content="EPG File Upload" />
		<meta name="description" content="Upload A File">
		<meta name="author" content="Mark Clulow / EPG">
		
		
		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="bootstrap.min.css" type="text/css" media="print" ><!-- printer-friendly? -->

		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
		<link rel="stylesheet" href="assets/vendor/intl-tel-input/css/intlTelInput.css" />

		<!-- Specific Page Vendor CSS - ADVANCED FORMS-->
		<link rel="stylesheet" href="assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css" />
		<link rel="stylesheet" href="assets/vendor/select2/select2.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/css/basic.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/css/dropzone.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-markdown/css/bootstrap-markdown.min.css" />
		<link rel="stylesheet" href="assets/vendor/summernote/summernote.css" />
		<link rel="stylesheet" href="assets/vendor/summernote/summernote-bs3.css" />
		<link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css" />
		<link rel="stylesheet" href="assets/vendor/codemirror/theme/monokai.css" />


		<!-- Specific Page Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="assets/stylesheets/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

		<!-- Head Libs -->
		<script src="assets/vendor/modernizr/modernizr.js"></script>
	</head>
	<body>
<div id="custom-content" class="modal-block modal-block-md">
	<section class="panel">
		<header class="panel-heading primary">
			<h2 class="panel-title"><i class="fa fa-cloud-upload"></i> Upload a new File</h2>
		</header>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">

<?php 
/*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
/*  ~~~~~~~ MESSAGE BLOCK ~~~~~~~~~ */
/*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

if (isset($_REQUEST['msg'])) {
	// we found a message!
	if ($_REQUEST['msg']=='NG') {
		$msg_id = 'danger';
	}
	else {
		$msg_id = 'success';
	}
	
	// now create the message box:
	?>
    
    
    <div class="alert alert-<?php echo $msg_id; ?>">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<?php 
	
	if ($_REQUEST['action']=='exists') {
		if ($_REQUEST['msg']=='NG') {
			?><strong>✘ This file already exists. ✘</strong><br />Please rename it and try again.<?php
		}
		else if ($_REQUEST['msg']=='OK') {
			?><strong>✔ The file was updated. ✔</strong><br /> You may close this window to continue. <br />Please refresh the page to see your changes.<?php
		}
	}
	
	else if ($_REQUEST['action']=='upload') {
		if ($_REQUEST['msg']=='NG') {
			?>✘ There was an error uploading the file. ✘<br />Please try again.<?php
		}
		else if ($_REQUEST['msg']=='OK') {
			?>✔ The file was successfully uploaded. ✔<br />You may close this window to continue. <br />Please refresh the page to see your changes.<?php
		}
	}
	
	else if ($_REQUEST['action']=='toobig') {
		if ($_REQUEST['msg']=='NG') {
			?>✘ The file you are trying to upload is too large. ✘<br />Please reduce it to under 200 KB and try again.<?php
		}
		else if ($_REQUEST['msg']=='OK') { // probably never called...
			?>✔ The file was not too big. ✔<?php
		}
	}
	
	else if ($_REQUEST['action']=='invalid') {
		if ($_REQUEST['msg']=='NG') {
			?>✘ The file was invalid. ✘<br />Please upload one of the following images: <br /><strong>gif, jpeg, jpg, png</strong>. <?php
		}
		else if ($_REQUEST['msg']=='OK') { // probably never called... 
			?>✔ The file was not invalid. ✔<?php
		}
	}
	
	else if ($_REQUEST['action']=='delete') {
		if ($_REQUEST['msg']=='NG') {
			?>✘ There was an error deleting the file. Please try again. ✘<?php
		}
		else if ($_REQUEST['msg']=='OK') {
			?>✔ The file was successfully deleted. ✔<?php
		}
	}
	
	?><!-- 
    
    // // // // // // // //
    ADMIN STUFF, IN CASE OF FILE UPLOAD PROBLEMS
    
    <p style="text-align:left;">Admin: <br /><strong>Name:</strong><?php echo $_REQUEST['filename']; ?><br /><strong>Type:</strong><?php echo $_REQUEST['type']; ?></p> -->
    </div>
	<?php
}
else {
	// no message found... do nothing
}

/*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
/*  ~~~~~ END MESSAGE BLOCK ~~~~~~~ */
/*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

?>

<form action="upload.php" method="post" enctype="multipart/form-data">
    
    <i class="fa fa-upload fa-2x"></i> Select file to upload: <input type="file" name="file" id="file">
    
    <br />
    
    	<select class="form-control populate" name="file_type" id="file_type">
	  	 	<option value="0" selected="selected">Select File Type:</option>
    
		<?php 
	
		$get_file_type_SQL = "SELECT * FROM `document_filetype` WHERE `record_status` = '2'";
		// echo '<h1>SQL: ' . $get_file_type_SQL . '</h1>';
	
		$result_get_file_type = mysqli_query($con,$get_file_type_SQL);

		// while loop
		while($row_get_file_type = mysqli_fetch_array($result_get_file_type)) {
			$file_type_ID 				= $row_get_file_type['ID'];
			$file_type_name_EN 			= $row_get_file_type['type_name_EN'];
			$file_type_name_CN 			= $row_get_file_type['type_name_CN'];
			$file_type_default_icon 	= $row_get_file_type['default_icon'];
			$file_type_record_status 	= $row_get_file_type['record_status']; // Should be 2
			$file_type_created_by 		= $row_get_file_type['created_by']; // don't need this!
			$file_type_created_date 	= $row_get_file_type['created_date']; // don't need this!
		
			?>
			<option value="<?php echo $file_type_ID; ?>"<?php if ($_REQUEST['file_type'] == $file_type_ID) { ?> selected="selected"<?php } ?>><?php 
				echo $file_type_name_EN;
				if (($file_type_name_CN!='')&&($file_type_name_CN!='中文名')){
					echo ' / ' . $file_type_name_CN;
				}
			?></option>
			<?php
		}
		?>
		</select>
		
    <br />
    
    	<select class="form-control populate" name="doc_cat" id="doc_cat">
	  	 	<option value="0" selected="selected">Select Document Category:</option>
    
		<?php 
	
		$get_doc_cat_SQL = "SELECT * FROM `document_categories` WHERE `record_status` = '2'";
		// echo '<h1>SQL: ' . $get_doc_cat_SQL . '</h1>';
	
		$result_get_doc_cat = mysqli_query($con,$get_doc_cat_SQL);

		// while loop
		while($row_get_doc_cat = mysqli_fetch_array($result_get_doc_cat)) {
			$doc_cat_ID 				= $row_get_doc_cat['ID'];
			$doc_cat_name_EN 			= $row_get_doc_cat['name_EN'];
			$doc_cat_name_CN 			= $row_get_doc_cat['name_CN'];
			$doc_cat_record_status 		= $row_get_doc_cat['record_status']; // Should be 2
			$doc_cat_target_dir 		= $row_get_doc_cat['target_dir'];
		
			?>
			<option value="<?php echo $doc_cat_ID; ?>"<?php if ($_REQUEST['doc_cat'] == $doc_cat_ID) { ?> selected="selected"<?php } ?>><?php 
				echo $doc_cat_name_EN;
				if (($doc_cat_name_CN!='')&&($doc_cat_name_CN!='中文名')){
					echo ' / ' . $doc_cat_name_CN;
				}
			?></option>
			<?php
		}
		?>
		</select>
    
    
  	<input name="file_ID" type="hidden" value="<?php echo $_REQUEST['ID']; ?>">
  	<input name="history" type="hidden" value="<?php echo $_REQUEST['history']; ?>"> 
    
    <button type="submit" class="mb-xs mt-xs mr-xs btn btn-success pull-right" name="submit" id="submit" value="Upload">
    	<i class="fa fa-cloud-upload"></i> Upload
    </button>
    
</form>

</div>
			</div>
		</div>
	</section>
</div>		<!-- end: page -->

		<!-- Vendor -->
		<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="assets/javascripts/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="assets/javascripts/theme.custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="assets/javascripts/theme.init.js"></script>

	</body>
</html>