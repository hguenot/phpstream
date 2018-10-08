<?php
use phpstream\functions\UnaryFunction;

class EvenFunction implements UnaryFunction {

	public function apply($value) {
		return intval($value) % 2 == 0;
	}
}
