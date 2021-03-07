<?php
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

include_once (__DIR__ . '/comparators/ReverseComparator.php');

class MinMaxMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testMinNoParam() {
		$array = [3, 3, 4, 1, 2, 5, 6];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->min();

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}

	public function testMinCallable() {
		$array = [3, 4, 1, 5, 6, 2];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->min(function ($first, $second) {
			return (intval($second) - intval($first)) / abs(intval($second) - intval($first));
		});

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}

	public function testMinComparator() {
		$array = [3, 4, 1, 5, 6, 2];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->min(new ReverseComparator());

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}

	public function testMinEmptyArray() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->min(new ReverseComparator());

		$this->assertTrue($res->isEmpty());
	}

	public function testMaxNoParam() {
		$array = [3, 3, 4, 1, 5, 6, 2, 6];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->max();

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}

	public function testMaxCallable() {
		$array = [3, 4, 1, 5, 6, 2];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->max(function ($first, $second) {
			return (intval($second) - intval($first)) / abs(intval($second) - intval($first));
		});

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}

	public function testMaxComparator() {
		$array = [3, 4, 1, 5, 6, 2];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->max(new ReverseComparator());

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}

	public function testMaxEmptyArray() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());

		$res = $stream->max(new ReverseComparator());

		$this->assertTrue($res->isEmpty());
	}
}
