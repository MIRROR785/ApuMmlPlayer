<?php
require_once('makeWaveData.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\{ApuMmlPlayer, Audio\AudioConst};

class MakeWavFromJsonTest extends TestCase
{
    public function test_makeWav() {
        $player = new ApuMmlPlayer();

        $player->setup(
        '{"AudioUnits": [{
            "Name": "unit1",
            "Devices": {
                "1": {"Position": [-0.25 , 1.0]},
                "2": {"Position": [ 0.25 , 1.0]},
                "3": {"Position": [-0.125, 1.0]},
                "4": {"Position": [ 0.125, 1.0]}}
            }]}');

        $data = $player->testSound('{
            "unit1": {
                "1": {"Voice": 0, "Volume": 15, "Octave": 4, "KeyNo": 9},
                "2": {"Voice": 1, "Volume": 15, "NoteNo": 42},
                "3": {"Voice": 0, "Volume": 15, "NoteNo": 45},
                "4": {"Voice": 0, "Volume": 15, "Octave": 1, "KeyNo": 0}}
            }');

        $file = __DIR__ . "/wav/testJson.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }

    public function test_makeOneCycleWav() {
        $player = new ApuMmlPlayer();
        $player->sampleBits = 32;

        $player->setup(
        '{"AudioUnits": [{
            "Name": "unit1",
            "Devices": {
                "1": {"Position": [-0.25 , 1.0]},
                "2": {"Position": [ 0.25 , 1.0]},
                "3": {"Position": [-0.125, 1.0]},
                "4": {"Position": [ 0.125, 1.0]}}
            }]}');

        $data = $player->oneCycleSound('{
            "unit1": {
                "1": {"Voice": 0, "Volume": 15, "Octave": 4, "KeyNo": 9},
                "2": {"Voice": 1, "Volume": 15, "NoteNo": 42},
                "3": {"Voice": 0, "Volume": 15, "NoteNo": 45},
                "4": {"Voice": 0, "Volume": 15, "Octave": 1, "KeyNo": 0}}
            }');

        $file = __DIR__ . "/wav/testJsonOneCycle.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }
}
