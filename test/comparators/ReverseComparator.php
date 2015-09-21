<?php

class ReverseComparator implements phpstream\util\Comparator {
	
	public function compare($o1, $o2) {
		return (intval($o2) - intval($o1)) / abs(intval($o2) - intval($o1));
	}

}
