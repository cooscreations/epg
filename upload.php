<?php

// THIS IS AN INVISIBLE UPLOAD FILE
// We can upload Category, User, Vendor and Material Images here
// material images might need some tweaking in the future and I haven't considered file dimensions yet... :S

	  session_start();
	  require ('page_functions.php');
	  include 'db_conn.php';

// echo 'starting...';

/*
if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die("Upload failed with error " . $_FILES['file']['error']);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
$ok = false;

switch ($mime) {
   case 'image/jpeg':
   case 'image/gif':
   case 'image/jpg':
   case 'image/pjpeg':
   case 'image/x-png':
   case 'image/png':
   case 'application/pdf'
   case etc....
        $ok = true;
   default:
       die("Unknown/not permitted file type");
}

move_uploaded_file(...);


*/	

	  $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xsl", "xlsx", "GIF", "JPEG", "JPG", "PNG", "PDF", "DOC", "DOCX", "XLS", "XLSX");
      $temp = explode(".", $_FILES["file"]["name"]);
      $extension = end($temp);
	  $max_size = 500000;
  
      if (
		  (
			  ($_FILES["file"]["type"] == "image/gif")
			  || ($_FILES["file"]["type"] == "image/jpeg")
			  || ($_FILES["file"]["type"] == "image/jpg")
			  || ($_FILES["file"]["type"] == "image/pjpeg")
			  || ($_FILES["file"]["type"] == "image/x-png")
			  || ($_FILES["file"]["type"] == "image/png")
			  || ($_FILES["file"]["type"] == "application/pdf")
			  || ($_FILES["file"]["type"] == "application/doc")
			  || ($_FILES["file"]["type"] == "application/docx")
			  || ($_FILES["file"]["type"] == "application/vnd.ms-excel")
			  || ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
		  )
		  && ($_FILES["file"]["size"] < $max_size)
		  && in_array($extension, $allowedExts)
      )
        {
        if ($_FILES["file"]["error"] > 0)
          {
          echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
          }
        else 
          {
          // echo 'OK';

		
		// $fileName = $temp[0].".".$temp[1];
		// $temp[0] = rand(0, 3000); //Set to random number
		// $fileName;

		// now let's set the relevant path:
		
		$file_cat = $_REQUEST['doc_category']; 
		
		if ($file_cat=='6') { // USER
			// this is a user profile image:
			$target_dir = "assets/images/users/";
			$add_pre_file_name = "";
			$add_post_file_name = ""; // nothing
		}
		else {
			$target_dir = "upload/";
			$add_pre_file_name = "";
			$add_post_file_name = ""; // nothing
		}
		
		$temp = explode(".",$_FILES["file"]["name"]);
				
		// now let's set the filename to the ID number passed from the form:
		$newfilename = $add_pre_file_name . $_REQUEST['file_ID'] . $add_post_file_name . '.' .end($temp);
		
		$newfilename = $_FILES["file"]["name"];
		
		// echo 'FILE FOUND IS CALLED ' . $_FILES["file"]["name"] . '<br />';
		
		if (file_exists($target_dir . $newfilename)) {
			// we are updating a file now...
			$history_known = 'update';	
		}
		else {
			// this is a new file, regardless of what anyone else tells you :)
			$history_known = 'new';	
		}

          if ((file_exists($target_dir . $newfilename))&&($_REQUEST['history'])!='update')
            {
            // echo '<a href="' . $target_dir . $newfilename .'">' . $target_dir . $newfilename .'</a> already exists.';
			header("Location: upload_file.php?msg=NG&error=exists&cat=". $file_cat . "&filename=" . $newfilename . "&table=" . $_REQUEST['lookup_table'] . "");
			die();
            }
          else
            {
				move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $newfilename);
				
				if (($_REQUEST['history']=='new')||($history_known=='new')) { // new file uploaded OK
					// echo 'New file was successfully uploaded. <br />It was stored in: <a href="' . $target_dir . $newfilename .'">' . $target_dir . $newfilename .'</a>';					
					// header("Location: add_file.php?msg=OK&action=upload&cat=". $file_cat . "&filename=" . $newfilename . "");
					// die();	
					
				}
				else { // file already existed, but then we knew that... 
					// echo 'File was successfully updated. <br />It was stored in: <a href="' . $target_dir . $newfilename .'">' . $target_dir . $newfilename .'</a>';	
					// header("Location: add_file.php?msg=OK&action=exists&cat=". $file_cat . "&filename=" . $newfilename . "");
					// die();	
				}
			
				// now show the info we have for the DB from the form:
				
				$doc_lookup_table 	= checkaddslashes($_REQUEST['doc_lookup_table']);
				$doc_lookup_ID 		= checkaddslashes($_REQUEST['doc_lookup_ID']);
				$doc_name_EN 		= checkaddslashes($_REQUEST['doc_name_EN']);
				$doc_name_CN 		= checkaddslashes($_REQUEST['doc_name_CN']);
				$doc_filetype_ID 	= checkaddslashes($_REQUEST['doc_filetype_ID']);
				$doc_category 		= checkaddslashes($_REQUEST['doc_category']);
				$doc_filesize_bytes = $_FILES["file"]["size"];
				$doc_icon 			= checkaddslashes($_REQUEST['doc_icon']);
				$doc_remarks 		= checkaddslashes($_REQUEST['doc_remarks']);
				$doc_revision 		= checkaddslashes($_REQUEST['doc_revision']);
				$doc_date_created 	= check_date_time($_REQUEST['doc_date_created']);
				$created_by 		= checkaddslashes($_REQUEST['created_by']);
				$record_status 		= checkaddslashes($_REQUEST['record_status']);
				$form_action 		= checkaddslashes($_REQUEST['form_action']);
				$next_step 			= checkaddslashes($_REQUEST['next_step']);
				
				// NOW write to the DB:
				$add_new_doc_SQL = "INSERT INTO `documents`(
				`ID`, 
				`name_EN`, 
				`name_CN`, 
				`filename`, 
				`filetype_ID`, 
				`file_location`, 
				`lookup_table`, 
				`lookup_ID`, 
				`document_category`, 
				`record_status`, 
				`created_by`, 
				`date_created`, 
				`filesize_bytes`, 
				`document_icon`, 
				`document_remarks`, 
				`doc_revision`) VALUES (
				NULL,
				'" . $doc_name_EN . "',
				'" . $doc_name_CN . "',
				'" . $newfilename . "',
				'" . $doc_filetype_ID . "',
				'" . substr($target_dir, 0, -1) . "',
				'" . $doc_lookup_table . "',
				'" . $doc_lookup_ID . "',
				'" . $doc_category . "',
				'" . $record_status . "',
				'" . $created_by . "',
				'" . $doc_date_created . "',
				'" . $doc_filesize_bytes . "',
				'" . $doc_icon . "',
				'" . $doc_remarks . "',
				'" . $doc_revision . "')";
				
				// echo '<h1>ADD SQL: ' . $add_new_doc_SQL . '</h1>';
				
						if (mysqli_query($con, $add_new_doc_SQL)) {

						$record_id = mysqli_insert_id($con);

						// echo "INSERT # " . $record_id . " OK";
						
						$update_note = "Adding a new document to the system.";

							// AWESOME! We added the record
						$record_edit_SQL = "INSERT INTO `update_log`(`ID`, `table_name`, `update_ID`, `user_ID`, `notes`, `update_date`, `update_type`, `update_action`) VALUES (NULL,'documents','" . $record_id . "','" . $_SESSION['user_ID'] . "','" . $update_note . "','" . date("Y-m-d H:i:s") . "', 'general', 'INSERT')";
							// echo $record_edit_SQL;

							if (mysqli_query($con, $record_edit_SQL)) {
								// AWESOME! We added the change record to the database
			
								if ($_REQUEST['next_step'] == 'view_record') {
									// view record!
									// regular add - send them to the record page
									header("Location: document_view.php?msg=OK&action=add&id=".$record_id."");
									exit();
								}
								else {
									// send them to the doc list
									header("Location: documents.php?msg=OK&action=add&new_record_id=".$record_id."");
									exit();
								}


							}
							else {
								echo "<h4>Failed to record the change in the edit log with SQL: <br />" . $record_edit_SQL . "</h4>";
							}

					}
					else {
						echo "<h4>Failed to add new document with SQL: <br />" . $add_supplier_SQL . "</h4>";
					}
			
            }
          }
        }
      else
        {
			if ($_FILES["file"]["size"] > $max_size) {
				echo $_FILES["file"]["name"] . " is too large. Please make it smaller before uploading.";	
				// header("Location: upload_file.php?msg=NG&action=toobig&cat=". $file_cat . "&filename=" . $newfilename . "&table=" . $_REQUEST['lookup_table'] . "");
				// die();	
				}
			else {
        		echo $_FILES["file"]["name"] . " is an invalid file. File type is " . $_FILES["file"]["type"] . "";
				// header("Location: upload_file.php?msg=NG&action=invalid&cat=". $file_cat . "&filename=" . $newfilename . "&table=" . $_REQUEST['lookup_table'] . "");
				// die();
			}
        }

?>