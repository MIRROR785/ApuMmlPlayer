<?php
/**
 * PulseDevice.php
 *
 * @author @MIRROR_
 * @license MIT
 */
namespace MIRROR785\ApuMmlPlayer\Device;

use MIRROR785\ApuMmlPlayer\Audio\{AudioConst, AudioDevice};

/**
 * 矩形波出力デバイスクラス
 */
class PulseDevice extends AudioDevice
{
    /** @var int デューティサイクル */
    private $dutyCycle;			// 0:12.5%, 1:25%, 2:50%, 3:75%

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
        $this->dutyCycle = 0;
        $this->offsetFrequency = 0;
    }

    /**
     * 音色を設定
     * @param int $value 音色
     */
    public function setVoiceValue($value) {
        $this->dutyCycle = $value & 3;
    }

    /**
     * 周波数オフセットを設定
     * @param int $value 周波数オフセット
     */
    public function setOffsetFrequencyValue($value) {
        $this->offsetFrequency = AudioConst::getValue($value, -64, 63);
    }

    /**
     * サンプリング値を取得
     * @return int サンプリング値
     */
    public function getSample() {
        $v = 0;

        $noteNo = $this->noteNo + $this->offsetNote;
        $this->tone = AudioConst::getFrequency($noteNo) + $this->offsetFrequency;
        $this->cycleDelta = $this->tone * 2 * M_PI / $this->sampleRate;
        $this->amp = AudioDevice::BASE_AMP * AudioConst::getValue($this->volume + $this->offsetVolume, 0, 15) / 31;

        $s = sin($this->cycleCount);
        $c = cos($this->cycleCount);

        switch ($this->dutyCycle) {
        case 0:
            // 12.5%
            $v = ($s >= 0 && $c >= 0 && $s <= $c) ? $this->amp : -$this->amp;
            break;

        case 1:
            // 25%
            $v = ($s >= 0 && $c >= 0) ? $this->amp : -$this->amp;
            break;

        case 2:
            // 50%
            $v = ($s >= 0) ? $this->amp : -$this->amp;
            break;

        case 3:
            // 75%
            $v = ($s < 0 || $c < 0) ? $this->amp : -$this->amp;
            break;
        }
        //echo $v."\n";

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
