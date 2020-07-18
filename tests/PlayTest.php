<?php
require_once('makeWaveData.php');
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\ApuMmlPlayer;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class PlayTest extends TestCase
{
    public function test_playTest() {
        $container = new MmlContainer(MmlSample::$penguin);

        $player = new ApuMmlPlayer();
        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, scale offset ]]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ],
            ]]]);
        $player->sampleTime = 60.0;
        $audioUnit = $player->audioUnits['unit1'];
        $apu = $audioUnit->apu;
        $apu->devices[1]->setVoice(2);
        $apu->devices[2]->setVoice(2);
        $data = $player->play($container);

        $file = __DIR__ . '/wav/penguin.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }
}
