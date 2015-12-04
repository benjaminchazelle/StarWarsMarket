<?php

require_once("include/datamodel.class.php");
require_once("include/view.class.php");
require_once("include/auth.class.php");
require_once("include/misc.lib.php");
require_once("include/ui.inc.php");

$auth = new Auth(false);

UI::Header($auth);

if(isset($_GET["sort"]))
	View::Enable("search_sort_".$_GET["sort"]);
else
	$_GET["sort"] = "";

if(!isset($_GET["search"]))
	$_GET["search"] = "";

$searchterm = !empty($_GET["search"]) ? "%" . $_GET["search"] . "%" : "%";

$page = isset($_GET["page"]) ? (int) $_GET["page"] : 0;

if($page < 0)
	$page = 0;

$blocksize = 8;

$object_counter = $_MODEL->getEntities("object")
					->where("object_name", "LIKE", $searchterm)
					->andWhere("object_statut", "=", 1)
					->andWhere("object_start_date", "<", time())
					->andWhere("object_end_date", ">", time())
					->count();
					
$totals = ($object_counter->size > 0) ? ($object_counter->size-1) : 0;
					
$maxpage = floor($totals / $blocksize);

if($page > $maxpage)
	$page = $maxpage;


					

$orderBy = "";

if($_GET["sort"] == "name_asc")
	$orderBy = "ORDER BY object_name ASC";

if($_GET["sort"] == "name_desc")
	$orderBy = "ORDER BY object_name DESC";

if($_GET["sort"] == "start_asc")
	$orderBy = "ORDER BY object_start_date ASC, object_id ASC";

if($_GET["sort"] == "start_desc")
	$orderBy = "ORDER BY object_start_date DESC, object_id DESC";

if($_GET["sort"] == "end_asc")
	$orderBy = "ORDER BY object_end_date ASC";

if($_GET["sort"] == "end_desc")
	$orderBy = "ORDER BY object_end_date DESC";


	
$pager = array();

$pager[0] = true;

if(($page-3) > 0)	
	$pager[$page-3] = false;

if(($page-2) > 0)	
	$pager[$page-2] = true;

if(($page-1) > 0)	
	$pager[$page-1] = true;

$pager[$page] = true;

if(($page+1) < $maxpage)	
	$pager[$page+1] = true;

if(($page+2) < $maxpage)	
	$pager[$page+2] = true;

if(($page+3) < $maxpage)	
	$pager[$page+3] = false;

$pager[0] = true;

$pager[$maxpage] = true;	


$sql = 'SELECT object.*, MAX(bid.bid_price) "price" FROM objects AS object LEFT JOIN bids AS bid ON bid.bid_object_id = object.object_id WHERE object_statut = 1 AND object_end_date > '.time().' AND object_start_date < '.time().' AND object_name LIKE \''.$_MYSQLI->real_escape_string($searchterm).'\' GROUP BY object_id '.$orderBy.' LIMIT '.$blocksize.' OFFSET ' . $blocksize * $page;

$result = $_MYSQLI->query($sql);	

$objects = array();

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	$resultObject = $_MODEL->createEntity("object", $row, array(), false);
	
	$entity = $resultObject->entity;
	
	if(!is_null($row["price"]))
		$entity->object_minimal_price = $row["price"];
  
	$objects[] = $entity;
	}


?>



	<div class="wrap">
		<main>
			<h1>Produits //</h1>

			<form method="get" action="listObjects.php">

				<div id="searchbar">
					<input type="text" name="search" value="<?php echo htmlentities($_GET["search"]); ?>" placeholder="Qu'est ce que tissa veut que missa recherche ?"/>
				
					<select name="sort">
						<option <?php View::Display("search_sort_name_asc", "selected"); ?> value="name_asc">Nom A-Z</option>
						<option <?php View::Display("search_sort_name_desc", "selected"); ?> value="name_desc">Nom Z-A</option>
						
						<option <?php View::Display("search_sort_start_desc", "selected"); ?> value="start_desc">Plus récent</option>
						<option <?php View::Display("search_sort_start_asc", "selected"); ?> value="start_asc">Moins récent</option>
						
						<option <?php View::Display("search_sort_end_asc", "selected"); ?> value="end_asc">Fin proche</option>
						<option <?php View::Display("search_sort_end_desc", "selected"); ?> value="end_desc">Fin lointaine</option>
					</select>
				
					<input type="submit"  value="search"/>
				</div>

			</form>			

			
			<div class="row">

			<?php for($i=0;$i<count($objects);$i++) { $object = $objects[$i]; ?>

				<a href="object.php?id=<?php echo $object->object_id; ?>"><div class="block">
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
				</div></a>
				
			<?php } ?>

			
			</div>
			
			<div class="clear"></div>
			
			<div class="navigation">
				<nav>
					<ul class="pagination">
					
					<?php

					foreach($pager as $pagenumber => $p) {
						if($p) {
							if($pagenumber != $page)
								echo '<li><a href="listObjects.php?search='.urlencode($_GET["search"]).'&amp;sort='.urlencode($_GET["sort"]).'&amp;page='.$pagenumber.'">'.$pagenumber.'</a></li>';
							else
								echo '<li class="currentPage"><a href="listObjects.php?search='.urlencode($_GET["search"]).'&amp;sort='.urlencode($_GET["sort"]).'&amp;page='.$pagenumber.'">'.$pagenumber.'</a></li>';
							
						}
						else {
							echo "<li><a>...</a></li>";
							
						}
						
						echo "\n";
					}

					?>
					</ul>
				</nav>
			</div>
			
		</main>
	</div>
	

<?php

UI::Footer($auth);

?>