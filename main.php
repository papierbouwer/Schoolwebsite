<?php 
if($stmt = $mysqli->prepare("SELECT titel, inhoud FROM message WHERE id=1")){
	$stmt->execute();
	$stmt->bind_result($titel, $inhoud);

	$stmt->fetch();
	$stmt->close();
}

$inhoud = bbcode($inhoud);
?>
<div>
	<h1>
		<?php echo $titel;?>
	</h1>
	<p>
		<?php echo $inhoud;?>
	</p>
	<?php if (login_check($mysqli)){
		$logged = true;?>
		<a href="?id=edit&msg_id=1">Veranderen</a>
	<?php }?>
</div>