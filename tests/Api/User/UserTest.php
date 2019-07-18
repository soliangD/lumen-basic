<?php

namespace Tests\Api\User;

use Tests\Api\ApiTestBase;

class UserTest extends ApiTestBase
{
    public function testInfo()
    {
        $params = [
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2FwaVwvYXBpXC91c2VyXC9sb2dpblwvbG9naW4iLCJpYXQiOjE1NjExODY5ODksImV4cCI6MTU2MTE4NzA0OSwibmJmIjoxNTYxMTg2OTg5LCJqdGkiOiJmNnE0Wmd3bzdiWjVlWUFtIiwic3ViIjoxMjcsInBydiI6IjdmODk5Yzk3MWUxZWE0ZDU0ZTNlNzUwZmZiMzEyNWM3MWY3MWQ3YjIifQ.EyRa1ekhx8X3yti0y4c73ALt_bEMJYQTl6vImdOg95s',
        ];
        $this->get('api/api/user/user/info', $params)->getData();
    }
}
