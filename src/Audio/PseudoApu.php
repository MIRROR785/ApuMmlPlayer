<?php
/**
 * PseudoApu.php
 *
 * @author @MIRROR_
 * @license MIT
 */
namespace MIRROR785\ApuMmlPlayer\Audio;

use MIRROR785\ApuMmlPlayer\Device\{PulseDevice, TriangleDevice, NoiseDevice};

/**
 * 擬似APUクラス
 */
class PseudoApu
{
    /** @var PulseDevice 矩形波デバイス1 */
    public $pulse1;

    /** @var PulseDevice 矩形波デバイス2 */
    public $pulse2;

    /** @var TriangleDevice 三角波デバイス */
    public $triangle;

    /** @var TriangleDevice ノイズデバイス */
    public $noise;

    /** @var AudioDeviceInterface[] デバイス配列 */
    public $devices;

    /** @var int[] トラック番号配列 */
    public $trackNumbers;

    /** @var double[] 遅延時間配列 */
    public $delays;

    /**
     * コンストラクタ
     * @param int $sampleRate サンプリングレート
     * @param int[] $trackNumbers トラック番号配列
     * @param double[] $lates 開始遅延時間配列
     * @param double[] $delays 発音遅延時間配列
     */
    public function __construct(
        $sampleRate,
        $trackNumbers = [1, 2, 3, 4],
        $lates = [1=>0.0, 2=>0.0, 3=>0.0, 4=>0.0],
        $delays = [1=>0.0, 2=>0.0, 3=>0.0, 4=>0.0]) {

        // デバイス
        $this->pulse1 = new PulseDevice($sampleRate);
        $this->pulse2 = new PulseDevice($sampleRate);
        $this->triangle = new TriangleDevice($sampleRate);
        $this->noise = new NoiseDevice($sampleRate);

        // デバイスリスト
        $this->devices = [
            1 => $this->pulse1,
            2 => $this->pulse2,
            3 => $this->triangle,
            4 => $this->noise,
        ];

        // トラック順
        $this->trackNumbers = $trackNumbers;

        // 遅延時間
        $this->lates = $lates;
        $this->delays = $delays;

        // 再初期化
        $this->reset();
    }

    /**
     * 再初期化
     */
    public function reset() {
        foreach ($this->trackNumbers as $tr) {
            $dev = $this->devices[$tr];
            $dev->noteOff();
            if (array_key_exists($tr, $this->lates)) {
                $dev->setLate($this->lates[$tr]);
            }
            if (array_key_exists($tr, $this->delays)) {
                $dev->setDelay($this->delays[$tr]);
            }
        }
    }

    /**
     * サンプリング
     * @return double[] サンプリングデータ
     */
    public function sampling() {
        $values = [];
        foreach ($this->trackNumbers as $tr) {
            $values[] = $this->devices[$tr]->sampling();
        }
        return $values;
    }
}
