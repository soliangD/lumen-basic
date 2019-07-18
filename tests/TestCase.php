<?php

namespace Tests;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use Tests\_trait\CacheTrait;
use Tests\_trait\RequestTrait;
use Tests\_trait\ResponseTrait;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    use RequestTrait;
    use CacheTrait;
    use ResponseTrait;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    /**
     * get response data
     * @param bool $isPrint
     * @param bool $original
     * @return mixed
     */
    public function getData($isPrint = true, $original = false)
    {
        $this->assertResponseOk();
        $response = $this->response->content();
        $data = $original ? $response : @json_decode($response, true);
        if (is_null($data)) {
            $data = $response;
        }

        if ($isPrint) {
            print_r($data);
        }

        if (isset($data['data'])) {
            return $data['data'];
        }

        return $data;
    }

    /**
     * Assert that the response contains the given JSON.
     * json_encode 改为256 方便查看
     * @param array $data
     * @param bool $negate
     * @return $this|\Laravel\Lumen\Testing\TestCase|void
     */
    protected function seeJsonContains(array $data, $negate = false)
    {
        $method = $negate ? 'assertFalse' : 'assertTrue';

        $actual = json_decode($this->response->getContent(), true);

        if (is_null($actual) || $actual === false) {
            return PHPUnit::fail('Invalid JSON was returned from the route. Perhaps an exception was thrown?');
        }

        $actual = json_encode(array_sort_recursive(
            (array)$actual
        ), 256);

        foreach (array_sort_recursive($data) as $key => $value) {
            $expected = $this->formatToExpectedJson($key, $value);

            PHPUnit::{$method}(
                Str::contains($actual, $expected),
                ($negate ? 'Found unexpected' : 'Unable to find') . " JSON fragment [{$expected}] within [{$actual}]."
            );
        }

        return $this;
    }
}
