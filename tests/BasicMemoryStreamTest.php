<?php
include_once (__DIR__ . '/operator/SquareOperator.php');

use phpstream\Stream;
use PHPUnit\Framework\TestCase;

class BasicMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testCountSimple() {
		$array = [1, 2, 3, 4, 5, 6];
		$res = Stream::of($array, $this->isMemory())->count();
		$this->assertEquals(6, $res);
	}

	public function testCountFilter() {
		$array = [1, 2, 3, 4, 5, 6];
		$res = Stream::of($array, $this->isMemory())->filter(function ($value) {
			return intval($value) % 2 == 0;
		})
		             ->count();

		$this->assertEquals(3, $res);
	}

	public function testToArray() {
		$array = ['a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6];
		$res = Stream::of($array, $this->isMemory())->toArray();
		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}

	public function testToMap() {
		$array = ['a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6];
		$res = Stream::of($array, $this->isMemory())->toMap();
		$this->assertEquals($array, $res);
	}

	public function testExecute() {
		$array = ['a' => 1, 'z' => 2, 'e' => 3, 'r' => 4, 't' => 5, 'y' => 6];
		$res = Stream::of($array, $this->isMemory())
		             ->execute(new SquareOperator())
		             ->toMap();
		$this->assertEquals(['a' => 2, 'z' => 4, 'e' => 6, 'r' => 8, 't' => 10, 'y' => 12], $res);
	}

	public function testConcat() {
		$array1 = ['a' => 1, 'z' => 2, 'e' => 3];
		$array2 = ['r' => 4, 't' => 5, 'y' => 6];
		$res = Stream::concat($array1, Stream::of($array2, $this->isMemory()))->toArray();
		$this->assertEquals([1, 2, 3, 4, 5, 6], $res);
	}
}
