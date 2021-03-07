<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

/**
 * Collects any data of a list.
 */
interface StreamCollector {

	/**
	 * The method collect the value of an `iterable` processed by the Stream API.
	 *
	 * @param iterable $iterable Values to collect.
	 *
	 * @return mixed The collected value(s)
	 */
	public function collect(iterable $iterable): mixed;

}