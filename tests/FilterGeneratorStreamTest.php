<?php

include_once(__DIR__ . '/FilterMemoryStreamTest.php');

class FilterGeneratorStreamTest extends FilterMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}