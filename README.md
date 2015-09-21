PHP Stream
====
PHP Stream is a basic port of new [Java Stream API](http://www.oracle.com/technetwork/articles/java/ma14-java-se-8-streams-2177646.html)
in PHP.

This library could be used to filter, convert or reduce any array. 

Continuous integration
------------
[![Build Status](https://travis-ci.org/hguenot/phpstream.svg)](https://travis-ci.org/hguenot/phpstream)
[![Code coverage](https://img.shields.io/codecov/c/github/hguenot/phpstream.svg)](https://codecov.io/github/hguenot/phpstream)

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist hguenot/phpstream "*"
```

or add

```
"hguenot/phpstream": "*"
```

to the require section of your `composer.json` file.


Usage
-----

* Here is a basic usage of this library. 

```php
// Create a simple array of data
$array = [ -10, -9, -8, -7, -6, -5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];

// Creates a Stream based on the previous array
$stream = new \phpstream\Stream($array);

// Compute the opposite value of each value
$stream = $stream->map(function($value){
    return intval($value) * (-1);
});

// Get only odd values
$stream = $stream->filter(function($value){
    return (intval($value) % 2) == 0;
});

// Collects data into an array
$new_array = $stream->collect(new \phpstream\collectors\ListCollector());

// Computes sum of all elements
$sum = $stream->reduce(function($a, $b){
    return intval($a) + intval($b);
}, 0);
```


* All stream operations can be chained : 

```php
$sum = \phpstream\Stream::of($array)
    ->map(function($value){
        return intval($value) * (-1);
    })
    ->filter(function($value){
        return (intval($value) % 2) == 0;
    })
    ->reduce(function($a, $b){
        return intval($a) + intval($b);
    }, 0);

```

You can find more examples on PHPUnit test.

