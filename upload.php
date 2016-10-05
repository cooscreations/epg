<?php
// THIS IS AN INVISIBLE UPLOAD FILE
// We can upload Category, User, Vendor and Material Images here
// material images might need some tweaking in the future and I haven't considered file dimensions yet... :S

	  session_start();
	  require ('page_functions.php'); 
	  require ('data_functions.php'); 
	  include 'db_conn.php';

	  $allowedExts = array("gif", "jpeg", "jpg", "png");
      $temp = explode(".", $_FILES["file"]["name"]);
      $extension = end($temp);
	  $max_size = 500000;
	  
      if ((($_FILES["file"]["type"] == "image/gif")
      || ($_FILES["file"]["type"] == "image/jpeg")
      || ($_FILES["file"]["type"] == "image/jpg")
      || ($_FILES["file"]["type"] == "image/pjpeg")
      || ($_FILES["file"]["type"] == "image/x-png")
      || ($_FILES["file"]["type"] == "image/png"))
      && ($_FILES["file"]["size"] < $max_size)
      && in_array($extension, $allowedExts))
        {
        if ($_FILES["file"]["error"] > 0)
          {
          echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
          }
        else 
          {

			/*
            $fileName = $temp[0].".".$temp[1];
            $temp[0] = rand(0, 3000); //Set to random number
            $fileName;
			*/

		// now let's set the relevant path:
		
		$file_type = $_REQUEST['file_type']; 
		
		if ($file_type=='user') {
			// this is a user profile image:
			$target_dir = "images/users/";
			$add_pre_file_name = "user_";
			$add_post_file_name = ""; // nothing
		}
		else if ($file_type=='vendor') {
			// this is a user profile image:
			$target_dir = "images/vendors/";
			$add_pre_file_name = "ven_";
			$add_post_file_name = ""; // nothing
		}
		else if ($file_type=='building') {
			// this is a building profile image:
			$target_dir = "images/building/";
			$add_pre_file_name = "building_";
			$add_post_file_name = ""; // nothing
		}
		else if ($file_type=='room') {
			// this is a room profile image:
			$target_dir = "images/rooms/";
			$add_pre_file_name = "room_";
			$add_post_file_name = ""; // nothing
		}
		else if ($file_type=='material') {
			// this is a material profile image:
			$target_dir = "images/materials/";
			$add_pre_file_name = "mat_";
			$add_post_file_name = ""; // nothing
			// ************************************//
			// don't forget a smaller version of the material images are here:
			// images/materials/thumbs/ -- -- with '_thumb.jpg' after the filename - messy! :S
		}
		else if ($file_type=='category') {
			// this is a user profile image:
			$target_dir = "images/categories/";
			$add_pre_file_name = "cat_";
			$add_post_file_name = ""; // nothing
		}
		
		$temp = explode(".",$_FILES["file"]["name"]);
				
		// now let's set the filename to the ID number passed from the form:
		$newfilename = $add_pre_file_name . $_REQUEST['file_ID'] . $add_post_file_name . '.' .end($temp);
		
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
			header("Location: add_file.php?msg=NG&action=exists&type=". $file_type . "&filename=" . $newfilename . "");
			die();
            }
          else
            {
				move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $newfilename);
				if (($_REQUEST['history']=='new')||($history_known=='new')) { // new file uploaded OK
					// echo 'New file was successfully uploaded. <br />It was stored in: <a href="' . $target_dir . $newfilename .'">' . $target_dir . $newfilename .'</a>';
					
				header("Location: add_file.php?msg=OK&action=upload&type=". $file_type . "&filename=" . $newfilename . "");
			die();	
					
				}
				else { // file already existed, but then we knew that... 
					// echo 'File was successfully updated. <br />It was stored in: <a href="' . $target_dir . $newfilename .'">' . $target_dir . $newfilename .'</a>';	
				header("Location: add_file.php?msg=OK&action=exists&type=". $file_type . "&filename=" . $newfilename . "");
			die();	
				}
            }
          }
        }
      else
        {
			if ($_FILES["file"]["size"] < $max_size) {
				//echo $_FILES["file"]["name"] . " is too large. Please make it smaller before uploading.";	
				header("Location: add_file.php?msg=NG&action=toobig&type=". $file_type . "&filename=" . $newfilename . "");
				die();	
				}
			else {
        		// echo $_FILES["file"]["name"] . " is an invalid file";
				header("Location: add_file.php?msg=NG&action=invalid&type=". $file_type . "&filename=" . $newfilename . "");
				die();
			}
        }
?>