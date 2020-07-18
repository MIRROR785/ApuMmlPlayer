<?php
require_once('makeWaveData.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\{ApuMmlPlayer, Audio\AudioConst};

class MakeWavTest extends TestCase
{
    public function test_makeWav() {
        $player = new ApuMmlPlayer();

        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, -1.0 <= scale offset <= 1.0 ]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ]
            ]]]);

        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ]
            ]);

        $file = __DIR__ . "/wav/test.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }

    public function test_makeWav8000() {
        $player = new ApuMmlPlayer();

        $player->sampleRate = 8000;
        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, -1.0 <= scale offset <= 1.0 ]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ]
            ]]]);

        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ]
            ]);

        $file = __DIR__ . "/wav/test-".$player->sampleRate.".wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }

    public function test_makeWav48000() {
        $player = new ApuMmlPlayer();

        $player->sampleRate = 48000;
        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, -1.0 <= scale offset <= 1.0 ]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ]
            ]]]);

        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ]
            ]);

        $file = __DIR__ . "/wav/test-".$player->sampleRate.".wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }

    public function test_makeFloatWav() {
        $player = new ApuMmlPlayer();
        $player->sampleBits = 32;

        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, -1.0 <= scale offset <= 1.0 ]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ]
            ]]]);

        $data = $player->testSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ]
            ]);

        $file = __DIR__ . "/wav/test32.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);

        $this->assertTrue(file_exists($file));
    }

    public function test_makeOneCycleWav() {
        $player = new ApuMmlPlayer();
        $player->sampleBits = 32;

        $player->setup(
        ['AudioUnits' => [[
            'Name' => 'unit1',
            'Devices' => [
                // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
                // => ['Position' => [ -1.5 <= panning <= 1.5, scale offset ]
                1 => ['Position' => [-0.25 , 1.0]],
                2 => ['Position' => [ 0.25 , 1.0]],
                3 => ['Position' => [-0.125, 1.0]],
                4 => ['Position' => [ 0.125, 1.0]],
                ]
            ]]]);

        $data = $player->oneCycleSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ]
            ]);

        $file = __DIR__ . "/wav/testOneCycle.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));

        $player->reset();
        $data = $player->oneCycleSound([
            'unit1' => [
                1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                ]
            ]);

        $file = __DIR__ . "/wav/testOneCycle1.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));

        $player->reset();
        $data = $player->oneCycleSound([
            'unit1' => [
                2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 6)],
                ]
            ]);

        $file = __DIR__ . "/wav/testOneCycle2.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));

        $player->reset();
        $data = $player->oneCycleSound([
            'unit1' => [
                3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(4, 9)],
                ]
            ]);

        $file = __DIR__ . "/wav/testOneCycle3.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));

        $player->reset();
        $data = $player->oneCycleSound([
            'unit1' => [
                4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioConst::getNoteNo(1, 0)],
                ]
            ]);

        $file = __DIR__ . "/wav/testOneCycle4.wav";
        $handle = fopen($file, "w+b");
        fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
        fclose($handle);
        $this->assertTrue(file_exists($file));
    }
}
