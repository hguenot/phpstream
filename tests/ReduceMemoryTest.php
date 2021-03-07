<?php

use phpstream\functions\BinaryFunction;
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

class ReduceMemoryTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testCallable() {
		$array = [1, 2, 3, 4];
		$stream = Stream::of($array, $this->isMemory());
		$res =  $stream->reduce(function ($carry, $value) {
			return ($carry == null ? 1 : $carry) * intval($value);
		});

		$this->assertEquals(24, $res);
	}

	public function testEmptyCallable() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());
		$res =  $stream->reduce(function ($carry, $value) {
			return ($carry == null ? 1 : $carry) * intval($value);
		}, -1);

		$this->assertEquals(-1, $res);
	}

	public function testBinaryFunction() {
		$array = [1, 2, 3, 4];
		$stream = Stream::of($array, $this->isMemory());
		$res =  $stream->reduce(new class implements BinaryFunction {
			public function apply($carry, $value) {
				return ($carry == null ? 1 : $carry) * intval($value);
			}
		});

		$this->assertEquals(24, $res);
	}

	public function testEmptyBinaryFunction() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());
		$res =  $stream->reduce(new class implements BinaryFunction {
			public function apply($carry, $value) {
				return ($carry == null ? 1 : $carry) * intval($value);
			}
		}, -1);

		$this->assertEquals(-1, $res);
	}


}