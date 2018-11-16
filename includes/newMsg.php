<?php
include_once 'register.inc.php';
include_once 'functions.php';
sec_session_start();

if(login_check($mysqli))
{
	if(isset($_POST['categorie'], $_POST['title'], $_POST['inhoud']))
	{
		$titel = $_POST['title'];
		$inhoud = stripslashes($_POST['inhoud']);
		$inhoud = htmlspecialchars($inhoud, ENT_QUOTES);	
		$kortInhoud = stripslashes($_POST['kortInhoud']);
		$kortInhoud = htmlspecialchars($kortInhoud, ENT_QUOTES);
		$categorie= $_POST['categorie'];
		if($stmt = $mysqli->prepare("INSERT INTO message (id, idPlaatser, datum, categorie, titel, inhoudKort, inhoud) VALUES (NULL, ?, ?, ?, ?, ?, ?);"))
		{
			$stmt->bind_param("iiisss", $_SESSION['user_id'], time(), $categorie, $titel, $kortInhoud, $inhoud);
			$stmt->execute();
			$stmt->close();
		}
	}
	header('location: /');
} else
{
	echo "Je moet ingelogd zijn om dit te kunnen bekijken.";
}
?>