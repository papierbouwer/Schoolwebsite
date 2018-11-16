<?php

if($stmt = $mysqli->prepare("SELECT Titel, beschrijving FROM categorien WHERE id = 3")){
	$stmt->execute();
	$stmt->bind_result($titel, $beschrijving);
	
	$stmt->fetch();
	$stmt->close();
}

$query = "SELECT * FROM message WHERE categorie=3";
$results = mysqli_fetch_all($mysqli->query($query), MYSQLI_ASSOC);

foreach($results as $result){
	if($stmt = $mysqli->prepare("SELECT username FROM members WHERE id = ?")){
		$stmt->bind_param('i', $result['idPlaatser']);
		$stmt->execute();
		$stmt->bind_result($username);
		
		$stmt->fetch();
		$stmt->close();
	}
	?>
	
<div>
    <h1><?= $result['titel']?></h1>
	<small>Geplaatst door <?= $username ?> op <?php echo strftime('%e %B %Y om %H:%M', $result['datum']); ?></small>
    <p class="bold"><?php bbcode($result['inhoudKort']); ?></p>
    <p><?php echo bbcode($result['inhoud']); ?></p>
	<?php if (login_check($mysqli)){
		$logged = true;
		echo '<a href="?id=edit&msg_id=' . $result['id'] . '">Veranderen</a>';
	}?>
</div>
<?php }?>