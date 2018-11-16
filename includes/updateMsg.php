<?php
include_once 'register.inc.php';
include_once 'functions.php';
sec_session_start();

if(login_check($mysqli))
{
	if(isset($_POST['id'], $_POST['title'], $_POST['inhoud']))
	{
		$id = $_POST['id'];
		$title = $_POST['title'];
		$inhoud = stripslashes($_POST['inhoud']);
		$inhoud = htmlspecialchars($inhoud, ENT_QUOTES);
		if($_POST['kortInhoud'] != "")
		{
			$kortInhoud = stripslashes($_POST['kortInhoud']);
			$kortInhoud = htmlspecialchars($kortInhoud, ENT_QUOTES);
			if($stmt = $mysqli->prepare("UPDATE message SET titel = ? ,inhoud = ? ,inhoudKort = ? WHERE id = ?"))
			{
				$stmt->bind_param("sssi", $title, $inhoud, $kortInhoud, $id);
				$stmt->execute();
				$stmt->close();
			}
		} else
		{
			if($stmt = $mysqli->prepare("UPDATE message SET titel=? ,inhoud=? WHERE id=?"))
			{
				$stmt->bind_param("ssi", $title, $inhoud, $id);
				$stmt->execute();
				$stmt->close();
			}
		}
	}
	header('location: /');
} else
{
	echo "Je moet ingelogd zijn om dit te kunnen bekijken.";
}
?>