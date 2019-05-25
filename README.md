ApuMmlPlayer
============

疑似APUによるMMLプレイヤーです。  
出力結果は "[PHP で PCM wav ファイル作成][1]" の function makeWaveData を使って、WAVファイルで保存できます。  

矩形波2ch、三角波1ch、ノイズ1chの単純な出力ができます。  

スイープ制御等の複雑な処理は未実装です。  
MML解析および演奏処理は未実装です。  

It is an MML player by pseudo APU.  
The output can be saved as a WAV file using function makeWaveData in "[PHP で PCM wav ファイル作成][1]".  

Simple output of 2 channels of square wave, 1 channel of triangle wave and 1 channel of noise is possible.  

Complex processing such as sweep control is not implemented yet.  
MML analysis and performance processing are not implemented yet.  


Usage
-----

test.php

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


License
-------
Copyright &copy; 2019 @MIRROR_  
Distributed under the [MIT License][MIT].  

[MIT]: http://www.opensource.org/licenses/mit-license.php
[1]: https://yoya.hatenadiary.jp/entry/20130430/php
