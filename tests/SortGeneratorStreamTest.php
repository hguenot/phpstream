<?php

include_once(__DIR__ . '/SortMemoryStreamTest.php');

class SortGeneratorStreamTest extends SortMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}