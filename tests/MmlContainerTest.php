<?php
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class MmlContainerTest extends TestCase
{
    public function test_parse() {
        $container = new MmlContainer(MmlSample::$penguin);
        var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(4, count($container->tracks));
    }
}
