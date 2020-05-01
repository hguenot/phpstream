<?php
include_once(__DIR__ . '/../vendor/autoload.php');

use phpstream\impl\GeneratorStream;
use phpstream\impl\MemoryStream;
use phpstream\operators\FilterOperator;
use phpstream\operators\LimitOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\SkipOperator;

ini_set('memory_limit', '1G');


$benchs = [
		[
				'name' => 'PHP native method',
				'fn' => function ($arr) {
					$arr = array_filter($arr, function ($x) {
						return ($x % 2) === 0;
					});
					$arr = array_filter(
							$arr,
							function ($x) {
								return $x >= 30;
							}
					);
					$arr = array_map(
							function ($x) {
								return $x + 1;
							},
							$arr
					);
					$arr = array_splice($arr, 2, 5);
					return array_values($arr);
				},
				'getArr' => function ($max) {
					return range(0, $max);
				}
		],
		[
				'name' => 'PHPStream op. direct call as array',
				'fn' => function ($arr) {
					$arr = iterable_to_array((new FilterOperator(function ($x) {
						return ($x % 2) === 0;
					}))->execute($arr));
					$arr = iterable_to_array((new FilterOperator(function ($x) {
						return $x >= 30;
					}))->execute($arr));
					$arr = iterable_to_array((new MapOperator(function ($x) {
						return $x + 1;
					}))->execute($arr));
					$arr = iterable_to_array((new SkipOperator(2))->execute($arr));
					$arr = iterable_to_array((new LimitOperator(5))->execute($arr));
					return array_values($arr);
				},
				'getArr' => function ($max) {
					return range(0, $max);
				}
		],
		[
				'name' => 'PHPStream operator direct call',
				'fn' => function ($arr) {
					$arr = (new FilterOperator(function ($x) {
						return ($x % 2) === 0;
					}))->execute($arr);
					$arr = (new FilterOperator(function ($x) {
						return $x >= 30;
					}))->execute($arr);
					$arr = (new MapOperator(function ($x) {
						return $x + 1;
					}))->execute($arr);
					$arr = (new SkipOperator(2))->execute($arr);
					$arr = (new LimitOperator(5))->execute($arr);
					return iterable_to_array($arr, false);
				},
				'getArr' => function ($max) {
					return range(0, $max);
				}
		],
		[
				'name' => 'PHPStream in memory',
				'fn' => function (MemoryStream $arr) {
					return $arr
					             ->filter(function ($x) {
						             return ($x % 2) === 0;
					             })
					             ->filter(function ($x) {
						             return $x >= 30;
					             })
					             ->map(function ($x) {
						             return $x + 1;
					             })
								->skip(2)
								->limit(5)
					             ->toArray();
				},
				'getArr' => function ($max) {
					return (new MemoryStream(range(0, $max)));
				}
		],
		[
				'name' => 'PHPStream with generator',
				'fn' => function (GeneratorStream $arr) {
					return $arr
					             ->filter(function ($x) {
						             return ($x % 2) === 0;
					             })
					             ->filter(function ($x) {
						             return $x >= 30;
					             })
					             ->map(function ($x) {
						             return $x + 1;
					             })
					             ->skip(2)
					             ->limit(5)
					             ->toArray();
				},
				'getArr' => function ($max) {
					return (new GeneratorStream(range(0, $max)));
				}
		],
		[
				'name' => 'PHPStream with full generator',
				'fn' => function (GeneratorStream $arr) {
					return $arr
					             ->filter(function ($x) {
						             return ($x % 2) === 0;
					             })
					             ->filter(function ($x) {
						             return $x >= 30;
					             })
					             ->map(function ($x) {
						             return $x + 1;
					             })
					             ->skip(2)
					             ->limit(5)
					             ->toArray();
				},
				'getArr' => function ($max) {
					return (new GeneratorStream((function() use ($max){
						for ($i = 0; $i < $max; $i++) {
							yield $i;
						}
					})()));
				}
		]
];

if ($argc < 2 || !array_key_exists($argv[1] - 1, $benchs)) {
	throw new InvalidArgumentException("{$argv[0]} Missing or wrong parameter");
}

$bench = $benchs[$argv[1] - 1];
echo sprintf('%-35s', $argv[1] . ": " . $bench['name']) . "\t";

$max = max(100, $argv[2] ?? 5000000);
$exp = [35, 37, 39, 41, 43];
$t1 = microtime(true);

$arr = $bench['getArr']($max);

$k1 = memory_get_usage(false);
$t2 = microtime(true);

$arr = $bench['fn']($arr);

$k2 = memory_get_usage(false);

$t3 = microtime(true);
$z = $exp == $arr ? 'true' : 'false';

$x1 = sprintf('%0.10f', $t2 - $t1);
$x2 = sprintf('%0.10f', $t3 - $t2);
$x3 = sprintf('%0.10f', $t3 - $t1);

$t1 = sprintf('%0.10f', $t1);
$t2 = sprintf('%0.10f', $t2);
$t3 = sprintf('%0.10f', $t3);

$max = sprintf('%7d', $max);
$k1 = sprintf('%8d', $k1);
$k2 = sprintf('%8d', $k1);
echo "$max | $t1 | $t2 | $t3 | $x1 | $x2 | $x3 | $k1 | $k2 | $z\n";
