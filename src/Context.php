<?php

namespace Al\Swow;

use Swoole\Coroutine;

// 因为swoole是单线程的 因此可以安全使用cid
class Context
{
    public function inCoroutine()
    {
        $throwable = Coroutine::getCid() < 0;
        if ($throwable) {
            throw new Exception("must called in coroutine context");
        }
        return true;
    }

    public function set(string $id, $value)
    {
        if ($this->inCoroutine()) {
            Coroutine::getContext()[$id] = $value;
            return $value;
        }
    }

    public function has(string $id, $cid = null)
    {
        if ($this->inCoroutine()) {
            if ($cid !== null) {
                return isset(Coroutine::getContext($cid)[$id]);
            }
            return isset(Coroutine::getContext()[$id]);
        }
    }

    public function get(string $id, $cid = null, $default = null)
    {
        if ($this->inCoroutine()) {
            if ($cid !== null) {
                return Coroutine::getContext($cid)[$id] ?? $default;
            }
            return Coroutine::getContext()[$id] ?? $default;
        }
    }

    public function getOrSet(string $id, $value)
    {
        if ($this->inCoroutine()) {
            return $this->has($id) ? $this->get($id) : $this->set($id, $value);
        }
    }

    public function delete(string $id)
    {
        if ($this->inCoroutine()) {
            unset(Coroutine::getContext()[$id]);
        }
    }

    /**
     * return current coroutine container
     */
    public function getContainer()
    {
        if ($this->inCoroutine()) {
            return Coroutine::getContext()->getArrayCopy();
        }
    }

    /**
     * copy the context from a given coroutine
     */
    public function copy(int $sourceCid)
    {
        if ($this->inCoroutine()) {
            $current = Coroutine::getContext();
            $givenCtx = Coroutine::getContext($sourceCid);
            $givenValues = $givenCtx->getArrayCopy();
            $current->exchangeArray($givenValues);
            return $givenValues;
        }
    }
}
