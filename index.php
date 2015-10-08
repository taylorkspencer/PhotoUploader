<?php
  // Define constant variables
  define('TAB', "  ");
  $allowedTypes = array("gif" => "image/gif",
  			"jpg" => "image/jpeg",
  			"jpeg" => "image/jpeg",
  			"png" => "image/png",
  			"jfif" => "image/pjpeg",
  			"jpe" => "image/pjpeg");
  
  // If filesToUpload is not defined or set to a non-numeric value, set a default value of 3
  if (isset($_GET["filesToUpload"]))
  {
  	if (!is_numeric($_GET["filesToUpload"]))
  	{
  		$_GET["filesToUpload"] = 3;
  	}
  	// If filesToUpload is set to an invalid value (less than 1 or greater than max_file_uploads), set it to the default value of 3
  	else if (($_GET["filesToUpload"]<1)||($_GET["filesToUpload"]>=ini_get('max_file_uploads')))
  	{
  		$_GET["filesToUpload"] = 3;
  	}
  }
  else
  {
  	$_GET["filesToUpload"] = 3;
  }
  
  // Check to see if the user uploaded the form
  if (isset($_POST["uploadImages"]))
  {
  	// Parse through the uploaded files to make sure they are images of a valid type before copying them over
  	for ($uploadedFile=0; $uploadedFile<sizeof($_FILES["uploads"]["name"]); $uploadedFile++)
  	{
  		// Check to make sure the file exists
  		if ($_FILES["uploads"]["name"][$uploadedFile]!="")
  		{
  			// Check to see if an error occured in upload
  			if ($_FILES["uploads"]["error"][$uploadedFile]!=UPLOAD_ERR_OK)
  			{
  				if ($_FILES["uploads"]["error"][$uploadedFile]==UPLOAD_ERR_INI_SIZE)
  				{
  					echo "Error UPLOAD_ERR_INI_SIZE uploading {$_FILES["uploads"]["name"][$uploadedFile]}<br/>\n";
  				}
  			}
  			else
  			{
  				// If the image has no MIME type, guess it based on the file extension
  				if ($_FILES["uploads"]["type"][$uploadedFile]=="")
  				{
  					$fileParts = pathinfo($_FILES["uploads"]["name"][$uploadedFile]);
  					foreach ($allowedTypes as $extension => $mime)
  					{
  						if ($fileParts["extension"]==$extension)
  						{
  							$_FILES["uploads"]["type"][$uploadedFile] = $mime;
  							break;
  						}
  					}
  				}
  				// Check to make sure the file is an image of a valid type
	  			foreach ($allowedTypes as $extension => $mime)
		  		{
		  			if ($_FILES["uploads"]["type"][$uploadedFile]==$mime)
		  			{
		  				// If uploaddir does not exist, create it before attempting to copy the uploaded file to it
		  				if (!is_dir(dirname(dirname(__FILE__)) . "/uploaddir"))
		  				{
		  					mkdir(dirname(dirname(__FILE__)) . "/uploaddir");
		  				}
		  				// If the file is an image of a valid type, copy it over to uploadir
		  				move_uploaded_file($_FILES["uploads"]["tmp_name"][$uploadedFile], dirname(dirname(__FILE__)) . "/uploaddir/" . $_FILES["uploads"]["name"][$uploadedFile]);
		  				break;
		  			}
		  		}
		  	}
	  	}
  	}
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>Photo Uploader</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <script type="text/javascript" src="script.js"></script>
  </head>
  <body>
    <div id="heading">Photo Uploader</div>
    <table id="authorInfoTable">
      <tr>
        <td>Taylor Spencer</td>
        <td>CSCE 3420</td>
        <td>Internet Programming</td>
        <td>Program 7</td>
      </tr>
    </table>
    <?php
      // Print the "Last Updated" statement
      echo "<div id=\"lastUpdated\">This file was last updated: " . date('F d Y H:i:s', getlastmod()) . "</div>\n";
      
      // Print the opening form tag
      echo TAB . TAB . "<form method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"return checkFileContainers()\">\n";
      
      // Print the opening tag for the upload containers
      echo TAB . TAB . TAB . "<div class=\"uploadContainerSpace\">\n";
      
      // Print the file upload containers
      for ($uploadContainer=0; $uploadContainer<$_GET["filesToUpload"]; $uploadContainer++)
      {
      	// Print the opening tag for the file upload container
      	echo TAB . TAB . TAB . TAB . "<div class=\"uploadContainer\">\n";
      	
      	// Print the file upload container
      	echo TAB . TAB . TAB . TAB . TAB . "<input type=\"file\" name=\"uploads[{$uploadContainer}]\" accept=\"image/*\" onchange=\"return checkFileType({$uploadContainer}, false)\"/>\n";
      	
      	// If there are more than one upload containers and if this is the last upload container, print a button allowing a user to remove it
      	if (($_GET["filesToUpload"]>1)&&($uploadContainer==($_GET["filesToUpload"]-1)))
      	{
      		echo TAB . TAB . TAB . TAB . TAB . "<input type=\"button\" name=\"removeLastContainer\" value=\"Remove\" onclick=\"location.href='http://{$_SERVER["HTTP_HOST"]}{$_SERVER["PHP_SELF"]}?filesToUpload=" . urlencode($_GET["filesToUpload"]-1) . "'\"/>\n";
      	}
      	
      	// Print the closing tag for the file upload container
      	echo TAB . TAB . TAB . TAB . "</div>\n";
      }
      
      // If the number of upload containers is less than max_file_uploads, print a button to allow users to increase the number of upload containers
      if ($_GET["filesToUpload"]<ini_get('max_file_uploads'))
      {
      	echo TAB . TAB . TAB . TAB . "<input type=\"button\" name=\"addNewContainer\" value=\"Add Upload Container\" onclick=\"location.href='http://{$_SERVER["HTTP_HOST"]}{$_SERVER["PHP_SELF"]}?filesToUpload=" . urlencode($_GET["filesToUpload"]+1) . "'\"/>\n";
      }
      
      // Print the closing tag for the upload containers
      echo TAB . TAB . TAB . "</div>\n";
      
      // Print the submit and reset buttons
      echo TAB . TAB . TAB . "<input type=\"submit\" name=\"uploadImages\" value=\"Upload Images\"/>\n";
      echo TAB . TAB . TAB . "<input type=\"reset\" name=\"resetUploads\" value=\"Reset\"/>\n";
      
      // Print the closing form tag
      echo TAB . TAB . "</form>\n";
      
      // Check to make sure uploaddir exists
      if (is_dir(dirname(dirname(__FILE__)) . "/uploaddir"))
      {
      	// Print the opening tags for the images container
      	echo TAB . TAB . "<div class=\"imagesContainer\">\n";
      	
      	// Print the uploaded images, along with their file names
      	$uploaddirContents = scandir(dirname(dirname(__FILE__)) . "/uploaddir/");
      	$finfo = finfo_open(FILEINFO_MIME_TYPE);
      	foreach ($uploaddirContents as $filename)
      	{
      		// Check to see if the file is actually a file
      		if (is_file(dirname(dirname(__FILE__)) . "/uploaddir/" . $filename))
      		{
      			// Check to see if the file is an image of a valid type
      			foreach ($allowedTypes as $extension => $mime)
		  	{
	      			if (finfo_file($finfo, dirname(dirname(__FILE__)) . "/uploaddir/" . $filename)==$mime)
	      			{
	      				// Get the height and the width of the image for use in the image tag
	      				$imageHeightWidth = getimagesize(dirname(dirname(__FILE__)) . "/uploaddir/" . $filename);
	      				// Print the image to the page
	      				echo TAB . TAB . TAB . "<img src=\"http://{$_SERVER["HTTP_HOST"]}" . dirname(dirname($_SERVER["PHP_SELF"])) . "/uploaddir/{$filename}\" height=\"{$imageHeightWidth[1]}\" width=\"{$imageHeightWidth[0]}\" alt=\"{$filename}\"/>\n";
	      				break;
	      			}
	      		}
      		}
      	}
      	
      	// Print the closing tags for the images container
      	echo TAB . TAB . "</div>\n";
      }
    ?>
  </body>
</html>
