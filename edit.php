<?php

if(login_check($mysqli))
{
	$id = $_GET['msg_id'];
	if($stmt = $mysqli->prepare("SELECT titel, inhoudKort, inhoud FROM message WHERE id=?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
		
		$stmt->bind_result($titel, $inhoud_kort, $inhoud);
		$stmt->fetch();
		$stmt->close();
	}
	?>
	<div class="form">
		<form action="/includes/updateMsg.php" method="post" name="edit-form">
			<label for="id">ID:</label><br/>
			<?php echo '<input type="text" name="id" id="id" value="'. $id . '" readonly/><br/>';?>
			<label for="title">Titel:</label><br/>
			<?php 
			echo '<input type="text" name="title" id="title" value="' . $titel . '" />';
			if(!($inhoud_kort == ""))
			{
				echo '<br/> <label for="kortInhoud">Korte Inhoud:</label> <br/> <textarea id="kortInhoud" name="kortInhoud">' . $inhoud_kort . '</textarea>';
			}
			?>
			<br/>
			<label for="inhoud" >Inhoud:</label><br/>
			<textarea id="inhoud" name="inhoud"><?php echo $inhoud; ?></textarea/><br/>
			<input type="submit" value="Opslaan"/>
		</form>
	</div>
		
	<?php
} else{
	include("403.shtml");
}

?>