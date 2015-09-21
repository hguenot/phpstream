<?php

use phpstream\functions\BiFunction;

class MinFunction implements BiFunction {
	
	public function apply($first, $second) {
		return min([ intval($first), intval($second) ]);
	}

}
