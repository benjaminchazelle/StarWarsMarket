<?php

$_VIEW = array();

class View {
	
	static function Enable($viewName) {
		global $_VIEW;
		
		$_VIEW[$viewName] = true;
	}
	
	static function Disable($viewName) {
		global $_VIEW;
		
		$_VIEW[$viewName] = false;
	}
	
	static function Display($viewName, $display, $displayElse = "") {
		global $_VIEW;
	
		if(!isset($_VIEW[$viewName]))
			$_VIEW[$viewName] = false;
		
		if($_VIEW[$viewName]) {
			if(is_callable($display))
				$display();
			else
				echo $display;
		}
		else {

			if(is_callable($displayElse))
				$displayElse();
			else
				echo $displayElse;

		}
		

	}
	
	static function Debug() {
		global $_VIEW;
		print_r($_VIEW);
		var_dump($_VIEW);
	}
	
}

?>