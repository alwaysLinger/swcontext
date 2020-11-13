<?php

include "../vendor/autoload.php";

use Al\Swow\Context;

try {
    $ctx = new Context();
    $ctx->getContainer();
} catch (\Throwable $th) {
    var_dump((string) $th);
}
