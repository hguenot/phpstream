<?php
use phpstream\collectors\ArrayCollector;
use phpstream\collectors\MapCollector;
use phpstream\Stream;
use phpstream\operators\FilterOperator;
use phpstream\functions\UnaryFunction;
use PHPUnit\Framework\TestCase;

include_once (__DIR__ . '/functions/EvenFunction.php');

class FilterMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testCallable() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([2, 4, 6], $res);
	}

	public function testFunction() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->filter(new EvenFunction());

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([2, 4, 6], $res);
	}

	public function testFilterOperator() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->filter(new FilterOperator(new class() implements UnaryFunction {

			public function apply(mixed $value): bool {
				return intval($value) % 2 == 0;
			}
		}));

		$res = $stream->collect(new ArrayCollector());

		$this->assertEquals([2, 4, 6], $res);
	}

	public function testMapCollector() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new MapCollector());

		$this->assertEquals(['b' => 2, 'd' => 4, 'f' => 6], $res);
	}

	public function testMapCollectorKeyFn1() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new MapCollector(function ($key, $value) {
			return 'z_' . $key;
		}));

		$this->assertEquals(['z_b' => 2, 'z_d' => 4, 'z_f' => 6], $res);
	}

	public function testMapCollectorKeyFn2() {
		$array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6];
		$stream = Stream::of($array, $this->isMemory());
		$stream->filter(function ($value) {
			return intval($value) % 2 == 0;
		});

		$res = $stream->collect(new MapCollector(function ($key, $value) {
			return $value * 2;
		}));

		$this->assertEquals([4 => 2, 8 => 4, 12 => 6], $res);
	}
}
