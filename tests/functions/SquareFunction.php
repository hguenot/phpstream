<?php
use phpstream\functions\UnaryFunction;

class SquareFunction implements UnaryFunction {

	public function apply($value) {
		return intval($value) * intval($value);
	}
}
