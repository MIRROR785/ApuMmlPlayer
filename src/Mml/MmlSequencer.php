<?php
/**
 * MmlSequencer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Mml;

use MIRROR785\ApuMmlPlayer\Audio\{AudioConst, AudioUnit};
use MIRROR785\ApuMmlPlayer\Mml\{MmlConst, MmlContainer};

/**
 * MMLトラック状態を格納するクラス
 */
class MmlTrackStatus
{
    /** @var int MML読み込み位置  */
    public $index;

    /** @var int MML読み込みカウント */
    public $count;

    /** @var int テンポ */
    public $tempo;

    /** @var int 音質 */
    public $voice;

    /** @var int 音量 */
    public $volume;

    /** @var int オクターブ */
    public $octave;

    /** @var int 音長 */
    public $length;

    /** @var int キー番号 */
    public $key_no;

    /** @var int キーオクターブ */
    public $key_octave;

    /** @var int キー長 */
    public $key_length;

    /** @var int 繰り返しネスト数 */
    public $loop_nest;

    /** @var int[] 繰り返し開始位置 */
    public $loop_begin;

    /** @var int[] 繰り返し終了位置 */
    public $loop_end;

    /** @var int[] 繰り返し回数 */
    public $loop_count;

    /** var bool ループ判定結果 */
    public $is_loop;

    /** @var int 開始遅延カウント */
    public $lateCount;

    /**
     * コンストラクタ
     */
    public function __construct() {
        $this->index = 0;
        $this->count = 0;
        $this->tempo = 0;
        $this->voice = 0;
        $this->volume = 15;
        $this->octave = 3;
        $this->length = 8;
        $this->key_no = MmlConst::KNO_R;
        $this->key_octave = 0;
        $this->key_length = 0;
        $this->is_loop = false;
        $this->lateCount = 0;

        $this->loop_begin = [];
        $this->loop_end = [];
        $this->loop_count = [];

        $this->loop_nest = 0;
        for ($i = 0; $i < MmlConst::LOOP_NEST_MAX; ++$i) {
            $this->loop_begin[$i] = 0xff;
            $this->loop_end[$i] = 0;
            $this->loop_count[$i] = 0;
        }
    }
}

/**
 * MMLの演奏を管理するクラス
 */
class MmlSequencer
{
    /** var bool 終端判定結果 */
    public $isEndOfData;

    /** var MmlContainer MMLコンテナ情報 */
    private $container;

    /** var MmlTrackStatus[] トラック状態配列 */
    private $trackStatus;

    /** var int[] キー番号配列 */
    private static $key_no = [
        'a' => MmlConst::KNO_A,
        'b' => MmlConst::KNO_B,
        'c' => MmlConst::KNO_C,
        'd' => MmlConst::KNO_D,
        'e' => MmlConst::KNO_E,
        'f' => MmlConst::KNO_F,
        'g' => MmlConst::KNO_G,
    ];

    /**
     * コンストラクタ
     * @param MmlContainer $container MMLコンテナ
     */
    public function __construct($container) {
        $this->isEndOfData = false;
        $this->container = $container;
        $this->trackStatus = [];
        foreach ($container->trackNumbers as $tr) {
            $this->trackStatus[$tr] = new MmlTrackStatus();
        }
        if (array_key_exists(0, $container->trackNumbers)) {
            $this->readControlTrack($container->tracks[0]);
        }
    }

    /**
     * １フレーム単位の処理
     * @param AudioUnit[] $audioUnits オーディオユニット配列
     * @return bool 終端判定結果（全トラックループ検知）
     */
    public function tick($audioUnits) {
        if ($this->isEndOfData) {
            foreach ($this->container->trackNumbers as $tr) {
                if ($tr > 0) {
                    $this->trackStatus[$tr]->is_loop = false;
                }
            }
        }

        $result = true;
        foreach ($this->container->trackNumbers as $tr) {
            if ($tr > 0) {
                $result &= $this->readTrack($tr, $audioUnits);
            }
        }
        $this->isEndOfData = $result;
    }

    /**
     * MML制御トラックの読み込み
     * @param char[] $controlTrack MMLトラック情報
     */
    private function readControlTrack($controlTrack) {
        $i = 0;
        $k = $controlTrack[$i];

        for (;;) {
            if ($k === 't') {
                $p = 0;
                for (;;) {
                    ++$i;
                    $n = $controlTrack[$i];
                    if ($n < '0' || '9' < $n) {
                        $k = $n;
                        break;
                    }

                    $n -= '0';
                    $p *= 10;
                    $p += $n;
                }

                $l = $p / 15 * 2;
                $m = 60 - $l;

                foreach ($this->container->trackNumbers as $tr) {
                    $track = $this->container->tracks[$tr];
                    $n = $track[0];
                    if ($n !== "\n") {
                        $status = $this->trackStatus[$tr];
                        $status->tempo = $l;
                        $status->count = $m;
                    }
                }

            } elseif ($k === "\n") {
                return;

            } else {
                ++$i;
                $k = $controlTrack[$i];
            }
        }
    }

    /**
     * MMLトラック情報の読み込み
     * @param int $tr トラック番号
     * @param AudioUnit[] $audioUnits オーディオユニット情報配列
     * @return bool 繰り返し発生判定結果
     */
    private function readTrack($tr, $audioUnits) {
        $status = $this->trackStatus[$tr];
        $track = $this->container->tracks[$tr];

        $n = $status->tempo;
        if ($n === 0) {
            $status->key_no = MmlConst::KNO_R;
            $status->is_loop = true;

        } else {
            $l = $status->count;
            $l += $n;

            if ($l < 60) {
                $status->count = $l;

            } else {
                $status->count = $l - 60;

                $l = $status->key_length;

                if ($l > 0) {
                    --$l;
                    $status->key_length = $l;

                } else {
                    $m = $status->volume;
                    $o = $status->octave;
                    $l = $status->length;

                    $i = $status->index;

                    for ($k = $track[$i]; ; $k = $track[$i]) {
                        if ($k === "\n") {
                            $status->is_loop = true;
                            $p = $status->loop_begin[0];
                            if ($p < 0xff) {
                                $i = $p;
                                $p = $status->loop_count[0];
                                if ($p > 0) {
                                    --$p;
                                    $status->loop_count[0] = $p;
                                    if ($p == 0) {
                                        $status->loop_begin[0] = 0xff;
                                    }
                                }
                                continue;
                            }

                            $k = MmlConst::KNO_R;
                            break;
                        }

                        if ($k === 'r') {
                            $k = MmlConst::KNO_R;
                            $p = 0;
                            for (;;) {
                                ++$i;
                                $n = $track[$i];
                                if ($n < '0' || '9' < $n) {
                                    break;
                                }

                                $n -= '0';
                                $p *= 10;
                                $p += $n;
                            }

                            if ($p > 0) {
                                $l = 32 / $p;
                                $p = 0;
                            }

                            while ($n === '.') {
                                ++$p;
                                ++$i;
                                $n = $track[$i];
                            }
                            if ($p > 0) {
                                $n = $p >> 1;
                                if ($n > 0) {
                                    $n *= $l;
                                }
                                $p &= 1;
                                if ($p > 0) {
                                    $n += $l >> 1;
                                }
                                $l += $n;
                            }

                            // TODO : タイ、スラー
                            break;
                        }

                        if ('a' <= $k && $k <= 'g') {
                            $k = self::$key_no[$k];
                            ++$i;
                            $n = $track[$i];

                            if ($n === '+' || $n === '#') {
                                ++$k;

                            } elseif ($n === '-') {
                                --$k;

                            } elseif ($n === '=' || $n === '*') {
                                // no effect

                            } else {
                                --$i;
                            }

                            $p = 0;
                            for (;;) {
                                ++$i;
                                $n = $track[$i];
                                if ($n < '0' || '9' < $n) {
                                    break;
                                }

                                $n -= '0';
                                $p *= 10;
                                $p += $n;
                            }

                            if ($p > 0) {
                                $l = 32 / $p;
                                $p = 0;
                            }

                            while ($n === '.') {
                                ++$p;
                                ++$i;
                                $n = $track[$i];
                            }
                            if ($p > 0) {
                                $n = $p >> 1;
                                if ($n > 0) {
                                    $n *= $l;
                                }
                                $p &= 1;
                                if ($p > 0) {
                                    $n += $l >> 1;
                                }
                                $l += $n;
                            }

                            // TODO : タイ、スラー
                            break;
                        }

                        if ($k === 'L') {
                            $p = 0;
                            for (;;) {
                                ++$i;
                                $n = $track[$i];
                                if ($n < '0' || '9' < $n) {
                                    break;
                                }

                                $n -= '0';
                                $p *= 10;
                                $p += $n;
                            }
                            $status->loop_nest = 0;
                            $status->loop_begin[0] = $i;
                            $status->loop_count[0] = $p;
                            continue;

                        } elseif ($k === '[') {
                            $p = 0;
                            for (;;) {
                                ++$i;
                                $n = $track[$i];
                                if ($n < '0' || '9' < $n) {
                                    break;
                                }

                                $n -= '0';
                                $p *= 10;
                                $p += $n;
                            }
                            $k = $status->loop_nest;
                            if ($k < MmlConst::LOOP_NEST_MAX) {
                                ++$k;
                                $status->loop_nest = $k;
                                $status->loop_begin[$k] = $i;
                                if ($p == 0) $p = 1;
                                $status->loop_count[$k] = $p;
                            }
                            continue;

                        } elseif ($k === ':') {
                            $k = $status->loop_nest;
                            $p = $status->loop_count[$k];
                            ++$i;
                            if ($p == 0) {
                                $p = $status->loop_end[$k];
                                if ($p > 0) {
                                    $i = $p;
                                    $status->loop_end[$k] = 0;
                                    if ($k > 0) {
                                        --$k;
                                        $status->loop_nest = $k;
                                    }
                                }
                            }
                            continue;

                        } elseif ($k === ']') {
                            ++$i;
                            $k = $status->loop_nest;
                            $p = $status->loop_begin[$k];
                            if ($p < 0xff) {
                                $status->loop_end[$k] = $i;
                                $i = $p;

                                $p = $status->loop_count[$k];
                                if ($p > 0) {
                                    --$p;
                                    $status->loop_count[$k] = $p;
                                }
                                if ($p == 0) {
                                    $status->loop_begin[$k] = 0xff;
                                }

                            } else if ($k > 0) {
                                --$k;
                                $status->loop_nest = $k;
                            }
                            continue;

                        } elseif ($k === '<') {
                            ++$i;
                            --$o;
                            $status->octave = $o;
                            continue;

                        } elseif ($k === '>') {
                            ++$i;
                            ++$o;
                            $status->octave = $o;
                            continue;

                        } elseif ($k === '(') {
                            ++$i;
                            --$m;
                            $status->volume = $m;
                            continue;

                        } elseif ($k === ')') {
                            ++$i;
                            ++$m;
                            $status->volume = $m;
                            continue;
                        }

                        $p = 0;
                        for (;;) {
                            ++$i;
                            $n = $track[$i];

                            if ($n < '0' || '9' < $n) {
                                break;
                            }

                            $n -= '0';
                            $p *= 10;
                            $p += $n;
                        }

                        if ($k === 'o') {
                            $o = $p - 1;
                            $status->octave = $o;
                            continue;

                        } elseif ($k === 'l') {
                            if ($p > 0) {
                                $l = 32 / $p;
                                $p = 0;
                            } else {
                                $l = 1;
                            }

                            while ($n === '.') {
                                ++$p;
                                ++$i;
                                $n = $track[$i];
                            }
                            if ($p > 0) {
                                $n = $p >> 1;
                                if ($n > 0) {
                                    $n *= $l;
                                }
                                $p &= 1;
                                if ($p > 0) {
                                    $n += $l >> 1;
                                }
                                $l += $n;
                            }

                            $status->length = $l;
                            continue;

                        } else if ($k === 't') {
                            $p = $p / 15 * 2;
                            $status->tempo = $p;
                            $status->count = 60 - $p;
                            continue;

                        } elseif ($k === 'v') {
                            $m = $p;
                            $status->volume = $m;
                            continue;
                        }
                    }

                    $status->key_no = $k;
                    $status->key_octave = $o;
                    $status->key_length = $l - 1;
                    $status->index = $i;

                    foreach ($audioUnits as $audioUnit) {
                        $apu = $audioUnit->apu;
                        if (array_key_exists($tr, $apu->devices)) {
                            $device = $apu->devices[$tr];
                            if ($status->key_no !== MmlConst::KNO_R) {
                                // TODO : 音色設定
                                //$device->setVoice($status->voice);
                                $device->setVolume($status->volume);
                                $device->noteOn(MmlConst::KNO_COUNT * $status->key_octave + $status->key_no);
                            } else {
                                $device->noteOff();
                            }
                        }
                    }
                }
            }
        }

        return $status->is_loop;
    }
}
