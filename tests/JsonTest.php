<?php
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\Container\Json;

class JsonTest extends TestCase
{
    public function test_json_parse() {
        $container = Json::ToArray(MmlSample::$penguin_json);
        var_dump($container);

        $this->assertSame(MmlSample::$penguin, $container);
    }
}
