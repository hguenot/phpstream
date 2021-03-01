<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\impl;

use InvalidArgumentException;
use phpstream\collectors\StreamCollector;
use phpstream\functions\BinaryFunction;
use phpstream\functions\UnaryFunction;
use phpstream\operators\FilterOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\PeekOperator;
use phpstream\operators\StreamOperator;
use phpstream\Stream;
use phpstream\util\Comparator;
use phpstream\util\Optional;

/**
 * Stream processor implementation using internal php array function.
 * It offers better performance but requires more memory to perform operations.
 */
class MemoryStream extends Stream {
	/**
	 * Enclosed array to be processed
	 * @var array $_array
	 */
	private $_array;

	/**
	 * MemoryStream constructor.
	 *
	 * @param array $iterable Iterable value to be processed.
	 */
	public function __construct(array $iterable) {
		$this->_array = $iterable;
	}

	public function filter($filter): Stream {
		if ($filter instanceof FilterOperator) {
			[$function, $callable] = FilterOperator::getFn($filter);
			if ($function) {
				/* @var UnaryFunction $function */
				$this->_array = array_filter($this->_array, function ($value) use ($function) {
					return $function->apply($value);
				});
			} else {
				/* @var callable $callable */
				$this->_array = array_filter($this->_array, $callable);
			}
		} else if ($filter instanceof UnaryFunction) {
			$this->_array = array_filter($this->_array, function ($value) use ($filter) {
				return $filter->apply($value);
			});
		} else if (is_callable($filter)) {
			$this->_array = array_filter($this->_array, $filter);
		} else {
			throw new InvalidArgumentException('Parameter must be callable or UnaryFunction.');
		}
		return $this;
	}

	public function map($mapper): Stream {
		[$function, $callable] = MapOperator::getFn($mapper);

		if ($function) {
			/* @var UnaryFunction $function */
			$this->_array = array_map(function ($value) use ($function) {
				return $function->apply($value);
			}, $this->_array);
		} else {
			/* @var callable $callable */
			$this->_array = array_map($callable, $this->_array);
		}

		return $this;
	}

	public function peek($peekingFunction): Stream {
		[$function, $callable] = PeekOperator::getFn($peekingFunction);
		if ($function) {
			/* @var UnaryFunction $function */
			array_walk($this->_array, function ($value) use ($function) {
				return $function->apply($value);
			});
		} else {
			/* @var callable $callable */
			$this->_array = array_filter($this->_array, $callable);
		}

		return $this;
	}

	public function limit(int $limit): Stream {
		if ($limit < 0) {
			throw new InvalidArgumentException('Limit must be a positive integer.');
		}
		$this->_array = array_slice($this->_array, 0, $limit);
		return $this;
	}

	public function skip($limit): Stream {
		if ($limit < 0) {
			throw new InvalidArgumentException('Limit must be a positive integer.');
		}
		$this->_array = array_slice($this->_array, $limit);
		return $this;
	}

	public function distinct(): Stream {
		$this->_array = array_unique($this->_array);
		return $this;
	}

	public function index($indexer, $allowDuplicate = false): Stream {
		$f = is_string($indexer)
				? function ($e) use ($indexer) {
					return $e->$indexer;
				}
				: $indexer;
		$array = [];
		if ($allowDuplicate) {
			foreach ($this->_array as $value) {
				$array[$f($value)] = $value;
			}
		} else {
			foreach ($this->_array as $value) {
				$key = $f($value);
				if (array_key_exists($key, $array)) {
					throw new InvalidArgumentException("Multiple elements got same key.");
				}
				$array[$key] = $value;
			}
		}
		$this->_array = $array;

		return $this;
	}

	public function sort($cmp = null): Stream {
		$fn = $this->_getComparator($cmp);
		uasort($this->_array, $fn instanceof Comparator
				? function ($o1, $o2) use ($fn) {
					return $fn->compare($o1, $o2);
				}
				: $fn);
		return $this;
	}

	public function execute(StreamOperator $operator): Stream {
		$this->_array = iterable_to_array($operator->execute($this->_array), true);
		return $this;
	}

	public function findAny(): Optional {
		reset($this->_array);
		return count($this->_array) > 0 ? Optional::ofNullable(next($this->_array)) : Optional::absent();
	}

	public function findFirst(): Optional {
		reset($this->_array);
		return count($this->_array) > 0 ? Optional::ofNullable(current($this->_array)) : Optional::absent();
	}

	public function findLast(): Optional {
		reset($this->_array);
		$count = count($this->_array);
		return $count > 0 ? Optional::ofNullable(array_values($this->_array)[$count-1]) : Optional::absent();
	}

	public function count(): int {
		return count($this->_array);
	}

	public function toArray(): array {
		return array_values($this->_array);
	}

	public function toMap(): array {
		return $this->_array;
	}

	public function toIterable(): iterable {
		foreach ($this->_array as $key => $value) {
			yield $key => $value;
		}
	}

	public function min($cmp = null): Optional {
		if ($cmp === null) {
			$cmp = function($o1, $o2) {
				return $o1 <=> $o2;
			};
		}
		if (!($cmp instanceof Comparator) && !is_callable($cmp)) {
			throw new InvalidArgumentException("Comparator callback must be instance of Comparator or a callable function.");
		}
		uasort($this->_array, $cmp instanceof Comparator
				? function ($o1, $o2) use ($cmp) {
					return $cmp->compare($o1, $o2);
				}
				: $cmp);
		return $this->findFirst();
	}

	public function max($cmp = null): Optional {
		if ($cmp === null) {
			$cmp = function($o1, $o2) {
				return $o1 <=> $o2;
			};
		}
		if (!($cmp instanceof Comparator) && !is_callable($cmp)) {
			throw new InvalidArgumentException("Comparator callback must be instance of Comparator or a callable function.");
		}
		uasort($this->_array, $cmp instanceof Comparator
				? function ($o1, $o2) use ($cmp) {
					return $cmp->compare($o2, $o1);
				}
				: function ($o1, $o2) use ($cmp) {
					return $cmp($o2, $o1);
				});
		return $this->findFirst();
	}

	public function reduce($reducer, $initialValue = null) {
		return array_reduce($this->_array, $reducer instanceof BinaryFunction
				? function ($carry, $item) use ($reducer) {
					return $reducer->apply($carry, $item);
				}
				: $reducer,
				$initialValue);
	}

	public function collect(StreamCollector $collector) {
		return $collector->collect($this->_array);
	}

}
