<?php

class Bean {

	public function __construct(public int $x = 0) {
		$this->x = $x;
	}

	public function getY() {
		return $this->x * 2;
	}
}

