<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<meta name="keywords" content="cosmosys log in page" />
		<meta name="description" content="Log In">
		<meta name="author" content="Mark Clulow / cosmosupplylab ltd.">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />

		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />

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
			<h2 class="panel-title"><i class="fa fa-cloud-upload"></i> Upload a new profile photo</h2>
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
    <i class="fa fa-upload fa-2x"></i> Select profile image to upload:
    <input type="file" name="file" id="file"><br />
    <input name="file_type" type="hidden" value="<?php echo $_REQUEST['file_type']; ?>" />
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