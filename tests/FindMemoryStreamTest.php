<?php
use phpstream\Stream;
use PHPUnit\Framework\TestCase;

class FindMemoryStreamTest extends TestCase {

	public function isMemory(): bool {
		return true;
	}

	public function testFirstNoData() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->findFirst();

		$this->assertTrue($res->isEmpty());
	}

	public function testFirstReturningData() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->findFirst();

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(1, $res->get());
	}

	public function testAnyNoData() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->findAny();

		$this->assertTrue($res->isEmpty());
	}

	public function testAnyReturningData() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->findAny();

		$this->assertFalse($res->isEmpty());
		$this->assertTrue(in_array($res->get(), $array, true));
	}

	public function testLastNoData() {
		$array = [];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->findLast();

		$this->assertTrue($res->isEmpty());
	}

	public function testLastReturningData() {
		$array = [1, 2, 3, 4, 5, 6];
		$stream = Stream::of($array, $this->isMemory());
		$res = $stream->findLast();

		$this->assertFalse($res->isEmpty());
		$this->assertEquals(6, $res->get());
	}

}
