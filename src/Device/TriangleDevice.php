<?php
/**
 * TriangleDevice.php
 *
 * @author @MIRROR_
 * @license MIT
 */
namespace MIRROR785\ApuMmlPlayer\Device;

use MIRROR785\ApuMmlPlayer\Audio\{AudioConst, AudioDevice};

/**
 * 三角波出力デバイスクラス
 */
class TriangleDevice extends AudioDevice
{
    /** @var int 周波数オフセット */
    private $offsetFrequency;	// -64 ~ 63

    /**
     * コンストラクタ
     * @param int $sampleRate サンプリングレート
     */
    public function __construct($sampleRate) {
        parent::__construct($sampleRate);
    }

    /**
     * 再初期化
     */
    public function reset() {
        parent::reset();
        $this->amp = AudioDevice::BASE_AMP;
        $this->offsetFrequency = 0;
    }

    /**
     * 周波数オフセットを設定
     * @param int $value 周波数オフセット
     */
    public function setOffsetFrequencyValue($value) {
        $this->offsetFrequency = AudioConst::getValue($value, -64, 63);
    }

    /**
     * 周波数オフセットを取得
     * @return int 周波数オフセット
     */
    public function getOffsetFrequencyValue() {
        return $this->offsetFrequency;
    }

    /**
     * サンプリング値を取得
     * @return int サンプリング値
     */
    public function getSample() {
        $v = 0;

        $noteNo = $this->noteNo + $this->offsetNote;
        $this->tone = AudioConst::getFrequency($noteNo) + $this->offsetFrequency;
        $this->cycleDelta = $this->tone * M_PI / $this->sampleRate;

        $theta = (int)($this->cycleCount * 8 / M_PI) * M_PI / 8;
        $d = (2 * acos(cos($theta)) - M_PI) / M_PI;
        $v = $this->amp * $d;

        $this->cycleCount += $this->cycleDelta;

        if ($this->cycleCount >= 2 * M_PI) {
            $this->cycleCount -= 2 * M_PI;
        }

        return $v;
    }

    /**
     * 現在の周波数を取得
     * @return int 周波数
     */
    public function getCurrentFrequency() {
        if ($this->stopped) {
            return 0;

        } else {
            $noteNo = $this->noteNo + $this->offsetNote;
            $freq = AudioConst::getFrequency($noteNo) + $this->offsetFrequency;
            return $freq;
        }
    }
}
