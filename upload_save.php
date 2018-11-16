<?php

include_once 'includes/register.inc.php';
include_once 'includes/functions.php';
sec_session_start();

if(login_check($mysqli))
{
	$target_dir = "upload/";
	$target_dir = $target_dir . basename( $_FILES["uploadFile"]["name"]);
	$uploadOk=1;
	$error = "";
	$uploadFile_type = $_FILES["uploadFile"]["type"];
	$uploadFile_size = $_FILES["uploadFile"]["size"];

	// Check if file already exists
	if (file_exists($target_dir . $_FILES["uploadFile"]["name"])) {
		$error += "Sorry, file already exists.<br>";
		$uploadOk = 0;
	}

	// Check file size
	if ($uploadFile_size > 500000) {
		$error += "Sorry, your file is too large.<br>";
		$uploadOk = 0;
	}

	$link = "http://v14ebaalbe.helenparkhurst.net/upload/" . basename($_FILES["uploadFile"]["name"]);

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, het bestand kan niet worden geüpload. Deze foutmeldingen werden gegenereerd:<br>";
		echo "<span style=\"color: red;\">" . $error . "</span>";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_dir)) {
			if($uploadFile_type == "image/bmp" || 
			$uploadFile_type == "image/jpg" || 
			$uploadFile_type == "image/jpeg" ||
			$uploadFile_type == "image/tiff" ||
			$uploadFile_type == "image/gif" ||
			$uploadFile_type == "image/png")
			{
				echo "
				<h1>Uploaden voltooid</h1>
				Voorbeeld:<br>
				<img src=" . $link . "></img><br>
				Plak dit in het bericht:<br>
				<input type=\"text\" value=\"[img]" . $link . "[/img]\" ><br>
				Of deze maar dan klikbaar:<br>
				<input type=\"text\" value=\"[url=" . $link . "][img]" . $link . "[/img][/url]\">
				";
			} elseif($uploadFile_type == "video/ogg" || 
			$uploadFile_type == "video/mp4"||
			$uploadFile_type == "video/WebM")
			{
				echo "
				De video us geüpload.<br>
				Link:<br>
				<input type=\"text\" value=\"[video][source=\"" . $link . "\"][/video]\">
				";
			} else
			{
				echo "
				Bestand succesvol geüpload.<br>
				Link:
				<input type=\"text\" value=\"" . $link . "\">
				";
			}
		} else {
			echo "Sorry, er was een probleem met het uploaden.";
		}
	}
}
?> 