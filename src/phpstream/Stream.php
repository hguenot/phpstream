<?php

/**
 * Stream class definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream;

use phpstream\collectors\AnyCollector;
use phpstream\collectors\DistinctCollector;
use phpstream\collectors\FirstCollector;
use phpstream\collectors\ListCollector;
use phpstream\collectors\MapCollector;
use phpstream\collectors\MaxCollector;
use phpstream\collectors\MinCollector;
use phpstream\collectors\ReduceCollector;
use phpstream\collectors\SumCollector;
use phpstream\operators\FilterOperator;
use phpstream\operators\LimitOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\PeekOperator;
use phpstream\operators\SkipOperator;
use phpstream\util\Comparator;
use phpstream\util\Optional;

/**
 * Stream class is the main class for stream processing which supports aggregate operations.
 * It claims to be a PHP port of the 
 * <a href="https://docs.oracle.com/javase/8/docs/api/java/util/stream/Stream.html">Java Stream API</a>.
 * 
 * To compute the result, a set of operations (filtering, maping) are applied over the initial array and the result
 * can be compute with a <em>terminal operation</em>.
 * 
 * <strong>Example : </strong>
 * @example ./docs/samples/00-basic.php 6 13 Basic example
 * 
 */
class Stream {
	
	/** @var mixed[] initial array of values to be processed. */
	private $array;
	
	/** @var StreamOperator[] List of operators to apply. */
	private $operators = [];
	
	/**
	 * Start a new Stream process over given array.
	 * s
	 * @param array $array Array on which apply stream process.
	 * 
	 * @return \phpstream\Stream The new stream.
	 */
	public static function of(array $array = []) {
		return new Stream($array);
	}
	
	/**
	 * Start a new Stream process over given array.
	 * 
	 * @param array $array Array on which apply stream process.
	 */
	public function __construct(array $array = []) {
		$this->array = $array;
	}
	
	/**
	 * Filter enclosing array using a callback function. 
	 * Callback function must take 1 parameters <tt>$value</tt> 
	 * corresponding to array value in the enclosing array. It must 
	 * return <tt>true</tt> if the element is valid, <tt>false</tt> otherwise.
	 * 
	 * @param callable|functions\UnaryFunction $filter Filtering callback function.
	 * 
	 * @return Stream Current stream
	 */
	public function filter($filter) {
		$this->addOperator(new FilterOperator($filter));
		return $this;
	}
	
	/**
	 * Maps each element of the enclosing array using function.
	 * 
	 * @param callable|functions\UnaryFunction $mapper Mapping function to call
	 * 
	 * @return Stream Current stream
	 */
	public function map($mapper) {
		$this->addOperator(new MapOperator($mapper));
		return $this;
	}
	
	/**
	 * Maps each element of the enclosing array using function.
	 * 
	 * @param callable|functions\UnaryFunction $mapper Mapping function to call
	 * 
	 * @return Stream Current stream
	 */
	public function peek($mapper) {
		$this->addOperator(new PeekOperator($mapper));
		return $this;
	}
	
	/**
	 * Limits the number of result.
	 * 
	 * @param int $limit
	 * 
	 * @return Stream Current stream
	 */
	public function limit($limit) {
		$this->addOperator(new LimitOperator($limit));
		return $this;
	}
	
	/**
	 * Skip the first result.
	 * 
	 * @param int $limit
	 * 
	 * @return Stream Current stream
	 */
	public function skip($limit) {
		$this->addOperator(new SkipOperator($limit));
		return $this;
	}
	
	/**
	 * Returns an Optional instance containing any element of the resulting array if exists.
	 * 
	 * @return Optional The first element of the resulting array if exists.
	 */
	public function findAny() {
		$stream = clone $this;
		return $stream->collect(new AnyCollector());
	}
	
	/**
	 * Returns an Optional instance containing the first element of the resulting array if exists.
	 * 
	 * @return Optional The first element of the resulting array if exists.
	 */
	public function findFirst() {
		$stream = clone $this;
		$stream->addOperator(new LimitOperator(1));
		return $stream->collect(new FirstCollector());
	}
	
	/**
	 * Returnd the number of elements in the resulting array.
	 * 
	 * @return int The number of elements in the resulting array.
	 */
	public function count() {
		$stream = clone $this;
		$stream->addOperator(new MapOperator(function(){ return 1; }));
		return $stream->collect(new SumCollector());
	}
	
	/**
	 * Returns a new Stream that contains only distinct elements.
	 * 
	 * @return Stream
	 */
	public function distinct() {
		return new Stream($this->collect(new DistinctCollector()));
	}
	
	/**
	 * Collect data according given Stream collector.
	 * 
	 * @param StreamCollector $collector
	 * 
	 * @return mixed Depends on Stream Collector
	 */
	public function collect(StreamCollector $collector) {
		if (!empty($this->operators)) {
			foreach ($this->operators as $operator) {
				$operator->reset();
			}
			$collector->reset();
			
			foreach ($this->array as $key => $value) {
				$stopPropagation = false;
				
				reset($this->operators);
				$last = $value;
				while (($operator = current($this->operators)) && !$stopPropagation) {
					$last = $operator->execute($last, $stopPropagation);
					next($this->operators);
				}
				
				if (!$stopPropagation) 
					$collector->collect($key, $last);
			}
		} else {
			foreach ($this->array as $key => $value) {
				$collector->collect($key, $value);
			}
		}
		
		return $collector->get();
	}
	
	/**
	 * Reduce the stream using the given function. The reduce method will be called with
	 * two Optional value.
	 * 
	 * @param callable|functions\BiFunction $reducer The reduce function to apply.
	 * @param mixed $default Default value
	 * 
	 * @return Optional Value after the reduce operation if exists.
	 */
	public function reduceWithDefault($reducer, $default) {
		$res = $this->reduce($reducer);
		return $res->isEmpty() ? Optional::fromNullable($default) : $res;
	}

	/**
	 * Reduce the stream using the given function. The reduce method will be called with
	 * two Optional value.
	 * 
	 * @param callable|functions\BiFunction $reducer The reduce function to apply.
	 * 
	 * @return Optional Value after the reduce operation if exists.
	 */
	public function reduce($reducer) {
		return $this->collect(new ReduceCollector($reducer));
	}
	
	/**
	 * Returns a stream consisting of the elements of this stream, sorted according to the provided 
	 * comparator 
	 * 
	 * @param callable|Comparator $cmp
	 * 
	 * @return \phpstream\Stream
	 */
	public function sort($cmp = null) {
		$res = $this->collect(new MapCollector());
		
		if ($cmp !== null) {
			if ($cmp instanceof Comparator) {
				$cmp = $this->getCallableComparator($cmp);
			} else if (!is_callable($cmp)) {
				throw new \InvalidArgumentException('Comparator function must be callable or a Comparator object.');
			}
			uasort($res, $cmp);
		} else {
			asort($res);
		}
		return new Stream($res);
	}
	
	/**
	 * Returns an optional containing the min value of the stream if exists.
	 * 
	 * @param callable|Comparator $cmp Comparator objet / callback.
	 * 
	 * @return Optional The min value of the stream.
	 */
	public function min($cmp = null) {
		return $this->collect(new MinCollector($cmp));
	}	
	
	/**
	 * Returns an optional containing the max value of the stream if exists.
	 * 
	 * @param callable|Comparator $cmp Comparator objet / callback.
	 * 
	 * @return Optional The max value of the stream.
	 */
	public function max($cmp = null) {
		return $this->collect(new MaxCollector($cmp));
	}

	/**
	 * Returns an optional containing the max value of the stream if exists.
	 *
	 * @param callable|string $indexer Indexer function or field.
	 * @param bool $allowDuplicate If duplicates are allowed, no error if given when 2 elements got same key.
	 *
	 * @return Stream
	 */
	public function index($indexer, $allowDuplicate = false) {
		$collected = $this->collect(new ListCollector());
		$res = [];

		$f = is_string($indexer)
			? function($e) use ($indexer) {
				return $e->$indexer;
			}
			: $indexer;

		foreach ($collected as $e) {
			$key = $f($e);
			if (!$allowDuplicate && array_key_exists($key, $res))
				throw new \InvalidArgumentException("Multiple elements got same key.");
			$res[$key] = $e;
		}

		return new Stream($res);
	}

	/**
	 * Executes all operations and return an array of results.
	 * 
	 * @return array 
	 */
	public function toArray() {
		return $this->collect(new ListCollector());
	}

	/**
	 * Executes all operations and return a map of results (conserving key / value association of the initial array).
	 * 
	 * @return array 
	 */
	public function toMap() {
		return $this->collect(new MapCollector());
	}

	/**
	 * Register an operator in the streaming process
	 * 
	 * @param StreamOperator $operator Operator to register.
	 */
	protected function addOperator(StreamOperator $operator) {
		$this->operators[] = $operator;
	}
	
	/**
	 * Clone the current stream and returns a new one.
	 * 
	 * @return Stream The cloned stream.
	 * 
	 * @ignore
	 */
	private function __clone() {
		$stream = new Stream($this->array);
		$stream->operators = array_merge([], $this->operators);
		return $stream;
	}
	
	/**
	 * Creates a callable function from the given comparator.
	 *  
	 * @param Comparator $obj
	 * 
	 * @return callable callable function applying the comparator.
	 * 
	 * @ignore
	 */
	private function getCallableComparator(Comparator $obj) {
		return function ($o1, $o2) use ($obj) {
			return $obj->compare($o1, $o2);
		};
	}
}
