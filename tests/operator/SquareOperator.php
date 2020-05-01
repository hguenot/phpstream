<?php

use phpstream\operators\StreamOperator;

class SquareOperator implements StreamOperator {
	/**
	 * @inheritDoc
	 */
	public function execute(iterable $values): iterable {
		foreach ($values as $key => $value) {
			yield $key => $value * 2;
		}
	}
}