<?php

namespace phpstream\traits;

use phpstream\functions\UnaryFunction;

trait OperatorInvokableTrait {

	/**
	 * Function used by __invoke magic method used to process one element
	 * @var callable $_invoker
	 * @internal
	 */
	private $_invoker;

	protected function buildInvoker(?UnaryFunction $function, ?callable $callable) {
		$this->_invoker = $function
				? fn($o1) => $function->apply($o1)
				: $callable;
	}

	public function __invoke() {
		return call_user_func_array($this->_invoker, func_get_args());
	}
}