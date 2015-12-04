<?php


require_once("datamodel.class.php");
require_once("database.inc.php");

$_MODEL = new DataModel($_MYSQLI);
				
$_MODEL->defineEntityModel("user", array(
				"user_id" => DataModel::Type("PrimaryKey"),
				"user_firstname" => DataModel::Type("NotEmptyString"),
				"user_lastname" => DataModel::Type("NotEmptyString"),
				"user_email" => DataModel::Type("Email"),
				"user_password" => DataModel::Type("NotEmptyString"),
				"user_address" => DataModel::Type("NotEmptyString"),
				"user_phone" => DataModel::Type("Phone"),
				"user_rank" => DataModel::Type("Rank")
				), "users");
				
$_MODEL->defineEntityModel("object", array(
				"object_id" => DataModel::Type("PrimaryKey"),
				"object_name" => DataModel::Type("NotEmptyString"),
				"object_description" => DataModel::Type("NotEmptyString"),
				"object_picture_url" => DataModel::Type("NotEmptyString"),
				"object_minimal_price" => DataModel::Type("UnsignedFloat"),
				"object_seller_user_id" => DataModel::Type("ForeignKey"),
				"object_start_date" => DataModel::Type("Timestamp"),
				"object_end_date" => DataModel::Type("Timestamp"),
				"object_statut" => DataModel::Type("UnsignedInt")
				), "objects");
				
$_MODEL->defineEntityModel("bid", array(
				"bid_id" => DataModel::Type("PrimaryKey"),
				"bid_object_id" => DataModel::Type("ForeignKey"),
				"bid_bidder_user_id" => DataModel::Type("ForeignKey"),
				"bid_price" => DataModel::Type("UnsignedFloat"),
				"bid_date" => DataModel::Type("Timestamp")
				), "bids");
		
/*
	
$_POST = array("usr" => "chuck", "family" => "noris", "email" => "Carlos@boris.com", "password" => "catonkeyboard", "addr" => "42 rue Internet", "tel" => "0351235139", "rank" => "7");

$result = $_MODEL->createEntity("user", $_POST, array(
					"usr"	=>	"user_firstname",
					"family"	=>	"user_lastname",
					"email"	=>	"user_email",
					"password"	=>	"user_password",
					"addr"	=>	"user_address",
					"tel"	=>	"user_phone",
					"rank"	=>	"user_rank"
					));
					


if($result->validEntity) {
	// $_MODEL->store($result->entity);
}
else {
	print_r($result->errors);
}	
	
	
	

$user_entities = $_MODEL->getEntities("user")
					->where("user_id", "!=", 78)
					->andWhere("user_email", "NOT LIKE", "%@sfr.fr")
					->groupBy(array("user_lastname"))
					->orderBy(array("user_lastname" => "DESC", "user_firstname" => "ASC"))
					->offset(1)
					->limit(3)
					->run();
	
$user_entity = $user_entities->result[0];
$user_entity->user_rank = 42;


$_MODEL->store($user_entity);

$rem = $_MODEL->remove($user_entity);

*/
?>