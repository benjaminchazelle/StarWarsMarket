<?php

require_once("model.inc.php");
require_once("session.inc.php");

class Auth{
	
	static $REQUIRED = true;
	static $NOT_REQUIRED = false;
	
	private $userEntity = null;
	
	public function __construct($requiredAuth = false) {
		
		global $_MODEL;	
			
			if(isset($_SESSION["user_id"])) {
				
				$userMatchesEntities = $_MODEL->getEntities("user")
							->where("user_id", "=", $_SESSION["user_id"])
							->limit(1)
							->run();
							
				if($userMatchesEntities->size == 1)	{
					
					$this->userEntity = $userMatchesEntities->results[0]["user"];

				}
				else {
					
					unset($_SESSION["user_id"]);
				}
			
			}

			if($this->userEntity == null && $requiredAuth) {
				
				header("Location: login.php");
				exit;
				
			}
		
	}
	
	public function isLogged() {
		return isset($_SESSION["user_id"]) && $this->userEntity != null;
	}
	
	static function login($username, $password) {
		
		global $_MODEL;
		
		$password = sha1("ordre66" . $password);
		
		$userMatchesEntities = $_MODEL->getEntities("user")
					->where("user_email", "=", $username)
					->andWhere("user_password", "=", $password)
					->limit(1)
					->run();
					
		if($userMatchesEntities->size == 1)	{
			
			$userEntity = $userMatchesEntities->results[0]["user"];
			
			$_SESSION["user_id"] = $userEntity->user_id;
			
			return true;
			
		}
		else {
			
			return false;
		}
		
	}
	
	static function logout() {
		
		unset($_SESSION["user_id"]);
		
	}
	
	public function getUser() {
		
		return $this->userEntity;
		
	}
	
};

?>