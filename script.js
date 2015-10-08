var allowedExtensions = ["gif", "jpg", "jpeg", "png", "jfif", "jpe"];

// Internal method: Used by checkFileContainers and areAllFileContainersEmpty to get an array of form controls
function getFormArray(formNameOrNumber, arrayName)
{
	var toReturn = new Array();
	for (var parsing=0; parsing<document.forms[formNameOrNumber].length; parsing++)
	{
		var thisArrayName = (document.forms[formNameOrNumber][parsing].name).split('[');
		if ((thisArrayName[0]==arrayName)&&((document.forms[formNameOrNumber][parsing].name)[(document.forms[formNameOrNumber][parsing].name).length-1]==']'))
		{
			toReturn.push(document.forms[formNameOrNumber][parsing]);
		}
	}
	return toReturn;
}

// Checks to see if the user picked a file of a valid extension
// Returns true if the user is uploading a valid file or no file,
// returns false if the user is uploading an invalid file
function checkFileType(containerToCheck, silent)
{
	var container = document.forms[0]["uploads["+containerToCheck+"]"];
	// Check to see if the user is uploading a file before proceeding
	if (container.value!="")
	{
		var extension = (container.value).split('.');
		for (var checking=0; checking<allowedExtensions.length; checking++)
		{
			// If the file being uploaded is of a valid type, return true to allow the upload and exit the method
			if (container[checking].toLowerCase()==extension[extension.length-1].toLowerCase())
			{
				return true;
			}
		}
		// If the file being uploaded is not of a valid type, display an error message and set the value to blank to force the user to select a different file
		if (!silent)
		{
			alert("The file " + container.value + " is not in a valid image format.  Valid image formats include GIF, JPEG, and PNG files.");
			container.value = "";
		}
		return false;
	}
	else
	{
		// If the user is not uploading a file, return true, as an unfilled field will not be uploaded
		return true;
	}
}

// Internal method: Used to see if all the file containers are empty
function areAllFileContainersEmpty()
{
	var fileContainers = getFormArray(0, "uploads");
	for (var checking=0; checking<fileContainers.length; checking++)
	{
		// If this file container contains a file, return false
		if (fileContainers[checking].value!="")
		{
			return false;
		}
	}
	// If all the file containers are empty, return true
	return true;
}

// Check to make sure all the file containers are filled with valid
// files before submitting
// Returns false if any of the form fields contain invalid files or
// if all the form fields are empty, otherwise returns true
function checkFileContainers()
{
	// Check to make sure all the file containers aren't empty
	if (!areAllFileContainersEmpty())
	{
		// Parse through the file containers and make sure they all contain files of a valid type
		var fileContainers = getFormArray(0, "uploads");
		for (var checking=0; checking<fileContainers.length; checking++)
		{
			// If this file container contains a file of an invalid type, display an error message and return false to cancel the upload
			if (!checkFileType(checking, true))
			{
				alert("One or more of the uploaded files are not images of a valid format.  Valid image formats include GIF, JPEG, and PNG files.");
				return false;
			}
		}
		// If all the checks finished successfully, return true to allow the upload
		return true;
	}
	else
	{
		// If all the file containers are empty, display an error message and return false to cancel the upload
		alert("You did not select any files to be uploaded.  Please add some GIF, JPEG, or PNG image files to be uploaded and then press Upload Images.");
		return false;
	}
}
