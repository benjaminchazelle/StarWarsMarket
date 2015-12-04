<?php

require_once("../include/database.inc.php");

$sql = file_get_contents("query.sql");

$yesterday_midnight = DateTime::createFromFormat('D M d Y  H i s', date("D M d Y", time()-60*60*24) . " 00 00 00")->format('U');

$sql .= "UPDATE objects SET object_start_date = $yesterday_midnight;\n";
$sql .= "UPDATE objects SET object_end_date = (((object_id%10) * 60*60*24) + $yesterday_midnight);\n";
$sql .= "UPDATE objects SET object_statut = 0 WHERE object_id = 51;\n";

$update = $_MYSQLI->multi_query($sql);

$files = scandir('static');

foreach($files as $file) {

	if(is_file("static/".$file) && !is_file("../static/".$file)) {
		copy("static/".$file, "../static/".$file);
	}
	
}

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Star Wars Market</title>
    <link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/datepicker.css">
  </head>
  <body>
    <header>
		<div class="wrap">
			<nav class="left">
				<a href="../index.php">Accueil</a>
				<a href="../listObjects.php">Produits</a>
				<a href="../addObject.php">Vendre</a>
			</nav>
			<nav class="right"><a href="../login.php">Connexion</a>			</nav>
		</div>
	</header>
	<div class="wrap">
		<main>

			<h1>Réinisitalisation //</h1>
			
				<div class="largebox bothradius">
				
					<div id="signform" class="sideform" style="text-align:center;">
						<h3>
						<?php
						if($update) {
							echo "La réinitialisation de la base de donnée s'est déroulée avec succès !";
						}
						else {
							echo "Il y a eu un problème avec la réinitialisation de la base de donnée ='(";
						}
						
						?>
						
						</h3>
						
						<div class="clear"></div>
					</div>

				</div>
			
			<div class="clear"></div>
			


		</main>
	</div>

	<footer>
	</footer>
	
  </body>
</html>






