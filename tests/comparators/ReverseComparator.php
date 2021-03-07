<?php

class ReverseComparator implements phpstream\util\Comparator {

	public function compare(mixed $o1, mixed $o2): int {
		return (intval($o2) - intval($o1)) / abs(intval($o2) - intval($o1));
	}
}
