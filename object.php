<?php

require_once("include/datamodel.class.php");
require_once("include/view.class.php");
require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");

$auth = new Auth(false);

UI::Header($auth);

$user = $auth->getUser();


if(isset($_GET["id"])) {
	
	if(isset($_GET["demo"]))
		$_MYSQLI->query("UPDATE objects SET object_end_date = ".(time()+15)." WHERE object_id = ".$_GET["id"]);
	
	$object_entities = $_MODEL->getEntities("object")
						->where("object_id", "=", $_GET["id"])
						->andWhere("object_statut", "=", 1)
						->limit(1)
						->run();
						
	if($object_entities->size == 1) {
		
		View::Enable("object_exists");	
		
		//Objet
		$object = $object_entities->results[0]["object"];
		
		if($object->object_end_date > time())
			View::Enable("en_cours");	
		
		//Vendeur
		$user_entities = $_MODEL->getEntities("user")
							->where("user_id", "=", $object->object_seller_user_id)
							->limit(1)
							->run();

		$seller_user = $user_entities->results[0]["user"];
		
		
		//Enchères
		$bid_counter = $_MODEL->getEntities("bid")
							->innerJoin("user")
							->on("bid.bid_bidder_user_id", "=", "user.user_id")
							->where("bid_object_id", "=", $object->object_id)
							->count();
			
		$bid_entities = $_MODEL->getEntities("bid")
							->innerJoin("user")
							->on("bid.bid_bidder_user_id", "=", "user.user_id")
							->where("bid_object_id", "=", $object->object_id)
							->limit(5)
							->orderBy(array("bid_date" => "DESC"))
							->run();
							
		if($bid_entities->size == 0)
			$object_price = $object->object_minimal_price;
		else
			$object_price = $bid_entities->results[0]["bid"]->bid_price;
			
		//I Am
		$i_am_auth = $user != NULL;

		
		//Route
		if($i_am_auth) {
			
			$i_am_the_seller = $seller_user->user_id == $user->user_id;
			$i_am_admin = $user->user_rank == 1;
			$i_am_the_best = $bid_entities->size > 0 && $bid_entities->results[0]["user"]->user_id ==  $user->user_id;		
		
			if($i_am_the_seller || $i_am_admin) {
				
				
				View::Enable("delete");
				
				if(isset($_GET["action"]) && $_GET["action"] == "delete") {
					
					$_MYSQLI->query("DELETE FROM bids WHERE bid_object_id = " . $object->object_id);
					$_MYSQLI->query("DELETE FROM objects WHERE object_id = " . $object->object_id);
					@unlink($object->object_picture_url);
					
					header("Location: index.php");
					exit;
					}
				
				if($i_am_the_seller)
					View::Enable("i_am_the_seller");
				

				}
			if(!$i_am_the_seller) {
				
				if($i_am_the_best) {
					View::Enable("i_am_the_best");					
					}
				else {
					if(isset($_POST["prix"]) && (float) $_POST["prix"] > $object_price) {
					
						$price = (float) $_POST["prix"];
					
						$result = $_MODEL->createEntity("bid", array(
							"bid_object_id" => $object->object_id,
							"bid_bidder_user_id" => $user->user_id,
							"bid_price" => round($price, 2),
							"bid_date" => time()
							));
									
						$resultUpdate = $_MODEL->store($result->entity);

						$bid_counter = $_MODEL->getEntities("bid")
											->innerJoin("user")
											->on("bid.bid_bidder_user_id", "=", "user.user_id")
											->where("bid_object_id", "=", $object->object_id)
											->count();
							
						$bid_entities = $_MODEL->getEntities("bid")
											->innerJoin("user")
											->on("bid.bid_bidder_user_id", "=", "user.user_id")
											->where("bid_object_id", "=", $object->object_id)
											->limit(5)
											->orderBy(array("bid_date" => "DESC"))
											->run();	
											
						$i_am_the_best = $bid_entities->size > 0 && $bid_entities->results[0]["user"]->user_id ==  $user->user_id;
						if($i_am_the_best)
							View::Enable("i_am_the_best");	
							
						if($bid_entities->size == 0)
							$object_price = $object->object_minimal_price;
						else
							$object_price = $bid_entities->results[0]["bid"]->bid_price;											

						}
					}

				
				}
			
			}
		else {
			View::Enable("need_connexion");
			}
		

		
		} //end if objet existe
	
	
	}


?>
	<div class="wrap">
		<main>
			<h1>Produit //</h1>
			
			
			<?php View::Display("object_exists", function () { global $object, $seller_user, $object_price, $bid_entities, $bid_counter; ?>

			<div class="column">
				<div class="specproduct leftradius ">
					<div class="specimgproduct" style="background-image: url('<?php echo $object->object_picture_url; ?>');"></div>
					<div class="spectextproduct">
						<div class="spectitleproduct">
							<?php echo $object->object_name; ?>
						</div>
						<div class="specdescproduct">
							<?php echo $object->object_description; ?>
						</div>
						<div class="specsellerproduct">
							Vendu par <b><?php echo $seller_user->user_firstname . " " . $seller_user->user_lastname; ?></b> depuis le <b><?php echo date("d/m/Y", $object->object_start_date); ?></b>
							<?php View::Display("delete", '<a href="object.php?id='.$object->object_id.'&amp;action=delete">[Supprimer]</a>'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="column">
				<div class="specwebinfo rightradius">
					<div class="specwebtext">
						<div class="spectitleinfo">
							<?php View::Display("en_cours", function () { global $object; ?>
							Fin de l'enchère dans
							
							<div id="countdown" class="timer" name="<?php echo $object->object_end_date*1000; ?>">
								Adjugé vendu
							</div>
							
							<?php }, '<div id="countdown">Adjugé vendu</div>'); ?>
						</div>
						
						<h2>
						<?php 
						
						if($bid_counter->size == 1)
							echo "La première enchère";
						else if($bid_entities->size > 1)
							echo "Les $bid_entities->size dernières enchères parmis $bid_counter->size";
						else
							echo "Aucune enchère actuellemnt";
						?>
						</h2>
						
						<?php if($bid_entities->size > 0) { ?>
						<table id="bidtab" style="width:100%">
						  <tr>
							<th>Enchérisseur</th>
							<th>Heure</th>
							<th>Offre</th>
						  </tr>
							<?php
							for($l = $bid_entities->size,$i=$l-1; $i >= 0; $i--) {
								$result = $bid_entities->results[$i];
								echo "<tr>";
								
								echo "<td>" . $result["user"]->user_firstname . " " . $result["user"]->user_lastname . "</td>";	
								
								echo "<td>" . date("d/m/y H:i", $result["bid"]->bid_date) . "</td>";	
								
								echo "<td>" . $result["bid"]->bid_price . " &euro; </td>";
								
								echo "</tr>";
								
							}
							
							?>
						</table> 
						<?php } ?>
						
						<table id="makebid" style="width:100%">
							<tr>
								<td>
								Prix actuel<div id="currentbid"><?php echo $object_price; ?>€</div>
								</td>
								<td>
								
									<?php									
									
									View::Display("en_cours", function () {
										
										View::Display("need_connexion", "Vous devez être connecté pour enchérir", function () {
											
											View::Display("i_am_the_seller", "Votre vente est en cours,<br/> que la Force soit avec vous", function () {
												
												View::Display("i_am_the_best", "Vous êtes le meilleur offreur", function () { global $object, $seller_user, $object_price;
													?>
													<div class="specenchere">
														<form action="object.php?id=<?php echo $object->object_id; ?>" method="post">
															<?php View::Display("prix_error", "Ce prix n'est pas valide"); ?>
															Votre prix<br />
															<input type="text" autofocus name="prix" value="<?php echo $object_price+1; ?>"/>€
															<br><input class="redbutton" id="button" type="submit"  value="Enchérir"/>
														</form>
													</div>
													<?php
													
												});
												
											});											
											
										});
										
									}, function () { //si fini
										
										View::Display("i_am_the_seller", "Votre vente est terminée", function () {
											
											View::Display("i_am_the_best", "Vous avez remporté cette enchère", "Cette vente est terminée");
											
										});


									});
									
									

									?>
									
								</td>
							</tr>
						</table>

						
					</div>
				</div>
			</div>
			
			<div class="clear"></div>
			
		<?php

		}, "Cette objet n'existe pas"); 
			
		?>

		</main>
	</div>



<?php
	
UI::Footer($auth);


?>









