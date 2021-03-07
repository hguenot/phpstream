<?php
use phpstream\functions\UnaryFunction;

class SquareFunction implements UnaryFunction {

	public function apply(mixed $value): float|int {
		return intval($value) * intval($value);
	}
}
