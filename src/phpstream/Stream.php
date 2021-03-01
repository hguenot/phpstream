<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream;

use InvalidArgumentException;
use phpstream\collectors\StreamCollector;
use phpstream\functions\BinaryFunction;
use phpstream\impl\GeneratorStream;
use phpstream\impl\MemoryStream;
use phpstream\operators\FilterOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\StreamOperator;
use phpstream\util\Comparator;
use phpstream\util\Optional;
/**
 * @example 00-basic.php 6 33 Basic example
 */

/**
 * Stream class is the main class for stream processing which supports aggregate operations.
 *
 * This version has globally 2 implementations :
 *
 * - MemoryStream : use native array function to perform operation. It requires more memory but is faster than the other implementation (this is the default).
 * - GeneratorStream: use generators to perform operation. It requires less memory but has lower performance.
 */
abstract class Stream {
	/**
	 * Construct a new Stream for performing array operations.
	 *
	 * @param iterable $iterable Array or iterable on which perform operations
	 * @param bool $inMemory Use in memory or not operation.
	 *
	 * @return Stream The stream processor
	 *
	 * {@see MemoryStream} for memory operation
	 * {@see GeneratorStream} for generator version
	 */
	public static function of(iterable $iterable = [], bool $inMemory = true): Stream {
		return $inMemory ? new MemoryStream(iterable_to_array($iterable, true)) : new GeneratorStream($iterable);
	}

	/**
	 * Concatenate multiple stream in one.
	 *
	 * @param Stream|array $streams Streams or arrays to concatenate
	 *
	 * @return Stream Stream processor for all parameters
	 */
	public static function concat(... $streams): Stream {
		return new GeneratorStream(self::_toIterable($streams));
	}

	/**
	 * Build an iterable for all streams
	 *
	 * @param array $streams Streams or arrays to concatenate
	 *
	 * @return iterable The iterable of all concatenated streams or arrays
	 */
	private static function _toIterable(array $streams): iterable {
		foreach ($streams as $stream) {
			if (is_array($stream)) {
				$stream = new MemoryStream($stream);
			}
			if (!($stream instanceof Stream)) {
				throw new InvalidArgumentException("Stream parameters must be Stream or array instance.");
			}
			foreach ($stream->toIterable() as $key => $value) {
				yield $key => $value;
			}
		}
	}

	/**
	 * Filter enclosing array using a callback function.
	 * Callback function must take 1 parameters <tt>$value</tt>
	 * corresponding to array value in the enclosing array. It must
	 * return <tt>true</tt> if the element is valid, <tt>false</tt> otherwise.
	 *
	 * @param callable|functions\UnaryFunction|FilterOperator $filter Filtering callback function.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function filter($filter): Stream;

	/**
	 * Maps each element of the enclosing array using function.
	 *
	 * @param callable|functions\UnaryFunction $mapper Mapping function to call
	 *
	 * @return Stream Current stream.
	 */
	public abstract function map($mapper): Stream;

	/**
	 * Maps each element of the enclosing array using function.
	 *
	 * @param callable|functions\UnaryFunction $peekingFunction Mapping function to call
	 *
	 * @return Stream Current stream.
	 */
	public abstract function peek($peekingFunction): Stream;

	/**
	 * Limits the number of result.
	 *
	 * @param int $limit Number of results to keep.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function limit(int $limit): Stream;

	/**
	 * Skip the first result.
	 *
	 * @param int $limit Number of elements to skip.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function skip($limit): Stream;

	/**
	 * Returns a new Stream that contains only distinct elements.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function distinct(): Stream;

	/**
	 * Returns an optional containing the max value of the stream if exists.
	 *
	 * @param callable|string $indexer Indexer function or field.
	 * @param bool $allowDuplicate If duplicates are allowed, no error if given when 2 elements got same key.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function index($indexer, $allowDuplicate = false): Stream;

	/**
	 * Sort stream.
	 *
	 * @param callable|Comparator|string $cmp Comparator object / callback or field/method.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function sort($cmp = null): Stream;

	/**
	 * Skip the first result.
	 *
	 * @param StreamOperator $operator Operator to execute.
	 *
	 * @return Stream Current stream.
	 */
	public abstract function execute(StreamOperator $operator): Stream;

	/**
	 * Returns an Optional instance containing any element of the resulting array if exists.
	 *
	 * @return Optional The first element of the resulting array if exists.
	 */
	public abstract function findAny(): Optional;

	/**
	 * Returns an Optional instance containing the first element of the resulting array if exists.
	 *
	 * @return Optional The first element of the resulting array if exists.
	 */
	public abstract function findFirst(): Optional;

	/**
	 * Returns an Optional instance containing the first element of the resulting array if exists.
	 *
	 * @return Optional The first element of the resulting array if exists.
	 */
	public abstract function findLast(): Optional;

	/**
	 * Returnd the number of elements in the resulting array.
	 *
	 * @return int The number of elements in the resulting array.
	 */
	public abstract function count(): int;

	/**
	 * Executes all operations and return an array of results.
	 *
	 * @return array
	 */
	public abstract function toArray(): array;

	/**
	 * Executes all operations and return a map of results (conserving key / value association of the initial array).
	 *
	 * @return array
	 */
	public abstract function toMap(): array;

	/**
	 * Executes all operations and return a map of results (conserving key / value association of the initial array).
	 *
	 * @return iterable
	 */
	public abstract function toIterable(): iterable;

	/**
	 * Returns an optional containing the min value of the stream if exists.
	 *
	 * @param callable|Comparator $cmp Comparator object / callback.
	 *
	 * @return Optional The min value of the stream.
	 */
	public abstract function min($cmp = null): Optional;

	/**
	 * Returns an optional containing the max value of the stream if exists.
	 *
	 * @param callable|Comparator $cmp Comparator object / callback.
	 *
	 * @return Optional The max value of the stream.
	 */
	public abstract function max($cmp = null): Optional;

	/**
	 * Reduces the value of the array using the given function.
	 *
	 * @param callable|BinaryFunction $reducer Reducing callback.
	 * @param mixed $initialValue Initial value - default if stream is empty.
	 *
	 * @return mixed The reduced value of the stream.
	 */
	public abstract function reduce($reducer, $initialValue = null);

	/**
	 * Collect data according given Stream collector.
	 *
	 * @param StreamCollector $collector Collector instance
	 *
	 * @return mixed Depends on Stream Collector
	 */
	public abstract function collect(StreamCollector $collector);

	/**
	 * @param $cmp
	 *
	 * @return callable|Comparator A comparator method or object
	 */
	protected final function _getComparator($cmp) {
		$fn = $cmp;
		if ($fn === null) {
			$fn = function ($o1, $o2) {
				return $o1 <=> $o2;
			};
		}
		if (is_string($fn)) {
			/* @var callable $callable */
			$callable = MapOperator::getFn($fn)[1];
			if ($callable) {
				$fn = function ($o1, $o2) use ($callable) {
					return $callable($o1) <=> $callable($o2);
				};
			}
		}
		if (!($fn instanceof Comparator) && !is_callable($fn)) {
			throw new InvalidArgumentException("Comparator callback must be instance of Comparator or a callable function.");
		}
		return $fn;
	}
}