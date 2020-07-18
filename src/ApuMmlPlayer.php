<?php
/**
 * ApuMmlPlayer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer;

use MIRROR785\ApuMmlPlayer\Audio\{AudioConst, AudioUnit, AudioMixer, AudioPacker};
use MIRROR785\ApuMmlPlayer\Mml\{MmlContainer, MmlSequencer};

/**
 * 擬似APUによるMMLプレイヤークラス
 */
class ApuMmlPlayer
{
    /** @var int サンプリングレート */
    public $sampleRate = 44100;		// CD quality

    /** @var int 量子化ビット数 */
    public $sampleBits = 16;		// PCM : 8 or 16 bits, float PCM : 32 bits

    /** @var int チャンネル数 */
    public $channelCount = 2;			// 1:Monaural, 2:Stereo

    /** @var double ボリューム拡大率 */
    public $volumeScale = 1.0;

    /** @var double サンプリング時間(sec) */
    public $sampleTime = 1.0;		// seconds

    /** @var int ループカウンタ */
    public $loopCount = 0;

    /** @var bool ループ終了判定 */
    public $loopEnd = true;

    /** @var AudioUnit[] オーディオユニット配列 */
    public $audioUnits = null;

    /** @var AudioMixer オーディオミキサー */
    public $mixer = null;

    /** @var AudioPacker オーディオパッカー */
    public $packer = null;

    /** @var int サンプリング時間 */
    const MaxSampleTime = 5 * 60;

    /**
     * コンストラクタ
     * @param string|array $config 設定情報
     */
    public function __construct($config = null) {
        AudioConst::initialize();

        if ($config !== null) {
            $this->setup($config);
        }
    }

    /**
     * 入力値の検証
     */
    private function validate() {
        if ($this->sampleTime < 0) {
            $this->sampleTime = 1.0;
        }

        if ($this->sampleTime > ApuMmlPlayer::MaxSampleTime) {
            $this->sampleTime = ApuMmlPlayer::MaxSampleTime;
        }

        if ($this->audioUnits === null) {
            $this->setup();
        }
    }

    /**
     * 初期設定
     * @param string|array $config 設定情報
     *
     * string (JSON)の場合：
     * [
     *  "SampleRate": サンプリングレート, //（省略可）
     *  "SampleBits": 量子化ビット数,     //（省略可）PCM : 8 or 16 bits, float PCM : 32 bits
     *  "ChannelCount": チャンネル数,     //（省略可）1:Monaural, 2:Stereo
     *  "VolumeScale": ボリューム拡大率,  //（省略可）
     *
     *  "AudioUnits": [[
     *    "Name": "オーディオユニット名", //（省略可）
     *    "Devices": [
     *        "デバイス番号"  // 1:pulse1, 2:pulse2, 3:triangle, 4:noise
     *        : [             // デバイス詳細定義（省略可）
     *            "Position": [音像定位, 音量増幅率],
     *            "Panning": 音像定位,  // -1.5 <= panning <= 1.5
     *            "Scale": 音量増幅率,  // -1.0 <= scale   <= 1.0
     *            "Late" : 開始遅延秒,  //  0.0 <= late
     *            "Delay": 発音遅延秒   //  0.0 <= delay
     *        ]
     *     ]
     *   ], ... ]
     * ]
     *
     * arrayの場合：
     * [
     *  'SampleRate' => サンプリングレート, //（省略可）
     *  'SampleBits' => 量子化ビット数,     //（省略可）PCM : 8 or 16 bits, float PCM : 32 bits
     *  'ChannelCount' => チャンネル数,     //（省略可）1:Monaural, 2:Stereo
     *  'VolumeScale' => ボリューム拡大率,  //（省略可）
     *
     *  'AudioUnits' => [[
     *    'Name' => 'オーディオユニット名', //（省略可）
     *    'Devices' => [
     *        デバイス番号 // 1:pulse1, 2:pulse2, 3:triangle, 4:noise
     *        => [         // デバイス詳細定義（省略可）
     *            'Position'=> [音像定位, 音量増幅率],
     *            'Panning'=> 音像定位,   // -1.5 <= panning <= 1.5
     *            'Scale'  => 音量増幅率, // -1.0 <= scale   <= 1.0
     *            'Late'   => 開始遅延秒, //  0.0 <= late
     *            'Delay'  => 発音遅延秒  //  0.0 <= delay
     *        ]
     *     ]
     *   ], ... ]
     * ]
     */
    public function setup($config = null) {
        // 引数確認
        if ($config === null) {
            $config = ['AudioUnits' => [['Devices'=>[1, 2, 3, 4]]]];
        }
        $values = is_array($config) ? $config : json_decode($config);

        // パラメータ、オーディオユニット設定
        foreach ($values as $key => $value) {
            switch ($key) {
            case 'SampleRate':
                $this->sampleRate = $value;
                break;

            case 'SampleBits':
                $this->sampleBits = $value;
                break;

            case 'ChannelCount':
                $this->channelCount = $value;
                break;

            case 'VolumeScale':
                $this->volumeScale = $value;
                break;

            case 'AudioUnits':
                $this->audioUnits = [];
                foreach ($value as $params) {
                    $audioUnit = new AudioUnit($this->sampleRate, $params);
                    if ($audioUnit->name === null) {
                        $audioUnit->name = 'unit' . count($this->audioUnits);
                    }
                    $this->audioUnits[$audioUnit->name] = $audioUnit;
                }
                break;
            }
        }

        // オーディオユニット及び出力設定の初期化
        $this->reset();
    }

    /**
     * オーディオユニット及び出力設定の初期化
     */
    public function reset() {
        // オーディオユニットの初期化
        foreach ($this->audioUnits as $audioUnit) {
            $audioUnit->apu->reset();
        }

        // 出力設定の初期化
        $this->mixer = AudioMixer::create($this->channelCount);
        $this->packer = AudioPacker::create($this->channelCount, $this->sampleBits);
    }

    /**
     * 最大開始遅延時間を取得
     * @return 最大開始遅延時間
     */
    public function getMaxLate() {
        $result = 0;
        foreach ($this->audioUnits as $audioUnit) {
            $late = $audioUnit->getMaxLate();
            if ($result < $late) {
                $result = $late;
            }
        }
        return $result;
    }

    /**
     * オーディオユニット毎の曲番を指定して演奏結果を出力
     * @param MmlContainer $container MMLデータコンテナ
     * @return byte[] WAVデータ
     */
    public function play($container) {
        // プロパティ検証
        $this->validate();

        // サンプリング初期化
        $loopCount = $this->loopCount;
        $sequencer = new MmlSequencer($container);
        $sequenceCount = floor($this->sampleRate / 60);
        $sequenceStop = false;
        $totalSamples = $this->sampleRate * $this->sampleTime;
        $lateCount = (int)($this->sampleRate * $this->getMaxLate());
        $data = '';

        // MML演奏結果サンプリング
        for ($i = 0, $j = 0; $i < $totalSamples; ++$i) {
            // シーケンサー制御
            if ($sequenceStop) {
                if ($lateCount > 0) {
                    --$lateCount;
                } else {
                    break;
                }

            } elseif ($j-- <= 0) {
                // 終端検知
                if ($this->loopEnd && $sequencer->isEndOfData) {
                    if ($loopCount <= 0) {
                        if ($lateCount > 0) {
                            --$lateCount;
                            $sequenceStop = true;
                        } else {
                            break;
                        }
                    } else {
                        --$loopCount;
                    }
                }

                if (!$sequenceStop) {
                    $sequencer->tick($this->audioUnits);
                }
                $j = $sequenceCount;
            }

            // サウンドサンプリング
            $rawData = [];
            foreach ($this->audioUnits as $audioUnit) {
                // サンプリング
                $sampling = $audioUnit->apu->sampling();

                // ミキシング
                $mixing = $this->mixer->mixing($audioUnit->trackNumbers, $audioUnit->positions, $sampling);

                $rawData[] = $mixing;
            }

            // パッキング
            $data .= $this->packer->packing($this->volumeScale, $rawData);
        }

        return $data;
    }

    /**
     * テストサウンドの出力
     * @param key=>value $notes 発音指示情報
     * ['Voice'=>音色番号, 'Volume'=>音量, 'NoteNo'=>ノート番号]
     * @return byte[] WAVデータ
     */
    public function testSound($notes) {
        // プロパティ検証
        $this->validate();

        // サンプリング初期化
        $totalSamples = $this->sampleRate * $this->sampleTime;
        $data = '';

        // テストサウンド設定
        foreach ($notes as $name => $values) {
            $apu = $this->audioUnits[$name]->apu;
            foreach ($values as $trackNo => $note) {
                $device = $apu->devices[$trackNo];
                $device->setVoice($note['Voice']);
                $device->setVolume($note['Volume']);
                $device->noteOn($note['NoteNo']);
            }
        }

        // テストサウンドサンプリング
        for ($i = 0; $i < $totalSamples; ++$i) {

            $rawData = [];
            foreach ($this->audioUnits as $audioUnit) {
                // サンプリング
                $sampling = $audioUnit->apu->sampling();

                // ミキシング
                $mixing = $this->mixer->mixing($audioUnit->trackNumbers, $audioUnit->positions, $sampling);

                $rawData[] = $mixing;
            }

            // パッキング
            $data .= $this->packer->packing($this->volumeScale, $rawData);
        }

        return $data;
    }

    /**
     * 1周期サウンドの出力
     * @param key=>value $notes 発音指示情報
     * ['Voice'=>音色番号, 'Volume'=>音量, 'NoteNo'=>ノート番号]
     * @return byte[] WAVデータ
     */
    public function oneCycleSound($notes) {
        // プロパティ検証
        $this->validate();

        // テストサウンド設定
        $lcmFrequency = 0;
        foreach ($notes as $name => $values) {
            $apu = $this->audioUnits[$name]->apu;
            foreach ($values as $trackNo => $note) {
                $device = $apu->devices[$trackNo];
                $device->setVoice($note['Voice']);
                $device->setVolume($note['Volume']);
                $device->noteOn($note['NoteNo']);

                $freq = $device->getCurrentFrequency();
                $lcmFrequency = self::getLcmFrequency($freq, $lcmFrequency);
            }
        }

        // サンプリング初期化
        $totalSamples = $this->sampleRate / self::getGcdFrequency($lcmFrequency, $this->sampleRate);
        $data = '';

        // テストサウンドサンプリング
        for ($i = 0; $i < $totalSamples; ++$i) {

            $rawData = [];
            foreach ($this->audioUnits as $audioUnit) {
                // サンプリング
                $sampling = $audioUnit->apu->sampling();

                // ミキシング
                $mixing = $this->mixer->mixing($audioUnit->trackNumbers, $audioUnit->positions, $sampling);

                $rawData[] = $mixing;
            }

            // パッキング
            $data .= $this->packer->packing($this->volumeScale, $rawData);
        }

        return $data;
    }

    /**
     * 最大公約数となる周波数を算出
     * @param int $v0 値0
     * @param int $v1 値1
     * @param int 最大公約数
     */
    private static function getGcdFrequency($v0, $v1) {
        if ($v0 < $v1) {
            $v0 = $v0 ^ $v1;
            $v1 = $v0 ^ $v1;
            $v0 = $v0 ^ $v1;
        }

        if ($v1 <= 0) {
            return $v0;
        }

        // 最大公約数
        for ($a = $v0, $b = $v1, $r = $a % $b;
             $r != 0;
             $a = $b, $b = $r, $r = $a % $b);

        return $b;
    }

    /**
     * 最小公倍数となる周波数を算出
     * @param int $v0 値0
     * @param int $v1 値1
     * @param int 最小公倍数
     */
    private static function getLcmFrequency($v0, $v1) {
        if ($v0 < $v1) {
//            $v0 = $v0 ^ $v1;
//            $v1 = $v0 ^ $v1;
//            $v0 = $v0 ^ $v1;
            $t  = $v0;
            $v0 = $v1;
            $v1 = $t;
        }

        if ($v1 <= 0) {
            return $v0;
        }

        // 最大公約数
        for ($a = $v0, $b = $v1, $r = $a % $b;
             $r != 0;
             $a = $b, $b = $r, $r = $a % $b);

        // 最小公倍数
        return $v0 * $v1 / $b;
    }
}
