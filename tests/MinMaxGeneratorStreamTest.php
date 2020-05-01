<?php

include_once(__DIR__ . '/MinMaxMemoryStreamTest.php');

class MinMaxGeneratorStreamTest extends MinMaxMemoryStreamTest {
	public function isMemory(): bool {
		return false;
	}
}