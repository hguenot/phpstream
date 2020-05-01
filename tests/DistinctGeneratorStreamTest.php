<?php

include_once(__DIR__ . '/DistinctMemoryStreamTest.php');

class DistinctGeneratorStreamTest extends DistinctMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}