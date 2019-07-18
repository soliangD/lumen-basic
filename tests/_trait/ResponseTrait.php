<?php

namespace Tests\_trait;

trait ResponseTrait
{
    protected $codeSuccess = 18000;
    protected $codeFail = 13000;

    /** @var string 状态字段名 */
    protected $statusField = 'code';

    /**
     * 断言 成功
     * @return mixed|$this
     */
    protected function assertSuccess()
    {
        return $this->seeJson([
            $this->statusField => $this->codeSuccess,
        ]);
    }
}
