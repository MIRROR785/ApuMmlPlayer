<?php
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\ApuMmlPlayer;
use MIRROR785\ApuMmlPlayer\Audio\AudioConst;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class DeviceTest extends TestCase
{
    public function test_setLateTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setLate(3.5);
        $apu0->devices[2]->setLate(2.1);
        $apu0->devices[3]->setLate(1.3);
        $apu0->devices[4]->setLate(1.2);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Late'=>3.5],
                2 => ['Late'=>2.1],
                3 => ['Late'=>1.3],
                4 => ['Late'=>1.2]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Late":3.5},
                "2": {"Late":2.1},
                "3": {"Late":1.3},
                "4": {"Late":1.2}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(3.5, $apu0->devices[1]->getLate());
        $this->assertSame(2.1, $apu0->devices[2]->getLate());
        $this->assertSame(1.3, $apu0->devices[3]->getLate());
        $this->assertSame(1.2, $apu0->devices[4]->getLate());

        $this->assertSame($apu0->devices[1]->getLate(), $apu1->devices[1]->getLate());
        $this->assertSame($apu0->devices[2]->getLate(), $apu1->devices[2]->getLate());
        $this->assertSame($apu0->devices[3]->getLate(), $apu1->devices[3]->getLate());
        $this->assertSame($apu0->devices[4]->getLate(), $apu1->devices[4]->getLate());

        $this->assertSame($apu0->devices[1]->getLate(), $apu2->devices[1]->getLate());
        $this->assertSame($apu0->devices[2]->getLate(), $apu2->devices[2]->getLate());
        $this->assertSame($apu0->devices[3]->getLate(), $apu2->devices[3]->getLate());
        $this->assertSame($apu0->devices[4]->getLate(), $apu2->devices[4]->getLate());
    }

    public function test_setVoiceTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setVoice(3);
        $apu0->devices[2]->setVoice(2);
        $apu0->devices[3]->setVoice(1);
        $apu0->devices[4]->setVoice(1);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Voice'=>3],
                2 => ['Voice'=>2],
                3 => ['Voice'=>1],
                4 => ['Voice'=>1]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Voice":3},
                "2": {"Voice":2},
                "3": {"Voice":1},
                "4": {"Voice":1}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(3, $apu0->devices[1]->getVoice());
        $this->assertSame(2, $apu0->devices[2]->getVoice());
        $this->assertSame(0, $apu0->devices[3]->getVoice());
        $this->assertSame(1, $apu0->devices[4]->getVoice());

        $this->assertSame($apu0->devices[1]->getVoice(), $apu1->devices[1]->getVoice());
        $this->assertSame($apu0->devices[2]->getVoice(), $apu1->devices[2]->getVoice());
        $this->assertSame($apu0->devices[3]->getVoice(), $apu1->devices[3]->getVoice());
        $this->assertSame($apu0->devices[4]->getVoice(), $apu1->devices[4]->getVoice());

        $this->assertSame($apu0->devices[1]->getVoice(), $apu2->devices[1]->getVoice());
        $this->assertSame($apu0->devices[2]->getVoice(), $apu2->devices[2]->getVoice());
        $this->assertSame($apu0->devices[3]->getVoice(), $apu2->devices[3]->getVoice());
        $this->assertSame($apu0->devices[4]->getVoice(), $apu2->devices[4]->getVoice());
    }

    public function test_setVolumeTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setVolume(0.3);
        $apu0->devices[2]->setVolume(0.2);
        $apu0->devices[3]->setVolume(0.1);
        $apu0->devices[4]->setVolume(0.5);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Volume'=>0.3],
                2 => ['Volume'=>0.2],
                3 => ['Volume'=>0.1],
                4 => ['Volume'=>0.5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Volume":0.3},
                "2": {"Volume":0.2},
                "3": {"Volume":0.1},
                "4": {"Volume":0.5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(0.3, $apu0->devices[1]->getVolume());
        $this->assertSame(0.2, $apu0->devices[2]->getVolume());
        $this->assertSame(0.1, $apu0->devices[3]->getVolume());
        $this->assertSame(0.5, $apu0->devices[4]->getVolume());

        $this->assertSame($apu0->devices[1]->getVolume(), $apu1->devices[1]->getVolume());
        $this->assertSame($apu0->devices[2]->getVolume(), $apu1->devices[2]->getVolume());
        $this->assertSame($apu0->devices[3]->getVolume(), $apu1->devices[3]->getVolume());
        $this->assertSame($apu0->devices[4]->getVolume(), $apu1->devices[4]->getVolume());

        $this->assertSame($apu0->devices[1]->getVolume(), $apu2->devices[1]->getVolume());
        $this->assertSame($apu0->devices[2]->getVolume(), $apu2->devices[2]->getVolume());
        $this->assertSame($apu0->devices[3]->getVolume(), $apu2->devices[3]->getVolume());
        $this->assertSame($apu0->devices[4]->getVolume(), $apu2->devices[4]->getVolume());
    }

    public function test_setOffsetVolumeTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setOffsetVolume(0.3);
        $apu0->devices[2]->setOffsetVolume(0.2);
        $apu0->devices[3]->setOffsetVolume(0.1);
        $apu0->devices[4]->setOffsetVolume(0.5);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['OffsetVolume'=>0.3],
                2 => ['OffsetVolume'=>0.2],
                3 => ['OffsetVolume'=>0.1],
                4 => ['OffsetVolume'=>0.5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"OffsetVolume":0.3},
                "2": {"OffsetVolume":0.2},
                "3": {"OffsetVolume":0.1},
                "4": {"OffsetVolume":0.5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(0.3, $apu0->devices[1]->getOffsetVolume());
        $this->assertSame(0.2, $apu0->devices[2]->getOffsetVolume());
        $this->assertSame(0.1, $apu0->devices[3]->getOffsetVolume());
        $this->assertSame(0.5, $apu0->devices[4]->getOffsetVolume());

        $this->assertSame($apu0->devices[1]->getOffsetVolume(), $apu1->devices[1]->getOffsetVolume());
        $this->assertSame($apu0->devices[2]->getOffsetVolume(), $apu1->devices[2]->getOffsetVolume());
        $this->assertSame($apu0->devices[3]->getOffsetVolume(), $apu1->devices[3]->getOffsetVolume());
        $this->assertSame($apu0->devices[4]->getOffsetVolume(), $apu1->devices[4]->getOffsetVolume());

        $this->assertSame($apu0->devices[1]->getOffsetVolume(), $apu2->devices[1]->getOffsetVolume());
        $this->assertSame($apu0->devices[2]->getOffsetVolume(), $apu2->devices[2]->getOffsetVolume());
        $this->assertSame($apu0->devices[3]->getOffsetVolume(), $apu2->devices[3]->getOffsetVolume());
        $this->assertSame($apu0->devices[4]->getOffsetVolume(), $apu2->devices[4]->getOffsetVolume());
    }

    public function test_setNoteNoTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->noteOn(3);
        $apu0->devices[2]->noteOn(2);
        $apu0->devices[3]->noteOn(1);
        $apu0->devices[4]->noteOn(5);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['NoteNo'=>3],
                2 => ['NoteNo'=>2],
                3 => ['NoteNo'=>1],
                4 => ['NoteNo'=>5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"NoteNo":3},
                "2": {"NoteNo":2},
                "3": {"NoteNo":1},
                "4": {"NoteNo":5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(3, $apu0->devices[1]->getNoteNo());
        $this->assertSame(2, $apu0->devices[2]->getNoteNo());
        $this->assertSame(1, $apu0->devices[3]->getNoteNo());
        $this->assertSame(5, $apu0->devices[4]->getNoteNo());

        $this->assertSame($apu0->devices[1]->getNoteNo(), $apu1->devices[1]->getNoteNo());
        $this->assertSame($apu0->devices[2]->getNoteNo(), $apu1->devices[2]->getNoteNo());
        $this->assertSame($apu0->devices[3]->getNoteNo(), $apu1->devices[3]->getNoteNo());
        $this->assertSame($apu0->devices[4]->getNoteNo(), $apu1->devices[4]->getNoteNo());

        $this->assertSame($apu0->devices[1]->getNoteNo(), $apu2->devices[1]->getNoteNo());
        $this->assertSame($apu0->devices[2]->getNoteNo(), $apu2->devices[2]->getNoteNo());
        $this->assertSame($apu0->devices[3]->getNoteNo(), $apu2->devices[3]->getNoteNo());
        $this->assertSame($apu0->devices[4]->getNoteNo(), $apu2->devices[4]->getNoteNo());
    }

    public function test_setKeyNoTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->noteOn(AudioConst::getNoteNo(1, 3));
        $apu0->devices[2]->noteOn(AudioConst::getNoteNo(2, 2));
        $apu0->devices[3]->noteOn(AudioConst::getNoteNo(3, 1));
        $apu0->devices[4]->noteOn(AudioConst::getNoteNo(0, 5));

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Octave'=>1, 'KeyNo'=>3],
                2 => ['Octave'=>2, 'KeyNo'=>2],
                3 => ['Octave'=>3, 'KeyNo'=>1],
                4 => ['Octave'=>0, 'KeyNo'=>5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Octave":1, "KeyNo":3},
                "2": {"Octave":2, "KeyNo":2},
                "3": {"Octave":3, "KeyNo":1},
                "4": {"Octave":0, "KeyNo":5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(AudioConst::getNoteNo(1, 3), $apu0->devices[1]->getNoteNo());
        $this->assertSame(AudioConst::getNoteNo(2, 2), $apu0->devices[2]->getNoteNo());
        $this->assertSame(AudioConst::getNoteNo(3, 1), $apu0->devices[3]->getNoteNo());
        $this->assertSame(AudioConst::getNoteNo(0, 5), $apu0->devices[4]->getNoteNo());

        $this->assertSame($apu0->devices[1]->getNoteNo(), $apu1->devices[1]->getNoteNo());
        $this->assertSame($apu0->devices[2]->getNoteNo(), $apu1->devices[2]->getNoteNo());
        $this->assertSame($apu0->devices[3]->getNoteNo(), $apu1->devices[3]->getNoteNo());
        $this->assertSame($apu0->devices[4]->getNoteNo(), $apu1->devices[4]->getNoteNo());

        $this->assertSame($apu0->devices[1]->getNoteNo(), $apu2->devices[1]->getNoteNo());
        $this->assertSame($apu0->devices[2]->getNoteNo(), $apu2->devices[2]->getNoteNo());
        $this->assertSame($apu0->devices[3]->getNoteNo(), $apu2->devices[3]->getNoteNo());
        $this->assertSame($apu0->devices[4]->getNoteNo(), $apu2->devices[4]->getNoteNo());
    }

    public function test_setOffsetNoteTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setOffsetNote(3);
        $apu0->devices[2]->setOffsetNote(2);
        $apu0->devices[3]->setOffsetNote(1);
        $apu0->devices[4]->setOffsetNote(5);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['OffsetNote'=>3],
                2 => ['OffsetNote'=>2],
                3 => ['OffsetNote'=>1],
                4 => ['OffsetNote'=>5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"OffsetNote":3},
                "2": {"OffsetNote":2},
                "3": {"OffsetNote":1},
                "4": {"OffsetNote":5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(3, $apu0->devices[1]->getOffsetNote());
        $this->assertSame(2, $apu0->devices[2]->getOffsetNote());
        $this->assertSame(1, $apu0->devices[3]->getOffsetNote());
        $this->assertSame(5, $apu0->devices[4]->getOffsetNote());

        $this->assertSame($apu0->devices[1]->getOffsetNote(), $apu1->devices[1]->getOffsetNote());
        $this->assertSame($apu0->devices[2]->getOffsetNote(), $apu1->devices[2]->getOffsetNote());
        $this->assertSame($apu0->devices[3]->getOffsetNote(), $apu1->devices[3]->getOffsetNote());
        $this->assertSame($apu0->devices[4]->getOffsetNote(), $apu1->devices[4]->getOffsetNote());

        $this->assertSame($apu0->devices[1]->getOffsetNote(), $apu2->devices[1]->getOffsetNote());
        $this->assertSame($apu0->devices[2]->getOffsetNote(), $apu2->devices[2]->getOffsetNote());
        $this->assertSame($apu0->devices[3]->getOffsetNote(), $apu2->devices[3]->getOffsetNote());
        $this->assertSame($apu0->devices[4]->getOffsetNote(), $apu2->devices[4]->getOffsetNote());
    }

    public function test_setOffsetFrequencyTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setOffsetFrequency(3);
        $apu0->devices[2]->setOffsetFrequency(2);
        $apu0->devices[3]->setOffsetFrequency(1);
        $apu0->devices[4]->setOffsetFrequency(5);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['OffsetFrequency'=>3],
                2 => ['OffsetFrequency'=>2],
                3 => ['OffsetFrequency'=>1],
                4 => ['OffsetFrequency'=>5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"OffsetFrequency":3},
                "2": {"OffsetFrequency":2},
                "3": {"OffsetFrequency":1},
                "4": {"OffsetFrequency":5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(3, $apu0->devices[1]->getOffsetFrequency());
        $this->assertSame(2, $apu0->devices[2]->getOffsetFrequency());
        $this->assertSame(1, $apu0->devices[3]->getOffsetFrequency());
        $this->assertSame(0, $apu0->devices[4]->getOffsetFrequency());

        $this->assertSame($apu0->devices[1]->getOffsetFrequency(), $apu1->devices[1]->getOffsetFrequency());
        $this->assertSame($apu0->devices[2]->getOffsetFrequency(), $apu1->devices[2]->getOffsetFrequency());
        $this->assertSame($apu0->devices[3]->getOffsetFrequency(), $apu1->devices[3]->getOffsetFrequency());
        $this->assertSame($apu0->devices[4]->getOffsetFrequency(), $apu1->devices[4]->getOffsetFrequency());

        $this->assertSame($apu0->devices[1]->getOffsetFrequency(), $apu2->devices[1]->getOffsetFrequency());
        $this->assertSame($apu0->devices[2]->getOffsetFrequency(), $apu2->devices[2]->getOffsetFrequency());
        $this->assertSame($apu0->devices[3]->getOffsetFrequency(), $apu2->devices[3]->getOffsetFrequency());
        $this->assertSame($apu0->devices[4]->getOffsetFrequency(), $apu2->devices[4]->getOffsetFrequency());
    }

    public function test_setDelayTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->setDelay(0.3);
        $apu0->devices[2]->setDelay(0.2);
        $apu0->devices[3]->setDelay(0.1);
        $apu0->devices[4]->setDelay(0.5);

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Delay'=>0.3],
                2 => ['Delay'=>0.2],
                3 => ['Delay'=>0.1],
                4 => ['Delay'=>0.5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Delay":0.3},
                "2": {"Delay":0.2},
                "3": {"Delay":0.1},
                "4": {"Delay":0.5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertSame(0.3, $apu0->devices[1]->getDelay());
        $this->assertSame(0.2, $apu0->devices[2]->getDelay());
        $this->assertSame(0.1, $apu0->devices[3]->getDelay());
        $this->assertSame(0.5, $apu0->devices[4]->getDelay());

        $this->assertSame($apu0->devices[1]->getDelay(), $apu1->devices[1]->getDelay());
        $this->assertSame($apu0->devices[2]->getDelay(), $apu1->devices[2]->getDelay());
        $this->assertSame($apu0->devices[3]->getDelay(), $apu1->devices[3]->getDelay());
        $this->assertSame($apu0->devices[4]->getDelay(), $apu1->devices[4]->getDelay());

        $this->assertSame($apu0->devices[1]->getDelay(), $apu2->devices[1]->getDelay());
        $this->assertSame($apu0->devices[2]->getDelay(), $apu2->devices[2]->getDelay());
        $this->assertSame($apu0->devices[3]->getDelay(), $apu2->devices[3]->getDelay());
        $this->assertSame($apu0->devices[4]->getDelay(), $apu2->devices[4]->getDelay());
    }

    public function test_setNoteTest() {
        $player0 = new ApuMmlPlayer();
        $player0->setup();
        $audioUnit0 = $player0->audioUnits['unit0'];
        $apu0 = $audioUnit0->apu;
        $apu0->devices[1]->noteOn(AudioConst::getNoteNo(1, 3));
        $apu0->devices[2]->noteOn(AudioConst::getNoteNo(2, 2));
        $apu0->devices[3]->noteOn(AudioConst::getNoteNo(3, 1));
        $apu0->devices[4]->noteOn(AudioConst::getNoteNo(0, 5));

        $player1 = new ApuMmlPlayer();
        $player1->setup();
        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Octave'=>1, 'KeyNo'=>3],
                2 => ['Octave'=>2, 'KeyNo'=>2],
                3 => ['Octave'=>3, 'KeyNo'=>1],
                4 => ['Octave'=>0, 'KeyNo'=>5]
            ]]);

        $player2 = new ApuMmlPlayer();
        $player2->setup();
        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Octave":1, "KeyNo":3},
                "2": {"Octave":2, "KeyNo":2},
                "3": {"Octave":3, "KeyNo":1},
                "4": {"Octave":0, "KeyNo":5}
            }}');

        $audioUnit1 = $player1->audioUnits['unit0'];
        $apu1 = $audioUnit1->apu;

        $audioUnit2 = $player2->audioUnits['unit0'];
        $apu2 = $audioUnit2->apu;

        $this->assertFalse($apu0->devices[1]->isNoteOff());
        $this->assertFalse($apu0->devices[2]->isNoteOff());
        $this->assertFalse($apu0->devices[3]->isNoteOff());
        $this->assertFalse($apu0->devices[4]->isNoteOff());

        $this->assertFalse($apu1->devices[1]->isNoteOff());
        $this->assertFalse($apu1->devices[2]->isNoteOff());
        $this->assertFalse($apu1->devices[3]->isNoteOff());
        $this->assertFalse($apu1->devices[4]->isNoteOff());

        $this->assertFalse($apu2->devices[1]->isNoteOff());
        $this->assertFalse($apu2->devices[2]->isNoteOff());
        $this->assertFalse($apu2->devices[3]->isNoteOff());
        $this->assertFalse($apu2->devices[4]->isNoteOff());


        $apu0->devices[1]->noteOff();
        $apu0->devices[2]->noteOff();
        $apu0->devices[3]->noteOff();
        $apu0->devices[4]->noteOff();

        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Note'=>false],
                2 => ['Note'=>false],
                3 => ['Note'=>false],
                4 => ['Note'=>false]
            ]]);

        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Note":false},
                "2": {"Note":false},
                "3": {"Note":false},
                "4": {"Note":false}
            }}');

        $this->assertTrue($apu0->devices[1]->isNoteOff());
        $this->assertTrue($apu0->devices[2]->isNoteOff());
        $this->assertTrue($apu0->devices[3]->isNoteOff());
        $this->assertTrue($apu0->devices[4]->isNoteOff());

        $this->assertTrue($apu1->devices[1]->isNoteOff());
        $this->assertTrue($apu1->devices[2]->isNoteOff());
        $this->assertTrue($apu1->devices[3]->isNoteOff());
        $this->assertTrue($apu1->devices[4]->isNoteOff());

        $this->assertTrue($apu2->devices[1]->isNoteOff());
        $this->assertTrue($apu2->devices[2]->isNoteOff());
        $this->assertTrue($apu2->devices[3]->isNoteOff());
        $this->assertTrue($apu2->devices[4]->isNoteOff());


        $apu0->devices[1]->noteOn();
        $apu0->devices[2]->noteOn();
        $apu0->devices[3]->noteOn();
        $apu0->devices[4]->noteOn();

        $player1->setDeviceParameter([
            'unit0' => [
                1 => ['Note'=>true],
                2 => ['Note'=>true],
                3 => ['Note'=>true],
                4 => ['Note'=>true]
            ]]);

        $player2->setDeviceParameter('{
            "unit0": {
                "1": {"Note":true},
                "2": {"Note":true},
                "3": {"Note":true},
                "4": {"Note":true}
            }}');

        $this->assertFalse($apu0->devices[1]->isNoteOff());
        $this->assertFalse($apu0->devices[2]->isNoteOff());
        $this->assertFalse($apu0->devices[3]->isNoteOff());
        $this->assertFalse($apu0->devices[4]->isNoteOff());

        $this->assertFalse($apu1->devices[1]->isNoteOff());
        $this->assertFalse($apu1->devices[2]->isNoteOff());
        $this->assertFalse($apu1->devices[3]->isNoteOff());
        $this->assertFalse($apu1->devices[4]->isNoteOff());

        $this->assertFalse($apu2->devices[1]->isNoteOff());
        $this->assertFalse($apu2->devices[2]->isNoteOff());
        $this->assertFalse($apu2->devices[3]->isNoteOff());
        $this->assertFalse($apu2->devices[4]->isNoteOff());
    }
}
