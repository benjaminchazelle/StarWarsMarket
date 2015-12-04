<?php

require_once("include/datamodel.class.php");
require_once("include/view.class.php");
require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");

$auth = new Auth(true);
$user = $auth->getUser();

$objects = $_MODEL->getEntities("object")
					->where("object_seller_user_id", "=", $user->user_id)
					->andWhere("object_statut", "=", 1)
					->limit(4)
					->run();
			
if($objects->size > 0)		
	View::Enable("ventes_en_cours");
	
if($objects->size < 4)
	View::Enable("ajout_disponible");

if($objects->size < 4 && Misc::checkArray(array("nom", "description", "prix_minimum", "date_debut", "date_fin"), $_POST)) {

	$valid_date = false;
	$startdate = -1;
	$enddate = -1;
	$startdate_instance = DateTime::createFromFormat('D M d Y H i s', $_POST["date_debut"] . "00 00 00");
	$enddate_instance = DateTime::createFromFormat('D M d Y  H i s', $_POST["date_fin"] . "00 00 00");

	if($startdate_instance instanceof DateTime && $enddate_instance instanceof DateTime) {
		$startdate = $startdate_instance->format('U');
		$enddate = $enddate_instance->format('U');

		$valid_date = $startdate <= ($enddate - 60*60*24);
		}

	$uri =  isset($_FILES['photo']['name']) ? "static/" . time() . "-" . $_FILES['photo']['name'] : "";
	$valid_file = isset($_FILES["photo"]) && is_uploaded_file($_FILES["photo"]["tmp_name"]) && in_array($_FILES["photo"]["type"], array("image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp")) && move_uploaded_file($_FILES['photo']['tmp_name'], $uri);

	$result = $_MODEL->createEntity("object", array(
				"object_name" => htmlentities($_POST["nom"]),
				"object_description" => htmlentities($_POST["description"]),
				"object_picture_url" => $uri,
				"object_minimal_price" => $_POST["prix_minimum"],
				"object_seller_user_id" => $user->user_id,
				"object_start_date" => $startdate,
				"object_end_date" => $enddate,
				"object_statut" => 1
				));
				
	if($valid_date && $valid_file && $result->validEntity) {
		
		$resultInsert = $_MODEL->store($result->entity);
		
		header("Location: object.php?id=" . $resultInsert->lastInsertId);
		exit;	
		}
	else {
		
		@unlink($uri);
	
		if(!$valid_date)
			View::Enable("date_error");
		
		if(!$valid_file)
			View::Enable("photo_error");
	
		if(in_array("object_name", $result->errors["integrity"]))
			View::Enable("nom_error");
		
		if(in_array("object_description", $result->errors["integrity"]))
			View::Enable("description_error");
		
		if(in_array("object_minimal_price", $result->errors["integrity"]) || $_POST["prix_minimum"] == "" || !is_numeric($_POST["prix_minimum"]))
			View::Enable("prix_minimum_error");
			
		}
					

	}



UI::Header($auth);

?>
	<div class="wrap">
		<main>

			<h1>Vendre //</h1>
			
				<div class="largebox bothradius">
				
					<div id="signform" class="sideform">
						<?php View::Display("ajout_disponible", function () { ?>
				
						<h2>Ajouter un objet</h2>
					
						<form action="addObject.php" method="post" enctype="multipart/form-data">
						<div class="column">

							<i class="error">
								<?php 
								View::Display("nom_error", "Le nom ne peut être vide", function () {
									 View::Display("description_error", "La description ne peut être vide", function () {
										 View::Display("photo_error", "Cette photo est incorrect", function () {
											 View::Display("prix_minimum_error", "Ce prix n'est pas valide", function () {
												 View::Display("date_error", "La période est invalide");
											 });
										 });
									 });
								}); 
								?>
							</i>
							
							<div>
								<span <?php View::Display("nom_error", 'class="error"'); ?>>Nom</span> 
								<input type="text" autofocus placeholder="" name="nom" value="" />
							</div>
							
							<div>
								<span <?php View::Display("photo_error", 'class="error"'); ?>>Photo</span> 
								<input type="file" placeholder="" name="photo" />
							</div>
														
							<div>
								<span <?php View::Display("description_error", 'class="error"'); ?>>Description</span>
								<textarea type="text" placeholder="" name="description"></textarea>
							</div>
							
							</div>
							<div class="column">
														
							<div>
								<span <?php View::Display("prix_minimum_error", 'class="error"'); ?>>Prix minimum</span>
								<input type="text" placeholder="" name="prix_minimum" value="" />
							</div>
														
							<div>
								<span <?php View::Display("date_error", 'class="error"'); ?>>Date de début (00h00)</span> 
								<input id="date_debut" type="text" placeholder="" name="date_debut" value="<?php echo date("D M d Y"); ?>" />
							</div>
														
							<div>
								<span <?php View::Display("date_error", 'class="error"'); ?>>Date de fin (00h00)</span>
								<input id="date_fin" type="text" placeholder="" name="date_fin" value="<?php echo date("D M d Y", time() + 60*60*24); ?>" />
							</div>

							
							
							<div> <span>&nbsp;</span> <input class="redbutton" type="submit" value="Déposer" /></div>
							
							<script src="js/datepicker.js"></script>
							<script>
								now = (new Date()).getTime();

								enddate = new Pikaday({ field: document.getElementById('date_fin'), minDate : new Date(now+60*60*24*1000), });
								
								startdate = new Pikaday({ field: document.getElementById('date_debut'), minDate : new Date(),         onSelect: function() {
									enddate.setMinDate(new Date(startdate.getDate().getTime()+60*60*24*1000));
									
									if(enddate.getDate().getTime() <= (startdate.getDate().getTime() + 60*60*24))
										enddate.setDate(new Date(startdate.getDate().getTime()+60*60*24*1000));
									
								} });

							</script>

						</div>
									<div class="clear"></div>

						</form>

					<?php }, '<h3>Vous ne pouvez pas vendre plus de 4 objets  en même temps.</h3><div class="clear"></div>') ?>

					</div>

				</div>
			
			<?php View::Display("ventes_en_cours", function () { global $objects; ?>
			<h1>Vos ventes en cours //</h1>
			
				<?php for($i=0;$i<$objects->size;$i++) { $object = $objects->results[$i]["object"]; ?>	

				<a href="object.php?id=<?php echo $object->object_id; ?>">
					<div class="block">
						<div class="imgproduct" style="background-image: url('<?php echo $object->object_picture_url; ?>');"></div>
						<div class="textproduct">
							<div class="titleproduct">
								<?php echo $object->object_name; ?>
							</div>
							<div class="countdown timer" name="<?php echo $object->object_end_date*1000; ?>"></div>
							<div class="priceproduct">
								<?php echo $object->object_minimal_price; ?>€
							</div>
						</div>
					</div>
				</a>
				
				<?php } ?>
			
			<?php }); ?>

			
			<div class="clear"></div>
			


		</main>
	</div>
<?php
UI::Footer($auth);
?>







