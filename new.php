<?php if(login_check($mysqli)) {?>
	<div class="form">
		<form action="/includes/newMsg.php" method="post" name="new-form">
			<label for="title">Titel:</label><br/>
			<input type="text" name="title" id="title"/><br/> 
			<label for="categorie">Categorie:</label>
			<input name="categorie" id="categorie"><br/>
			<label for="kortInhoud">Korte Inhoud:</label> <br/> 
			<textarea id="kortInhoud" name="kortInhoud"></textarea> <br/>
			<label for="inhoud" >Inhoud:</label><br/>
			<textarea id="inhoud" name="inhoud"></textarea/><br/>
			<input type="submit" value="Opslaan"/>
		</form>
	</div>
<?php
} else{
	include("403.shtml");
}

?>