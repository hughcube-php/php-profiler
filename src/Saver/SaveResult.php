<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2024/1/1
 * Time: 02:42
 */

namespace HughCube\Profiler\Saver;

class SaveResult
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * @var callable
     */
    private $awaitCallable;

    /**
     * @var callable
     */
    private $isSuccessCallable;

    public function __construct($result, callable $awaitCallable, callable $isSuccessCallable)
    {
        $this->result = $result;
        $this->awaitCallable = $awaitCallable;
        $this->isSuccessCallable = $isSuccessCallable;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    public function isSuccess(): bool
    {
        $this->await();

        return call_user_func($this->isSuccessCallable, $this->getResult());
    }

    /**
     * @return void
     */
    public function await()
    {
        call_user_func($this->awaitCallable, $this->getResult());
    }

    public function __destruct()
    {
        $this->await();
    }
}
