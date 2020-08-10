<?php
/**
 * NoiseDevice.php
 *
 * @author @MIRROR_
 * @license MIT
 */
namespace MIRROR785\ApuMmlPlayer\Device;

use MIRROR785\ApuMmlPlayer\Audio\{AudioConst, AudioDevice};

/**
 * ノイズ出力デバイスクラス
 */
class NoiseDevice extends AudioDevice
{
    /** @var bool 短周期ノイズ判定 */
    private $shortFreq;

    /** @var short ノイズレジスタ */
    private $reg;

    /** @var bool 励起判定 */
    private $edge;

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
        $this->amp = AudioDevice::BaseAmp;
        $this->shortFreq = false;
        $this->reg = 0x8000;
        $this->edge = false;
    }

    /**
     * 音色を設定
     * @param int $value 音色
     */
    public function setVoiceValue($value) {
        $this->shortFreq = ($value != 0);
    }

    /**
     * 音色を取得
     * @return int 音色
     */
    public function getVoiceValue() {
        return $this->shortFreq ? 1 : 0;
    }

    /**
     * サンプリング
     * @return int サンプリング情報
     */
    public function getSample() {
        $noteNo = $this->noteNo + $this->offsetNote;
        $this->tone = AudioConst::getNoiseFrequency($noteNo);
        $this->cycleDelta = $this->tone * 2 * M_PI / $this->sampleRate;
        $this->amp = AudioDevice::BaseAmp * AudioConst::getValue($this->volume + $this->offsetVolume, 0, 15) / 15;

        $s = sin($this->cycleCount);

        if ($this->edge) {
            if ($s < 0) {
                $this->edge = false;
            }
        } else if ($s >= 0) {
            // ニコニコ大百科(仮) FC音源<https://dic.nicovideo.jp/a/fc%E9%9F%B3%E6%BA%90>
            $this->reg >>= 1;
            $this->reg |= (($this->reg ^ ($this->reg >> ($this->shortFreq ? 6 : 1))) & 1) << 15;
            $this->edge = true;
        }
        $v = ($this->reg & 1) ? $this->amp: -$this->amp;

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
            $freq = AudioConst::getNoiseFrequency($noteNo);
            return $freq;
        }
    }
}
