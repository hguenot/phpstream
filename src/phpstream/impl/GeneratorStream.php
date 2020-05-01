<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\impl;

use InvalidArgumentException;
use phpstream\collectors\ArrayCollector;
use phpstream\collectors\CountCollector;
use phpstream\collectors\FirstCollector;
use phpstream\collectors\LastCollector;
use phpstream\collectors\MaxCollector;
use phpstream\collectors\MinCollector;
use phpstream\collectors\StreamCollector;
use phpstream\operators\DistinctOperator;
use phpstream\operators\FilterOperator;
use phpstream\operators\LimitOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\PeekOperator;
use phpstream\operators\SkipOperator;
use phpstream\operators\StreamOperator;
use phpstream\Stream;
use phpstream\util\Comparator;
use phpstream\util\Optional;

/**
 * Stream processor implementation using Generators (`yield`).
 * It offers less performance but requires less memory to perform operations.
 */
class GeneratorStream extends Stream {
	/**
	 * Enclosed iterator to be processed.
	 * @var iterable $_iterable
	 */
	private $_iterable;

	/**
	 * GeneratorStream constructor.
	 *
	 * @param iterable $iterable Iterable value to be processed.
	 */
	public function __construct(iterable $iterable) {
		$this->_iterable = $iterable;
	}

	public function filter($filter): Stream {
		$operator = new FilterOperator($filter);
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function map($mapper): Stream {
		$operator = new MapOperator($mapper);
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function peek($peekingFunction): Stream {
		$operator = new PeekOperator($peekingFunction);
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function limit(int $limit): Stream {
		$operator = new LimitOperator($limit);
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function skip($limit): Stream {
		$operator = new SkipOperator($limit);
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function distinct(): Stream {
		$operator = new DistinctOperator();
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function index($indexer, $allowDuplicate = false): Stream {
		$f = is_string($indexer)
				? function ($e) use ($indexer) {
					return $e->$indexer;
				}
				: $indexer;
		$iterable = $this->_iterable;
		if ($allowDuplicate) {
			$mapper = function () use ($iterable, $f): iterable {
				foreach ($iterable as $value) {
					yield $f($value) => $value;
				}
			};
		} else {
			$indices = [];
			$mapper = function () use ($iterable, $f, $indices): iterable {
				foreach ($iterable as $value) {
					$key = $f($value);
					if (array_key_exists($key, $indices)) {
						throw new InvalidArgumentException("Multiple elements got same key.");
					}
					$indices[$key] = true;
					yield $key => $value;
				}
			};
		}

		$this->_iterable = $mapper();

		return $this;
	}

	public function sort($cmp = null): Stream {
		$fn = $this->_getComparator($cmp);
		$array = iterable_to_array($this->_iterable, true);
		uasort($array, $fn instanceof Comparator
				? function ($o1, $o2) use ($fn) {
					return $fn->compare($o1, $o2);
				}
				: $fn);
		$this->_iterable = $array;
		return $this;
	}

	public function execute(StreamOperator $operator): Stream {
		$this->_iterable = $operator->execute($this->_iterable);
		return $this;
	}

	public function findAny(): Optional {
		return $this->findFirst();
	}

	public function findFirst(): Optional {
		$collector = new FirstCollector();
		$this->limit(1);
		return $collector->collect($this->_iterable);
	}

	public function findLast(): Optional {
		$collector = new LastCollector();
		return $collector->collect($this->_iterable);
	}

	public function count(): int {
		$collector = new CountCollector();
		return $collector->collect($this->_iterable);
	}

	public function toArray(): array {
		$collector = new ArrayCollector(false);
		return $collector->collect($this->_iterable);
	}

	public function toMap(): array {
		$collector = new ArrayCollector(true);
		return $collector->collect($this->_iterable);
	}

	public function toIterable(): iterable {
		return $this->_iterable;
	}

	public function min($cmp = null): Optional {
		$collector = new MinCollector($cmp);
		return $collector->collect($this->_iterable);
	}

	public function max($cmp = null): Optional {
		$collector = new MaxCollector($cmp);
		return $collector->collect($this->_iterable);
	}

	public function collect(StreamCollector $collector) {
		return $collector->collect($this->_iterable);
	}
}
