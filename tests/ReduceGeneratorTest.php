<?php

include_once (__DIR__ . '/ReduceMemoryTest.php');

class ReduceGeneratorTest extends ReduceMemoryTest {
	public function isMemory(): bool {
		return false;
	}
}