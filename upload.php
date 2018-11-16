<?php if(login_check($mysqli)){?>
	<form action="upload_save.php" method="post" enctype="multipart/form-data">
	Foto uploaden:<br>
	<input type="file" name="uploadFile" accept="
		image/*, 
		video/*, 
		audio/*, 
		application/pdf, 
		application/vnd.ms-excel, 
		application/vnd.ms-excel.addin.macroEnabled.12,
		application/vnd.ms-excel.sheet.binary.macroEnabled.12,
	 	application/vnd.ms-excel.sheet.macroEnabled.12,
		application/vnd.ms-excel.template.macroEnabled.12,
		application/vnd.ms-powerpoint,
		application/vnd.ms-powerpoint.addin.macroEnabled.12,
		application/vnd.ms-powerpoint.presentation.macroEnabled.12,
		application/vnd.ms-powerpoint.slide.macroEnabled.12,
		application/vnd.ms-powerpoint.slideshow.macroEnabled.12,
		application/vnd.ms-powerpoint.template.macroEnabled.12,
		application/vnd.ms-word.document.12,
		application/vnd.ms-word.document.macroEnabled.12,
		application/vnd.ms-word.template.macroEnabled.12,"><br>
	<input type="submit" value="Upload Bestand">
	</form> 
<?php 
} else
{
	include("403.shtml");
}
?>