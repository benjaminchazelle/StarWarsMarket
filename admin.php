<?php

require_once("include/datamodel.class.php");
require_once("include/view.class.php");
require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");
require_once("include/phpmailer.class.php");

$auth = new Auth(true);

$user = $auth->getUser();

if($user->user_rank == 1) {
	
	View::Enable("admin_permission");
	
	$purge_stack = $_MODEL->getEntities("object")
					->where("object_statut", "=", 1)
					->andWhere("object_end_date", "<", time())
					->run();
							
		if(isset($_POST["action"]) && $_POST["action"] == "purge") {
			
			$sql_purge = '	SELECT	o.object_name,
									o.object_picture_url,
									u1.user_firstname AS "seller_firstname",
									u1.user_lastname AS "seller_lastname",
									u1.user_email AS "seller_email",
									u1.user_address AS "seller_address",
									u1.user_phone AS "seller_phone",
									u2.user_firstname AS "bidder_firstname",
									u2.user_lastname AS "bidder_lastname",
									u2.user_email AS "bidder_email",
									u2.user_address AS "bidder_address",
									u2.user_phone AS "bidder_phone",
									MAX(b.bid_price) AS "price" 
							FROM objects AS o 
							INNER JOIN bids AS b ON b.bid_object_id = o.object_id 
							INNER JOIN users AS u1 ON u1.user_id = o.object_seller_user_id 
							INNER JOIN users AS u2 ON u2.user_id = b.bid_bidder_user_id 
							WHERE o.object_end_date  < '.time().' AND o.object_statut = 1 
							GROUP BY object_id';
			
			$r = $_MYSQLI->query($sql_purge);
			
			while ($obj = mysqli_fetch_object($r)) {
				
				$phpmailer_toSeller = new PHPMailer;

				$phpmailer_toSeller->addBCC($obj->seller_email, $obj->seller_firstname . " " . $obj->seller_lastname); 

				$phpmailer_toSeller->From = "no-reply@starwarsmarket.com";
				$phpmailer_toSeller->FromName = "Star Wars Market";
				$phpmailer_toSeller->CharSet = "UTF-8";

				$phpmailer_toSeller->isHTML(true);   

				$phpmailer_toSeller->Subject = "Sar Wars Market - Votre enchère est terminée";
				
				$message = "Bonjour $obj->seller_firstname $obj->seller_lastname,\n";
				$message.= "\n";
				$message.= "Vous recevez ce mail car votre objet \"$obj->object_name\" s'est terminée et a trouvé acquéreur !\n";
				$message.= "Pour finaliser la vente, veuillez prendre contact avec l'acheteur :\n";
				$message.= " $obj->bidder_firstname $obj->bidder_lastname\n";
				$message.= " Mail: $obj->bidder_email\n";
				$message.= " Téléphone: $obj->bidder_phone\n";
				$message.= " Adresse: $obj->bidder_address\n";
				$message.= "\n";
				$message.= "À bientôt sur le Star Wars Market ! Et que la Force soit avec vous ~";
				
				$phpmailer_toSeller->Body    = nl2br($message);
				$phpmailer_toSeller->AltBody = $message;

				$phpmailer_toSeller->send();
				
				$phpmailer_toBidder = new PHPMailer;

				$phpmailer_toBidder->addBCC($obj->bidder_email, $obj->bidder_firstname . " " . $obj->bidder_lastname); 

				$phpmailer_toBidder->From = "no-reply@starwarsmarket.com";
				$phpmailer_toBidder->FromName = "Star Wars Market";
				$phpmailer_toBidder->CharSet = "UTF-8";

				$phpmailer_toBidder->isHTML(true);   

				$phpmailer_toBidder->Subject = "Star Wars Market - Vous avez remporté une enchère";
				
				$message = "Bonjour $obj->bidder_firstname $obj->bidder_lastname,\n";
				$message.= "\n";
				$message.= "Vous recevez ce mail car vous avez remporté une enchère sur l'objet \"$obj->object_name\" !\n";
				$message.= "Pour finaliser la vente, veuillez prendre contact avec le vente :\n";
				$message.= " $obj->seller_firstname $obj->seller_lastname\n";
				$message.= " Mail: $obj->seller_email\n";
				$message.= " Téléphone: $obj->seller_phone\n";
				$message.= " Adresse: $obj->seller_address\n";
				$message.= "\n";
				$message.= "À bientôt sur le Star Wars Market ! Et que la Force soit avec vous ~";

				$phpmailer_toBidder->Body    = nl2br($message);
				$phpmailer_toBidder->AltBody = $message;

				$phpmailer_toBidder->send();

			}
			
			
			$_MYSQLI->query("UPDATE objects SET object_statut = 0 WHERE ".time()." > object_end_date");
			
			$purge_stack = null;
			
		}
	
	$users_objects = $_MODEL->getEntities("user")
					->leftJoin("object")
					->on("user.user_id", "=", "object.object_seller_user_id")
					->where("object.object_statut", "=", 1)
					->orWhere("object.object_statut", "IS", NULL)
					->run();
	// echo $users_objects->query;
	$collection = array();
	
	foreach($users_objects->results as $row) {
		
		$uid = $row["user"]->user_id;

		if(!isset($collection[$uid])) {
			$collection[$uid] = array("user" => $row["user"], "objects" => array());
			
			if(isset($row["object"]->object_id))
				$collection[$uid]["objects"][] = $row["object"];
		
		}
		else if ( count($collection[$uid]["objects"]) < 4 && isset($row["object"]->object_id) ) {
			$collection[$uid]["objects"][] = $row["object"];
		}
		
	}
	

		

		if(isset($_POST["action"]) &&$_POST["action"] == "delete" && isset($_POST["user_id"])) {
			
			$uid = (int) $_POST["user_id"];
			
			
			$_MYSQLI->query("DELETE FROM bids WHERE bid_bidder_user_id = ".$uid);
			
			foreach($collection[$uid]["objects"] as $obj) {
				$_MYSQLI->query("DELETE FROM bids WHERE bid_object_id = ".$obj->object_id);
				@unlink($obj->object_picture_url);
			}

			
			$_MYSQLI->query("DELETE FROM objects WHERE object_seller_user_id = ".$uid);
			
			$_MYSQLI->query("DELETE FROM users WHERE user_id = ".$uid);
			
			unset($collection[$uid]);
			
		}
		
	
	

	

	
	
}


UI::Header($auth);

?>
	
	<?php View::Display("admin_permission", function () { global $collection, $purge_stack; ?>
	
	<div class="wrap">
		<main>

			<h1>Base de donnée //</h1>

				<div class="largebox bothradius">
				
					<div id="signform" class="sideform">
					
						<table class="collection">
							<tr>
								<td>
									<h3>La cloturation des ventes supprime de la base les enchères terminées.</h3>									
								</td>
								<td class="buttoncell">
									<form method="post" action="admin.php">
										<input type="hidden" name="action" value="purge" />
										<input class="redbutton" type="submit" value="Clôturer" />
									</form>									
								</td>
							</tr>
						</table>
						<?php if($purge_stack != NULL && $purge_stack->size > 0) { ?>
						<hr />							
						<table class="collection">
							<tr>
								<td>

									<ul>
									<?php foreach($purge_stack->results as $o) { ?>
										
										<li><a href="object.php?id=<?php echo $o["object"]->object_id; ?>"><?php echo $o["object"]->object_name; ?></a></li>

									<?php } ?>
									</ul>
								</td>
							</tr>
						</table>
						<?php } ?>
				

					</div>
					
				</div>
				
				<h1>Gestion des membres //</h1>

				<div class="largebox bothradius">
						
					<div id="signform" class="sideform">
										
						<table class="collection">
							<?php foreach($collection as $u) { ?>
							<tr>
								<td><?php echo $u["user"]->user_firstname . " " . $u["user"]->user_lastname; ?></td>
								<td><?php echo $u["user"]->user_email; ?></td>
								<td>
									<form method="post" action="admin.php">
										<input type="hidden" name="action" value="delete" />
										<input type="hidden" name="user_id" value="<?php echo $u["user"]->user_id; ?>" />
										<input class="redbutton" type="submit" value="Supprimer" />
									</form>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<ul>
										<?php foreach($u["objects"] as $o) { ?>
										
										<li><a href="object.php?id=<?php echo $o->object_id; ?>"><?php echo $o->object_name; ?></a></li>
										
										<?php } ?>
									</ul>
									<hr />
								</td>
							</tr>
							<?php } ?>
						</table>


					</div>
					
				</div>

				<div class="marger"></div>

			</main>
			
		</div>

	<?php }, "Vous n'avez pas le droit d'accéder à cette page"); 

UI::Footer($auth);

?>

