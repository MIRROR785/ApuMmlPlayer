<?php
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\{Mml\MmlContainer, Mml\MmlSequencer};

class MmlSequencerTest extends TestCase
{
    public function test_parse() {
        $container = new MmlContainer(MmlSample::$penguin);
        $sequencer = new MmlSequencer($container);
        var_dump($sequencer);

        $this->assertSame(false, $sequencer->isEndOfData);
    }
}
