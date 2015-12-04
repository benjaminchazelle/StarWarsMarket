<?php

require_once("include/datamodel.class.php");
require_once("include/view.class.php");
require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");

$auth = new Auth(false);

UI::Header($auth);

$resultTop = $_MYSQLI->query('SELECT object.*, MAX(bid.bid_price) AS "price" FROM bids AS bid INNER JOIN objects AS object ON bid.bid_object_id = object.object_id WHERE object_statut = 0 GROUP BY object_id ORDER BY price DESC LIMIT 1');

if($row = $resultTop->fetch_array(MYSQLI_ASSOC)) {
	
$topObject = $_MODEL->createEntity("object", $row, array(), false)->entity;
$topObject->object_minimal_price = $row["price"];
}
else {
$topObject = new stdClass;
$topObject->object_minimal_price = "";
$topObject->object_picture_url = "images/alongtimeago.jpg";
$topObject->object_name = "À venir...";

	
}




$resultNews = $_MYSQLI->query('SELECT object.*, MAX(bid.bid_price) "price" FROM objects AS object LEFT JOIN bids AS bid ON bid.bid_object_id = object.object_id WHERE object_end_date > '.time().' AND object_statut = 1 GROUP BY object_id ORDER BY object_start_date DESC, object_id DESC LIMIT 4');	


$objects = array();
while ($row = $resultNews->fetch_array(MYSQLI_ASSOC))
{
	$resultObject = $_MODEL->createEntity("object", $row, array(), false);
	
	$entity = $resultObject->entity;
	
	if(!is_null($row["price"]))
		$entity->object_minimal_price = $row["price"];
  
	$objects[] = $entity;
}
	
					
					


?>



	<div class="wrap">
		<main>
			<h1>Accueil //</h1>
			
			<div class="column">
				<div class="bestseller">
					<div class="bestimgproduct" style="background-image: url('<?php echo $topObject->object_picture_url; ?>');">MEILLEURE VENTE DE LA GALAXIE</div>
					<div class="textproduct">
						<div class="titleproduct">
							<?php echo $topObject->object_name; ?>
						</div>
						<div class="priceproduct">
							<?php echo $topObject->object_minimal_price; echo ($topObject->object_minimal_price != "") ? "€" : ""; ?>
						</div>
					</div>	
				</div>
			</div>
			<div class="column">
				<div class="webinfo">
					<div class="webtext">
						<div class="titleinfo">
							Que la force soit avec vous !
						</div>
						<div class="contentinfo">
							L'univers de Star Wars se déroule dans une galaxie qui se fait le théâtre d'affrontements entre Chevaliers Jedi et Seigneurs Sith, des êtres sensibles à la Force, un champ énergétique mystérieux leur procurant des pouvoirs psychiques. Les Jedi, du Côté lumineux de la Force, s'en servent à des fins bénéfiques là où les Sith utilisent le Côté obscur pour détruire.
							<br>
							<br>Besoin de droides, de chasseurs de prime ou encore d'esclaves de toutes la galaxie ? Vous êtes au bon endroit.
							<br />Que la Force soit avec vous !
						</div>
					</div>
				</div>
			</div>
			
			<div class="containercenter">
				<div class="new_objects">
					<div class="webtext">
						<div class="titlenew">
							NOUVEAUX PRODUITS
						</div>
						<div class="contentnew">
						
							<?php for($i=0;$i<count($objects);$i++) { $object = $objects[$i]; ?>
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
							
						</div>
					</div>
				</div>
			</div>	
			
			<div class="clear"></div>
			
		</main>
	</div>


<?php
UI::Footer($auth);
?>