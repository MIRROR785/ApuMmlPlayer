<?php
require_once('makeWaveData.php');
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\ApuMmlPlayer;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class PhraseTest extends TestCase
{
    public function test_noisePhraseTest() {
        $container = new MmlContainer([
        "Tracks" => [
            0 => "t120",
            1 => "",
            2 => "",
            3 => "",
            4 => "l4 Lo5cc#dd#eff#gg#aa#b>cc#d",
            ]
        ]);

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

        $file = __DIR__ . '/wav/noise_phrase.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }

    public function test_loopPhraseTest() {
        $container = new MmlContainer([
        "Tracks" => [
            0 => "t120",
            1 => "Lo4 l16 [3cdedef:efgfga>] l2 [2rrr]",
            2 => "Lo4 l2 rrr l16 [3cdedef:efgfga>] l2 [rrr]",
            3 => "Lo4 l2 [rrr] l16 [3cdedef:efgfga>] l2 rrr",
            4 => "l4 Lo4 l2 [2rrr] l16 [3cdedef:efgfga>]",
            ]
        ]);

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

        $file = __DIR__ . '/wav/loop_phrase.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }

    public function test_loopNestTest() {
        $container = new MmlContainer([
        "Tracks" => [
            0 => "t120",
            1 => "Lo4 l16 [[[cde]fga]>] l1 [8r]",
            2 => "Lo4 l1 [2r] l16 [[[ccdd:ee]ff:ggaa]>cc:ddee>] l1 [5r]",
            3 => "Lo4 l1 [7r] l16 [cde[fga:b>cd[efg]]:<cde>]",
            4 => "l1 [8r] v4 Lo4 l32 [[[2cc:dd]:[ee:ff]]] l1 [5r]",
            ]
        ]);

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

        $file = __DIR__ . '/wav/loop_nest.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }
}
