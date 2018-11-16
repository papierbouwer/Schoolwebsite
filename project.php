<?php

if($stmt = $mysqli->prepare("SELECT Titel, beschrijving FROM categorien WHERE id = 2")){
	$stmt->execute();
	$stmt->bind_result($titel, $beschrijving);
	
	$stmt->fetch();
	$stmt->close();
}

$query = 'SELECT * FROM message WHERE categorie = 2 ORDER BY datum DESC';
$result = mysqli_fetch_all($mysqli->query($query), MYSQLI_ASSOC);

echo "<h1>" . $titel . "</h1><p>" . $beschrijving . "</p><div class='bericht_container'>";

foreach($result as $article) {
	if($stmt = $mysqli->prepare("SELECT username FROM members WHERE id = ?")){
		$stmt->bind_param('i', $article['idPlaatser']);
		$stmt->execute();
		$stmt->bind_result($username);

		$stmt->fetch();
		$stmt->close();
	}
?>

<div class="bericht">
	<h3>
		<?= $article['titel']?>
	</h3>
	<small>
		Geplaatst door: <?= $username?> op <?php echo strftime('%e %B %Y om %H:%M', $article['datum']);?>
	</small>
	<p>
		<?php echo bbcode($article['inhoudKort']); ?>
	</p>
	<a href="./index.php?article=<?= $article['id']?>">Lees meer</a>
</div>

<?php } echo "</div>";?>