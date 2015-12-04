<?php

/////////////////////
//DataModel    v1.2//
//Benjamin Chazelle//
//Licence  CC-BY-NC//
/////////////////////

class DataModel{
	
	static $dataTypes = array();
	
	static function RegisterType($typeName, $typeCast, $typeCheck, $nullAtInsertion = false) {
		
		self::$dataTypes[$typeName] = array("cast" => $typeCast, "check" => $typeCheck, "nullAtInsertion" => $nullAtInsertion);
	}
	
	static function RegisterTypeAs($typeName, $typeNameReference) {
		
		if(isset(self::$dataTypes[$typeNameReference]))
			self::$dataTypes[$typeName] = self::$dataTypes[$typeNameReference];
	}
	
	static function Type($typeName) {
		
		if(!isset(self::$dataTypes[$typeName])) {
			echo "$typeName type doesn't exists";
			exit;
		}
		
		return self::$dataTypes[$typeName];
	}
	
	private $mysqli = null;
	
	private $entitiesDefinition = array();
	private $entitiesTable = array();
	
	
	public function __construct($_mysqli) {
		$this->mysqli = $_mysqli;
	}
	
	public function defineEntityModel ($entityName, $entityDefinition, $entityTable) {
		
		$this->model[$entityName] = array(	"definition" => $entityDefinition,
											"table" => $entityTable
										);
		
	}
	
	public function checkEntityFieldIntegrity($entityName, $entityFieldName, $testValue) {
		

		
		$castFunction = $this->model[$entityName]["definition"][$entityFieldName]["cast"];
		$check = $this->model[$entityName]["definition"][$entityFieldName]["check"]($testValue, $castFunction);
		
		if($entityFieldName == "user_id")
			index($this->model[$entityName]["definition"]) ;		
		
		return $check;
	
	}

	public function createEntity ($entityName, $source, $route = array(), $creation = true) {

		$result = new stdClass;
		
		$result->errors = array("routing" => array(), "integrity" => array());
		$result->validEntity = false;
		$result->entity = null;
		
		$entityBase = array();
		
		foreach($source as $streamFieldName => $streamFieldValue) {
			
			if(isset($route[$streamFieldName]) && isset($this->model[$entityName]["definition"][$route[$streamFieldName]])) {
				
				$entityBase[$route[$streamFieldName]] = $streamFieldValue;
			}
			else if(isset($this->model[$entityName]["definition"][$streamFieldName])){
				$entityBase[$streamFieldName] = $streamFieldValue;
			}
			else {
				$result->errors["routing"][] = $streamFieldName;
			}

		}

		
		$entityInstance = new stdClass;
		
		foreach($this->model[$entityName]["definition"] as $entityFieldName => $entityFieldDefinition) {
			
			if($creation && $entityFieldDefinition["nullAtInsertion"]) {
				
				$entityInstance->{$entityFieldName} = null;
				
			}
			else if(isset($entityBase[$entityFieldName]) && $this->checkEntityFieldIntegrity($entityName, $entityFieldName, $entityBase[$entityFieldName])){
				
				$entityInstance->{$entityFieldName} = $entityBase[$entityFieldName];		
				
			}
			else {
				$result->errors["integrity"][] = $entityFieldName;
				
			}
		}	
		if(count($result->errors["integrity"]) == 0) {
			$result->validEntity = true;	
			$entityInstance->{":type"} = $entityName;
			$result->entity = $entityInstance;
		}



		
		return $result;
		
	}
	
	public function getEntityModel($entityName) {
		
		if(isset($this->model[$entityName])) {
			return $this->model[$entityName];
		}
		else{
			return null;
		}
		
	}

	public function getEntities ($entityName) {
		return new EntityQuery($this, $entityName);
	}

	public function store ($entity) {
		
		$result = new stdClass;
		
		$result->errors = array();
		$result->queryState = null;
		$result->lastInsertId = null;
		
		$obj = new ReflectionObject($entity);
		
		if($obj->hasProperty(":type")) {
			$entityName = $entity->{":type"};			
		}
		else {
			$result->errors[] = ":type";
		}
		
		$sql_insert_head = 'INSERT INTO '.$this->model[$entityName]["table"].' VALUES (';
		$sql_update_head = 'UPDATE '.$this->model[$entityName]["table"].' SET ';
		
		$sql_insert_values = $sql_update_values = "";
		
		$sql_update_where = "WHERE ";
		
		$insertion = true;
		
		foreach($this->model[$entityName]["definition"] as $entityFieldName => $entityFieldDefinition) {
			
			if($obj->hasProperty($entityFieldName)) {

				if(!$entityFieldDefinition["nullAtInsertion"]) {
					
					$sql_insert_values .=  "'" . mysqli_real_escape_string($this->mysqli, $entity->{$entityFieldName}) . "', ";
					
					
					$sql_update_values .=  $entityFieldName . " = '" . mysqli_real_escape_string($this->mysqli, $entity->{$entityFieldName}) . "', ";
				}
				else {
					
					if($entity->{$entityFieldName} != null)
						$insertion = false;
					
					$sql_insert_values .= "NULL, ";
					
					$sql_update_where .= $entityFieldName . " = '" . mysqli_real_escape_string($this->mysqli, $entity->{$entityFieldName}) . "', ";
				}

				
			}
			else {
				$result->errors[] = $entityFieldName;
			}
		}
		
		$sql_insert = $sql_insert_head . rtrim($sql_insert_values, ", ") . ")";
		$sql_update = $sql_update_head . rtrim($sql_update_values, ", ") . " " . rtrim($sql_update_where, ", ");

		
		$sql = $insertion ? $sql_insert : $sql_update;
		
		$result->queryState = $this->mysqli->query($sql);
		
		if($insertion)
			$result->lastInsertId = $this->mysqli->insert_id;
		
		return $result;
	}

	public function remove ($entity) {
		$result = new stdClass;
		
		$result->errors = array();
		$result->queryState = null;
		
		$obj = new ReflectionObject($entity);
		
		if($obj->hasProperty(":type")) {
			$entityName = $entity->{":type"};			
		}
		else {
			$result->errors[] = ":type";
		}
		
		$sql_delete_head = 'DELETE FROM '.$this->model[$entityName]["table"].' WHERE ';
		$sql_update_where = '';

		
		foreach($this->model[$entityName]["definition"] as $entityFieldName => $entityFieldDefinition) {
			
			if($obj->hasProperty($entityFieldName)) {

				if($entityFieldDefinition["nullAtInsertion"]) {
					
					$sql_update_where .= $entityFieldName . " = '" . mysqli_real_escape_string($this->mysqli, $entity->{$entityFieldName}) . "', ";
				}

				
			}
			else {
				$result->errors[] = $entityFieldName;
			}
		}
		
		$sql = $sql_delete_head . rtrim($sql_update_where, ", ");
		
		// echo $sql;
		
		$result->queryState = $this->mysqli->query($sql);
		
		return $result;
	}
	
	public function getMySQLi() {
		return $this->mysqli;
	}

	
};

class EntityQuery{
	
	private $datamodel = null;
	private $mysqli = null;
	
	private $entityName = null;
	private $entityTable = null;
	private $entityDefinition = null;
	
	private $mainEntity = null;
	private $entities = array();
	
	private $statement_join = "";
	private $statement_where = "";
	private $statement_groupby = "";
	private $statement_orderby = "";
	private $statement_offset = "";
	private $statement_limit = "";
	
	public function __construct(&$datamodel, $entityName) {

		$this->datamodel = $datamodel;
		$this->mysqli = $datamodel->getMySQLi();
		$this->entityName = $entityName;
		
		$this->mainEntity = $entityName;
		$this->entities[$entityName] = $datamodel->getEntityModel($entityName);

	}
	
	private function sanitize($value) {
		
		if(is_null($value))
			return "NULL";
		else
			return '"' . $this->mysqli->real_escape_string($value) . '"';
		
	}
	
	public function innerJoin($entityName) {

		$this->entities[$entityName] = $this->datamodel->getEntityModel($entityName);
		
		
		$this->statement_join .= " INNER JOIN " . $this->entities[$entityName]["table"] . " AS " . $entityName;
		
		return $this;
	}
	
	public function leftJoin($entityName) {

		$this->entities[$entityName] = $this->datamodel->getEntityModel($entityName);
		
		
		$this->statement_join .= " LEFT JOIN " . $this->entities[$entityName]["table"] . " AS " . $entityName;
		
		return $this;
	}
	
	public function on($leftOperand, $operator, $rightOperand) {
		
		$this->statement_join .= " ON " . $leftOperand . " " . $operator . $rightOperand;
		
		return $this;
	}
	
	public function where($leftOperand, $operator, $rightOperand) {
		
		$this->statement_where .= $leftOperand . " " . $operator . " " . $this->sanitize($rightOperand);
		
		return $this;
	}
	
	public function andWhere($leftOperand, $operator, $rightOperand) {
		
		$this->statement_where .= " AND " . $leftOperand . " " . $operator . " " . $this->sanitize($rightOperand);
		
		return $this;
	}
	
	public function orWhere($leftOperand, $operator, $rightOperand) {
		
		$this->statement_where .= " OR " . $leftOperand . " " . $operator . " " . $this->sanitize($rightOperand);

		return $this;
	}
	
	public function groupBy($fields) {
		
		$this->statement_groupby = implode(", ", $fields);

		return $this;
	}
	
	public function orderBy($fields) {
		
		$this->statement_orderby = "";
		
		foreach($fields as $fieldName => $sortMode) {
			$this->statement_orderby .= $fieldName . " " . $sortMode . ", ";
		}
		
		$this->statement_orderby = rtrim($this->statement_orderby, ", ");

		return $this;
	}
	
	public function offset($value) {
		
		$this->statement_offset = (string) $value;		

		return $this;
	}
	
	public function limit($value) {
		
		$this->statement_limit = (string) $value;

		return $this;
	}	
	
	
	public function run () {
		
		$result = new stdClass;
		
		$result->fieldErrors = array();
		$result->results = array();
		$result->size = -1;
		$result->query = "";
		
		
		$sql = "SELECT *";
		
		$sql .= " FROM " . $this->entities[$this->mainEntity]["table"] . " AS " . $this->mainEntity;
		
		if($this->statement_join != "")
			$sql .= $this->statement_join;
		if($this->statement_where != "")
			$sql .= " WHERE " . $this->statement_where;
		if($this->statement_groupby != "")
			$sql .= " GROUP BY " . $this->statement_groupby;
		if($this->statement_orderby != "")
			$sql .= " ORDER BY " . $this->statement_orderby;
		if($this->statement_limit != "")
			$sql .= " LIMIT " . $this->statement_limit;
		if($this->statement_offset != "")
			$sql .= " OFFSET " . $this->statement_offset;
		
		$result->query = $sql;
		
		$result->queryState = $this->mysqli->query($sql);
		
		if($result->queryState) {


			while($row = $result->queryState->fetch_array(MYSQLI_ASSOC)) {

				
				
				$instances = array();

				foreach($this->entities as $entityName => &$entity) {
					$instances[$entityName] = new stdClass;
					$instances[$entityName]->{":type"} = $entityName;


					foreach($entity["definition"] as $entityFieldName => $entityFieldDefinition) {
						
						if(isset($row[$entityFieldName])) {
							$instances[$entityName]->{$entityFieldName} = $row[$entityFieldName];
							
						}
						else {
							$result->fieldErrors[$entityFieldName] = $entityFieldName;
						}
						
						
					}
				
				}
				
				
				$result->results[] = $instances;
				
				
				
			}
			
			$result->size = count($result->results);
			
			
		
		}
		else {
			echo "Query error: $sql";
		}
			
		return $result;
	}
	public function count () {
		
		$result = new stdClass;
		
		$result->fieldErrors = array();
		$result->results = array();
		$result->size = -1;
		$result->query = "";
		
		
		$sql = "SELECT COUNT(*) AS size";
		
		$sql .= " FROM " . $this->entities[$this->mainEntity]["table"] . " AS " . $this->mainEntity;
		
		if($this->statement_join != "")
			$sql .= $this->statement_join;
		if($this->statement_where != "")
			$sql .= " WHERE " . $this->statement_where;
		if($this->statement_groupby != "")
			$sql .= " GROUP BY " . $this->statement_groupby;
		if($this->statement_orderby != "")
			$sql .= " ORDER BY " . $this->statement_orderby;
		
		$result->query = $sql;
		
		$result->queryState = $this->mysqli->query($sql);
		
		if($result->queryState) {
				
				$result->size = $result->queryState->fetch_array(MYSQLI_ASSOC)["size"];
				
		}
		else {
			echo "Query error: $sql";
		}
			
		return $result;
	}
};



DataModel::RegisterType("PrimaryKey",
						function ($d) {
							return (int)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_numeric($d);
						},
						true
						);

DataModel::RegisterType("Int",
						function ($d) {
							return (int)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_numeric($d);
						}
						);
						
DataModel::RegisterType("UnsignedInt",
						function ($d) {
							return (int)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_numeric($d) && $d >= 0;
						}
						);
						
DataModel::RegisterTypeAs("ForeignKey", "UnsignedInt");
DataModel::RegisterTypeAs("Timestamp", "UnsignedInt");
DataModel::RegisterTypeAs("Rank", "UnsignedInt");
			
DataModel::RegisterType("Float",
						function ($d) {
							return (float)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_float($d);
						}
						);
						
DataModel::RegisterType("UnsignedFloat",
						function ($d) {
							return (float)  $d;
						},
						function ($d, $c) {
							return is_numeric($d) && is_float($c($d)) && $c($d) >= 0;
						}
						);
			
DataModel::RegisterType("String",
						function ($d) {
							return (string)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_string($d);
						}
						);
						
DataModel::RegisterType("NotEmptyString",
						function ($d) {
							return (string)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_string($d) && $d != "";
						}
						);
			
DataModel::RegisterType("Phone",
						function ($d) {
							return (string)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_string($d) && strlen($d) == 10;
						}
						);
			
DataModel::RegisterType("Email",
						function ($d) {
							return (string)  $d;
						},
						function ($d, $c) {
							$d=$c($d);
							return is_string($d) && filter_var($d, FILTER_VALIDATE_EMAIL);
						}
						);
			
			
?>