<?php

namespace Al\Swow;

use Swoole\ExitException;

class CtxException extends ExitException
{
    public function __toString()
    {
        return 'must called in coroutine context';
    }
}
