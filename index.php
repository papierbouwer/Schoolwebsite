<?php
include_once 'includes/register.inc.php';
include_once 'includes/functions.php';
sec_session_start();
?>
<!DOCTYPE html>
<html lang='nl'>

	<head>
		<meta charset="UTF-8" />
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<title>
			School Site
		</title>
		<script src="/script/jquery.js"></script>
		<script src="/script/script.js" ></script>
		<script type="text/JavaScript" src="js/sha512.js"></script> 
		<script type="text/JavaScript" src="js/forms.js"></script>
		<link rel="stylesheet" href="/highlight/styles/xcode.css">
		<script src="/highlight/highlight.pack.js"></script>
		<script>hljs.initHighlightingOnLoad();</script>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	
	<body>
		<div id="main">
			<div class="topBar">
				<nav>
					<div class="handle">
						<p>&#9776;	Menu</p>
					</div>
					<ul>
						<li class="nav-link">
							<a href="./">
								Home
							</a>
						</li>
						<li class="nav-link">
							<a href="./?id=info">
								Info
							</a>
						</li>
						<li class="nav-link">
							<a href="./?id=project">
								Projecten
							</a>
						</li>
						<li class="nav-link">
							<a href="./?id=contact">						
								Contact
							</a>						
						</li>					
					</ul>
				</nav>
			</div>
			<div class="Page">
				<div class="message">
					<div class="container">
						
						<?php
						
						if (isset($_GET['id']))
						{
							if (isset($_GET['article']))
							{ 
								include('404.shtml'); 
								break;
							}
							
							$id = $_GET['id'];
							switch($id)
							{
								case "info":
									include("info.php");
									break;
								case "contact":
									include("contact.php");
									break;
								case "project":
									include("project.php");
									break;
								case "edit":
									include("edit.php");
									break;
								case "new":
									include("new.php");
									break;
								default:
									include("404.shtml");
									break;
							}
						} elseif(isset($_GET['article']))
						{
							include("article.php");
						} elseif(isset($_GET['upload']))
						{
							include("upload.php");
						} else
						{
							include("main.php");
						}
						
						?>						
					</div>
				</div>
				<div class="sideBar">
					<div class="inlog">
						<?php if(login_check($mysqli) == true){
						$logged = true;
						?>
						
						<h3>Welkom <?php echo $_SESSION['username'];?> </h3>
						<p><a href="index.php?id=new">Nieuw Bericht</a></p>
						<p><a href="index.php?upload">Upload bestand</a></p>
						<p><a href="/includes/logout.php">Uitloggen</a></p>
						
						<?php } else{?>
						Admin?
						<form action="/includes/process_login.php" method="post" name="login-form">
							<div class="inlogrij">
								<div class="label">
									<label for="email">Email:</label>
								</div>
								<div class="inputlogin">
									<input type="text" name="email" id="email">
								</div>
							</div>
							<div>
								<div class="label">
									<label for="password">Password:</label>
								</div>
								<div class="inputlogin">
									<input type="password" name="password" id="password">
								</div>
							</div>
							<input type="button" value="inloggen" onclick="formhash(this.form, this.form.password);" />
						</form>
						<?php }?>
					</div>
					
					<div class="recent">
						<h3>Recente berichten:</h3>
						<div id="recentWindow">
							<?php
							
								$query = 'SELECT * FROM message ORDER BY datum DESC';
								$results = mysqli_fetch_all($mysqli->query($query), MYSQLI_ASSOC);
								$number = 1;
								foreach($results as $result){
									if($number <= 5){ if($result['titel'] != ""){
									?>
										<div class="recent_msg">
											<h4><?= $result['titel']?></h4>
											<p><?php echo bbcode($result['inhoudKort']);?></p>
											<a href="?article=<?=$result['id']?>">Lees Meer</a>
										</div>
									<?php
									}}
									$number +=1;
								}
							
							?>
						</div>
						<div class="slideButton">
							<a id="vorige">Vorige</a> <a id="volgende">Volgende
							</a>
						</div>
					</div>
					<div class="ccsvalm">
						<a target="_blank" href="http://validator.w3.org/check?uri=http%3A%2F%2Fv14ebaalbe.helenparkhurst.net">
							<img class="cssvald" width="100" height="48" title="HTML5 Powered with CSS3 / Styling" alt="HTML5 Powered with CSS3 / Styling" src="http://www.w3.org/html/logo/badge/html5-badge-h-css3.png">
						</a>
						<a href="http://jigsaw.w3.org/css-validator/check/referer">
							<img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Valide CSS!" />
						</a>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>