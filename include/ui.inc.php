<?php

class UI {
	
static function Header ($auth) {
	
	$u = $auth->getUser();
	
	
echo '
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Star Wars Market</title>
    <link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/datepicker.css">
  </head>
  <body>
    <header>
		<div class="wrap">
			<nav class="left">
				<a href="index.php">Accueil</a>
				<a href="listObjects.php">Produits</a>
				<a href="addObject.php">Vendre</a>
			</nav>
			<nav class="right">';
			
			if($u != NULL) {
				
				if($u->user_rank == 1)
					echo '<a href="admin.php">Zone Admin</a>';
				else
					echo $u->user_firstname{0} . '. ' . $u->user_lastname;
			
				echo '<a href="logout.php">Déconnexion</a>';
			}
			else {
				echo '<a href="login.php">Connexion</a>';
			}

echo '			</nav>
		</div>
	</header>
';

}	


static function Footer ($auth) {
	
echo '
	<script src="js/timer.js"></script>
	<footer>
	</footer>
	
  </body>
</html>';

}

};

?>