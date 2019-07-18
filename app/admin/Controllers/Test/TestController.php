<?php

namespace Admin\Controllers\Test;

use Admin\Controllers\BaseController;

class TestController extends BaseController
{
    public function demo()
    {
        $list = [
            '4566', 'fdaf', 'fdaf',
        ];

        return $this->resultSuccess($list, '这是一个测试');
    }
}
