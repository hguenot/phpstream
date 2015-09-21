#!/bin/bash

phpunit --bootstrap $(dirname $0)/../src/autoload.php .
