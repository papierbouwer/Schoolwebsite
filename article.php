<?php
$art_id = $_GET['article'];
if($stmt = $mysqli->prepare("SELECT titel, inhoudKort, inhoud, datum, idPlaatser FROM message WHERE id=?"))
{
	$stmt->bind_param('i', $art_id);
	$stmt->execute();
	$stmt->bind_result($titel, $inhoudKort, $inhoud, $datum, $idPlaatser);

	$stmt->fetch();
	$stmt->close();
}
if($stmt = $mysqli->prepare("SELECT username FROM members WHERE id = ?")){
	$stmt->bind_param('i', $idPlaatser);
	$stmt->execute();
	$stmt->bind_result($username);
	
	$stmt->fetch();
	$stmt->close();
}

$inhoud = bbcode($inhoud);
$inhoudKort = bbcode($inhoudKort);
?>

<div>
	<h1>
		<?php if($titel != ""){ echo $titel;} else{ echo "Geen artikel gevonden";} ?>
	</h1>
	<small>
		Geplaatst door <?= $username?> op <?php echo strftime('%e %B %Y om %H:%M',$datum)?>
	</small>
	<?php if($inhoudKort != "") echo '<p class="bold">' . $inhoudKort . '</p>'; ?>
	<p><?php echo bbcode($inhoud); ?></p>
	<?php if (login_check($mysqli)){
		$logged = true;?>
		<a href="?id=edit&msg_id=<?= $art_id?>">Veranderen</a>
	<?php }?>
</div>