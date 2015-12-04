<?php

require_once("include/datamodel.class.php");
require_once("include/view.class.php");
require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");

$auth = new Auth(false);

View::Enable("form");

if($auth->isLogged()) {
	header("Location: index.php");
	exit;
	}
else if(Misc::checkArray(array("prenom", "nom", "email", "adresse", "telephone", "password", "repassword"), $_POST)) {
	
	$_POST["rank"] = 0;
	
	$password_clear = $_POST["password"];
	
	$_POST["password"] = sha1("ordre66".$_POST["password"]);
	
	$result = $_MODEL->createEntity("user", array(
						"user_firstname"	=>	htmlentities($_POST["prenom"]),
						"user_lastname"	=>	htmlentities($_POST["nom"]),
						"user_email"	=>	htmlentities($_POST["email"]),
						"user_password"	=>	$_POST["password"],
						"user_address"	=>	htmlentities($_POST["adresse"]),
						"user_phone"	=>	$_POST["telephone"],
						"user_rank"	=>	$_POST["rank"]	
						));
						
					
			
	if($result->validEntity) {
		
		if($password_clear == $_POST["repassword"]) {
			
			$user_entities = $_MODEL->getEntities("user")
								->where("user_email", "=", $_POST["email"])
								->limit(1)
								->run();
								
			if($user_entities->size == 0) {
				$_MODEL->store($result->entity);
				Auth::login($_POST["email"], $password_clear);
				
				header("Location: index.php");
				exit;
				

			}
			else {
			
			View::Enable("unique_email_error");

				
			}

		}
		else {
			View::Enable("repassword_error");
		}
		
		
	}
	else {
		if(in_array("user_firstname", $result->errors["integrity"]))
			View::Enable("prenom_error");
		
		if(in_array("user_lastname", $result->errors["integrity"]))
			View::Enable("nom_error");
		
		if(in_array("user_email", $result->errors["integrity"]))
			View::Enable("email_error");
		
		if(in_array("user_password", $result->errors["integrity"]))
			View::Enable("password_error");
		
		if(in_array("user_address", $result->errors["integrity"]))
			View::Enable("adresse_error");
		
		if(in_array("user_phone", $result->errors["integrity"]))
			View::Enable("telephone_error");
	}
						
						

	
}


UI::Header($auth);

?>
	<div class="wrap">
		<main>

			<h1>Inscription //</h1>
			
			<div class="column">
				<div class="specproduct leftradius" id="signinfobox">


				</div>
			</div>
			<div class="column">
				<div class="specwebinfo rightradius">

					<div id="signform" class="sideform">
					
						<h2>Inscription</h2>
					
						<form action="register.php" method="post">

							<i class="error">
								<?php 
								View::Display("prenom_error", "Le prénom ne peut être vide", function () {
									 View::Display("nom_error", "Le nom ne peut être vide", function () {
										 View::Display("unique_email_error", "Cette e-mail est déjà utilisée", function () {
											 View::Display("email_error", "Cette e-mail n'est pas valide", function () {
												 View::Display("adresse_error", "L'adresse ne peut être vide !", function () {
													 View::Display("telephone_error", "Ce numéro n'est pas valide", function () {
														 View::Display("password_error", "Ce mot de passe n'est pas valide", function () {
															 View::Display("repassword_error", "Les mots de passe ne coincident pas");
														 });
													 });
												 });
											 });
										 });
									 });
								}); 
								?>
							</i>
							
							<div>
								<span <?php View::Display("prenom_error", 'class="error"'); ?>>Prénom</span> 
								<input type="text" autofocus placeholder="" name="prenom" value="" />
							</div>
														
							<div>
								<span <?php View::Display("nom_error", 'class="error"'); ?>>Nom</span>
								<input type="text" placeholder="" name="nom" value="" />
							</div>
														
							<div>
								<span <?php View::Display("email_error", 'class="error"'); View::Display("unique_email_error", ' class="error"'); ?>>E-Mail</span>
								<input type="text" placeholder="" name="email" value="" />
							</div>
														
							<div>
								<span <?php View::Display("adresse_error", 'class="error"'); ?>>Adresse</span> 
								<input type="text" placeholder="" name="adresse" value="" />
							</div>
														
							<div>
								<span <?php View::Display("telephone_error", 'class="error"'); ?>>Téléphone</span>
								<input type="text" placeholder="" name="telephone" value="" />
							</div>
							
							<div>
								<span <?php View::Display("password_error", 'class="error"'); View::Display("repassword_error", ' class="error"'); ?>>Mot de passe</span> 
								<input type="password" placeholder="" name="password" value="" />
							</div>
							
							<div>
								<span <?php View::Display("repassword_error", 'class="error"'); ?>>Mot de passe bis</span> 
								<input type="password" placeholder="" name="repassword" value="" />
							</div>
							
							
							<div> <span>&nbsp;</span> <input class="redbutton" type="submit" value="S'incrire" /></div>
							

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