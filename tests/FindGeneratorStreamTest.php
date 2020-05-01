<?php

include_once(__DIR__ . '/FindMemoryStreamTest.php');

class FindGeneratorStreamTest extends FindMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}