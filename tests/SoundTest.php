<?php
require_once('makeWaveData.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\ApuMmlPlayer;
use MIRROR785\ApuMmlPlayer\Audio\AudioConst;

class SoundTest extends TestCase
{
    public function test_soundTest() {
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

        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                ],
            ]);
        $file = __DIR__ . '/wav/pulse1.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));


        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                ],
            ]);
        $file = __DIR__ . '/wav/pulse2.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));


        $data = $player->testSound([
            'unit1' => [
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                ],
            ]);
        $file = __DIR__ . '/wav/triangle.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));


        $data = $player->testSound([
            'unit1' => [
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ],
            ]);
        $file = __DIR__ . '/wav/noise.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));


        $player->volumeScale = 1.0;
        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ],
            ]);
        $file = __DIR__ . '/wav/all-1.0.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));


        $player->volumeScale = 0.8;
        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ],
            ]);
        $file = __DIR__ . '/wav/all-0.8.wav';
        $handle = fopen($file, 'wb');
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }
}
