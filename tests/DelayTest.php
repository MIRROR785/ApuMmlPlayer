<?php
require_once('makeWaveData.php');
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\ApuMmlPlayer;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class DelayTest extends TestCase
{
    public function test_delayTest() {
        $container = new MmlContainer(MmlSample::$penguin);

        $player = new ApuMmlPlayer();
        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, scale offset ], 'Delay' => delay start time ]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ],
            ],
            [
            'Name' => 'unit2',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, scale offset ], 'Delay' => delay start time ]
                1 => ['Position' => [-0.25 , 0.5], 'Delay' => 0.0625],
                2 => ['Position' => [ 0.25 , 0.5], 'Delay' => 0.0625],
                3 => ['Position' => [-0.125, 0.5], 'Delay' => 0.0625],
                4 => ['Position' => [ 0.125, 0.5], 'Delay' => 0.0625],
                ],
        ]]]);
        $player->sampleTime = 60.0;
        $data = $player->play($container);

        $file = __DIR__ . '/wav/delayTest.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }
}
