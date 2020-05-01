<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\operators;

/**
 * Basic interface for performing operations over a collection.
 */
interface StreamOperator {

	/**
	 * The method returns an `iterable` of the processed elements.
	 *
	 * @param iterable $values Values to process.
	 *
	 * @return iterable The processed values.
	 */
	public function execute(iterable $values): iterable;

}