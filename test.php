<?php
require_once('ApuMmlPlayer.php');
require_once('makeWaveData.php');

$player = new ApuMmlPlayer();

$player->setup([[
	'Name' => 'apu1',
	'Devices' => [
		// device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
		// => ['Position' => [ -1.5 <= panning <= 1.5, -1.0 <= scale offset <= 1.0 ]
		1 => ['Position' => [-0.25 , 0]],
		2 => ['Position' => [ 0.25 , 0]],
		3 => ['Position' => [-0.125, 0]],
		4 => ['Position' => [ 0.125, 0]],
		],
	],
]);

$data = $player->testSound([
	'apu1' => [
		1 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioUtil::getNoteNo(4, 9)],
		2 => ['Voice' => 1, 'Volume' => 15, 'NoteNo' => AudioUtil::getNoteNo(4, 6)],
		3 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioUtil::getNoteNo(4, 9)],
		4 => ['Voice' => 0, 'Volume' => 15, 'NoteNo' => AudioUtil::getNoteNo(1, 0)],
	],
]);
$handle = fopen("test.wav", "wb");
fwrite($handle, makeWaveData($data, $player->nChannel, $player->sampleBits, $player->sampleRate));
fclose($handle);
