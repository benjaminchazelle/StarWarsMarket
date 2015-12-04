<?php

require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");
require_once("include/view.class.php");

$auth = new Auth(false);

if($auth->isLogged()) {
	header("Location: index.php");
	exit;
	}
else if(Misc::checkArray(array("email", "password"), $_POST)) {
	
	if(Auth::login($_POST["email"], $_POST["password"])) {
		header("Location: index.php");
		exit;
		}
	else {
		View::Enable("error");
		}
	}

UI::Header($auth);

?>
	<div class="wrap">
		<main>

			<h1>Connexion //</h1>
			
			<div class="column">
				<div class="specproduct leftradius" id="loginfobox">


				</div>
			</div>
			<div class="column">
				<div class="specwebinfo rightradius">

					<div id="logform" class="sideform">
					
						<h2>Connexion</h2>
					
						<form action="login.php" method="post">
							<?php View::Display("error", '<div class="error"> <span></span> Erreur de connexion</div>'); ?>

							<div> <span>E-Mail</span> <input type="text" autofocus placeholder="" name="email" value="" /></div>
							<div> <span>Mot de passe</span> <input type="password" placeholder="" name="password" value="" /></div>
							
							<div> <span>&nbsp;</span> <input class="redbutton" type="submit" value="Connexion" /></div>
						</form>
						
						<hr />
						
						<h2> Pas encore inscrit ?</h2>
						
						<form action="register.php" method="get">
							
							<div> <span>Écouter la Force</span> <input class="redbutton" type="submit" value="S'incrire" /></div>

						</form>


					</div>
				</div>
			</div>

			
			<div class="clear"></div>
			


		</main>
	</div>
	
<?php
UI::Footer($auth);
?>