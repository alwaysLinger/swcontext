<?php

include "../vendor/autoload.php";

use Al\Swow\Context;

$http = new Swoole\Http\Server("0.0.0.0", 9501);

$http->set([
    'hook_flag' => SWOOLE_HOOK_ALL
]);

$http->on("start", function ($server) {
    echo 'start', PHP_EOL;
});

$http->on("request", function ($request, $response) {
    $pcid = Coroutine::getCid();
    $pctx = new Context();
    $pctx->set('a', 'a');
    $pctx->set('b', 'b');
    $pctx->set('c', 'c');
    $pctx->delete('c');
    $pContainer = $pctx->getContainer();
    var_dump($pContainer);
    go(function () use ($pcid) {
        $ctx = new Context();
        $ctx->copy($pcid);
        var_dump($ctx->getContainer());
        $ctx->set('c', 'c');
        var_dump($ctx->getContainer());
    });
    Coroutine::sleep(0.2);
    // echo 123, PHP_EOL;
    $response->end("Hello World\n");
});

$http->start();
