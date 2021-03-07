<?php

include_once 'vendor/autoload.php';

die(json_encode(\phpstream\Stream::of([1, 3])->map(true)->toArray()));