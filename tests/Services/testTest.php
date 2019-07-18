<?php

namespace Tests\Services;

use Laravolt\Avatar\Avatar;
use Tests\TestCase;

class testTest extends TestCase
{
    /**
     * 文字头像生成
     */
    public function testAvatar()
    {
        /** @var Avatar $avatar */
        $avatar = app('avatar');
        dd($avatar->create("测试")->toSvg());
        $avatar->create("测试")->save(storage_path('app/test.png'), 100);
        $avatar->create("ab")->save(storage_path('app/test1.png'), 100);
        $result = $avatar->create("叧");
        $result->save(storage_path('app/test2.png'), 100);
        dd((string)$result);
    }
}
