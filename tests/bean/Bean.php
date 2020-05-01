<?php

class Bean {

	public $x;

	public function __construct(int $x = 0) {
		$this->x = $x;
	}

	public function getY() {
		return $this->x * 2;
	}
}

