<?php

class Misc {
	
	static function checkArray($attr_array, $array) {
		/*
		$l = count($attr_array);
		
		for($i = 0;$i<$l;$i++) {
			
			if(!array_key_exists($attr_array[$i], $array))
				return false;
			
		}
		*/
		foreach($attr_array as $attr) {
			if(!isset($array[$attr]))
				return false;
		}
		
		return true;
	}
};
	
?>