<?php
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class MmlContainerTest extends TestCase
{
    public function test_parse() {
        //var_dump(MmlSample::$penguin);
        $container = new MmlContainer(MmlSample::$penguin);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(4, count($container->tracks));
    }

    public function test_json_parse() {
        //var_dump(MmlSample::$penguin_json);
        echo json_encode(MmlSample::$penguin, true);
        
        $container = new MmlContainer(MmlSample::$penguin_json);
        var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(4, count($container->tracks));

        $this->assertTrue(array_key_exists(0, $container->tracks));
        $this->assertTrue(array_key_exists(1, $container->tracks));
        $this->assertTrue(array_key_exists(2, $container->tracks));
        $this->assertTrue(array_key_exists(3, $container->tracks));
    }
}
