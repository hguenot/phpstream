<?php

use phpstream\functions\UnaryFunction;

class EvenFunction implements UnaryFunction {

	public function apply(mixed $value): bool {
		return intval($value) % 2 == 0;
	}
}
