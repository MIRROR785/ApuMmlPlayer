ApuMmlPlayer
============

疑似APUによるMMLプレイヤーです。
出力結果は、よや(id:yoya)様の function [makeWaveData] を使って、WAVファイルで保存できます。

矩形波2ch、三角波1ch、ノイズ1chの単純な出力ができます。

スイープ制御等の複雑な処理は未実装です。
MMLのコマンドは部分的なサポートです。

It is an MML player by pseudo APU.
The output can be saved as a WAV file using function [makeWaveData] by yoya.

Simple output of 2 channels of square wave, 1 channel of triangle wave and 1 channel of noise is possible.

Complex processing such as sweep control is not implemented yet.
MML commands are partially supported.


Use extentions
--------------

* composer
* mbstring
* [makeWaveData]


Installation
------------

1. Make project directory.

    プロジェクトのディレクトリを作成。

    ```console
    $ mkdir sample
    $ cd sample
    ```


2. Create `composer.json`.

    composer.jsonを用意。

    ```json
    {
        "require": {
            "mirror785/apu-mml-player": "dev-master"
        },
        "repositories": [
           {
               "type": "vcs",
               "url": "https://github.com/MIRROR785/ApuMmlPlayer.git"
           }
        ]
    }
    ```


3. To install library from Github.

    composerを使って、Githubよりライブラリをインストール。

```console
$ php composer.phar install
```


4. Download [makeWaveData].

    makeWaveData.phpをダウンロード。


Usage
-----

1. Loading external files and use declaration.

    外部ファイルの読み込みとuse宣言。

    ```php
    <?php
    require_once('makeWaveData.php');
    require_once('vendor/autoload.php');

    use MIRROR785\ApuMmlPlayer\{ApuMmlPlayer, Mml\MmlContainer};
    ```


2. Create MmlContainer.

    MMLコンテナを作成。

    ```php
    $container = new MmlContainer([
        "Title" => "Test",
        "Tracks" => [
        /* control  */ 0 => "t120",
        /* pulse1   */ 1 => "l8 o6cdefgab>cr1cdefgab>c",
        /* pulse2   */ 2 => "l8 o4r2cdefgab>cr2cdefgab>c",
        /* triangle */ 3 => "l8 o6r1cdefgab>ccdefgab>c",
        /* noise    */ 4 => "l8 o1r1r2cdefgab>ccdef",
    ]]);
    ```


3. Create ApuMmlPlayer.

    ApuMmlPlayerを作成。

    For simple instance (CD quality, Stereo 16bits PCM):

    ```php
    $player = new ApuMmlPlayer();
    ```


        For custom instance:
    ```php
    $player = new ApuMmlPlayer(
    ['SampleRate' => 44100, // サンプリングレート
     'SampleBits' => 16,    // 量子化ビット数 (PCM: 8 or 16 bits, float PCM: 32 bits)
     'ChannelCount' => 2,   // チャンネル数 (1:Monaural, 2:Stereo)
     'VolumeScale' => 1.0,  // ボリューム拡大率
     'AudioUnits' => [
       ['Name' => 'unit0',  // オーディオユニット名
        'Devices' => [
            // Use device number (1:pulse1, 2:pulse2, 3:triangle, 4:noise) => [ Parameters ]
            // Parameters : 
            //   'Position' => [ panning, scale offset ]
            //   'Panning'  => (-1.5 <= panning <= 1.5)
            //   'Scale'    => (-1.0 <= scale offset <= 1.0)
            //   'Late'     => (0.0 <= late)
            //   'Delay'    => (0.0 <= delay)
            1 => ['Position' => [-0.25 , 1.0]],
            2 => ['Position' => [ 0.25 , 1.0]],
            3 => ['Position' => [-0.125, 1.0]],
            4 => ['Position' => [ 0.125, 1.0]]]
       ],
    ]]);
    ```


4. Set voice number for pulse device.

    矩形波の音色を指定。

    ```php
    $unit = $player->audioUnits['unit0'];
    $apu = $unit->apu;
    $apu->devices[1]->setVoice(2); // 0:12.5%(default), 1:25%, 2:50%, 3:75%
    $apu->devices[2]->setVoice(2); // 0:12.5%(default), 1:25%, 2:50%, 3:75%
    ```


5. Set sampling time and loops.

    サンプリング時間とループの指定。

    ```php
    $player->sampleTime = 60.0; // default: 1.0, max: 300.0
    $player->loopCount = 0;     // default: 0
    $player->loopEnd = true;    // default: true
    ```

    ループ終了が指定されている場合、サンプリング時間を満たす前に終了します。

    If end of loop is specified, it ends before the sampling time is met.


6. Get sampling data.

    サンプリングデータを取得。

    ```php
    $data = $player->play($container);
    ```


7. Write wave file.

    WAVEファイルとして書き出し。

    ```php
    $handle = fopen(__DIR__ . "test.wav", "wb");
    fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
    fclose($handle);
    ```


Sample
------

オーディオユニットを増設し反射音を真似た、32bit float PCMでステレオデータを生成するサンプル。

Sample to generate stereo data in 32 bit float PCM to mimic reflections by adding an audio unit.

```php
<?php
require_once('makeWaveData.php');
require_once('vendor/autoload.php');

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
]]);

$player = new ApuMmlPlayer(
['SampleRate' => 44100,
 'SampleBits' => 32,
 'ChannelCount' => 2,
 'VolumeScale' => 1.0,
 'AudioUnits' => [
   ['Name' => 'unit0',
    'Devices' => [
        1 => ['Position' => [-0.25 , 1.0]],
        2 => ['Position' => [ 0.25 , 1.0]],
        3 => ['Position' => [-0.125, 1.0]],
        4 => ['Position' => [ 0.125, 1.0]]]
   ],
   ['Name' => 'unit1',
    'Devices' => [
        1 => ['Position' => [ 0.25 , 0.5], 'Late' => 0.125],
        2 => ['Position' => [-0.25 , 0.5], 'Late' => 0.125],
        3 => ['Position' => [ 0.125, 0.5], 'Late' => 0.125],
        4 => ['Position' => [ 0.125, 0.5], 'Late' => 0.125]]
   ],
]]);

$unit = $player->audioUnits['unit0'];
$apu = $unit->apu;
$apu->devices[1]->setVoice(2);
$apu->devices[2]->setVoice(2);

$unit = $player->audioUnits['unit1'];
$apu = $unit->apu;
$apu->devices[1]->setVoice(2);
$apu->devices[2]->setVoice(2);

$player->sampleTime = 60.0;
$player->loopCount = 1;
$data = $player->play($container);

$handle = fopen(__DIR__ . "/sample.wav", "wb");
fwrite($handle, makeWaveData($data, $player->channelCount, $player->sampleBits, $player->sampleRate));
fclose($handle);
```


License
-------
Copyright &copy; 2019 @MIRROR_  
Distributed under the [MIT].  

[MIT]: http://www.opensource.org/licenses/mit-license.php "MIT License"
[makeWaveData]: https://yoya.hatenadiary.jp/entry/20130430/php "PHP で PCM wav ファイル作成"
