ApuMmlPlayer
============

疑似APUによるMMLプレイヤーです。
出力結果は function [makeWaveData] を使って、WAVファイルで保存できます。

矩形波2ch、三角波1ch、ノイズ1chの単純な出力ができます。

スイープ制御等の複雑な処理は未実装です。
MMLのコマンドは部分的なサポートです。

It is an MML player by pseudo APU.
The output can be saved as a WAV file using function [makeWaveData].

Simple output of 2 channels of square wave, 1 channel of triangle wave and 1 channel of noise is possible.

Complex processing such as sweep control is not implemented yet.
MML commands are partially supported.


Use extentions
--------------
* composer
* mbstring
* makeWaveData

Usage
-----

	<?php
	require_once('makeWaveData.php');
	require_once('autoload.php');
	
	use MIRROR785\ApuMmlPlayer\{ApuMmlPlayer, Mml\MmlContainer};
	
	$container = new MmlContainer([
	    "Title" => "ICE BALLER - Penguin",
	    "Composer" => "Alma",
	    "Arranger" => "@MIRROR_",
	    "Tracks" => [
	        0 => "t120",
	        1 => "l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr",
	        2 => "l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed",
	        3 => "l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba",
	        ]
	    ]
	);
	
	$player = new ApuMmlPlayer(
	['AudioUnits' => [[
	    'Name' => 'unit1',
	    'Devices' => [
	        // device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise)
	        // => ['Position' => [ -1.5 <= panning <= 1.5, scale offset ]
	        1 => ['Position' => [-0.25 , 1.0]],
	        2 => ['Position' => [ 0.25 , 1.0]],
	        3 => ['Position' => [-0.125, 1.0]],
	        4 => ['Position' => [ 0.125, 1.0]],
	    ],
	]]]);
	
	$player->sampleRate = 44100;
	$player->sampleBits = 32;
	$player->channelCount = 2;
	$player->volumeScale = 1.0;
	$player->sampleTime = 60.0;
	$player->loopCount = 0;
	$player->loopEnd = true;
	$player->reset();
	
	$unit = $player->audioUnits['unit1'];
	$apu = $unit->apu;
	$apu->devices[1]->setVoice(2);
	$apu->devices[2]->setVoice(2);
	
	$data = $player->play($container);
	
	$handle = fopen(__DIR__ . "test.wav", "wb");
	fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
	fclose($handle);


License
-------
Copyright &copy; 2019 @MIRROR_  
Distributed under the [MIT].  

[MIT]: http://www.opensource.org/licenses/mit-license.php "MIT License"
[makeWaveData]: https://yoya.hatenadiary.jp/entry/20130430/php "PHP で PCM wav ファイル作成"
