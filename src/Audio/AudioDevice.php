<?php
/**
 * AudioDevice.php
 *
 * @author @MIRROR_
 * @license MIT
 */
namespace MIRROR785\ApuMmlPlayer\Audio;

/**
 * デバイス制御インターフェース
 */
interface DeviceController
{
    /**
     * 実行可能状態確認
     * @return bool 実行判定
     */
    function isAction();

    /**
     * 実行カウントを取得
     * @return int 実行カウント
     */
    function getCount();

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    function action();
}

/**
 * 遅延実行用パラメータ制御クラス
 */
class ParamControl
{
    /**  @var AudioDevice オーディオデバイス */
    protected $device;

    /**  @var int 設定値 */
    protected $value;

    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value 設定値
     */
    public function __construct($device, $value) {
        $this->device = $device;
        $this->value = $value;
    }

    /**
     * 実行可能状態確認
     * @return bool 実行判定
     */
    public function isAction() {
        return true;
    }

    /**
     * 実行カウントを取得
     * @return int 実行カウント
     */
    public function getCount() {
        return 0;
    }
}

/**
 * 遅延実行用音色制御クラス
 */
class VoiceControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value 音色
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setVoiceValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用音量制御クラス
 */
class VolumeControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value 音量
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setVolumeValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用音量オフセット制御クラス
 */
class OffsetVolumeControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value 音量オフセット
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setOffsetVolumeValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用ノート番号制御クラス
 */
class NoteNoControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value ノート番号
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setNoteNoValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用ノートオフセット制御クラス
 */
class OffsetNoteControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value ノートオフセット
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setOffsetNoteValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用周波数オフセット制御クラス
 */
class OffsetFrequencyControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value 周波数オフセット
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setOffsetFrequencyValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用発音遅延時間制御クラス
 */
class DelayControl extends ParamControl implements DeviceController
{
    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $value 発音遅延時間
     */
    public function __construct($device, $value) {
        parent::__construct($device, $value);
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        $this->device->setDelayValue($this->value);
        return true;
    }
}

/**
 * 遅延実行用ノート制御クラス
 */
class NoteControl implements DeviceController
{
    /**  @var AudioDevice オーディオデバイス */
    private $device;

    /**  @var int 実行カウント */
    private $count;

    /**  @var bool ノート有効判定 */
    private $enabled;

    /**  @var int ノート値 */
    private $noteNo;

    /**
     * コンストラクタ
     * @param AudioDevice $device オーディオデバイス
     * @param int $count 実行カウント
     * @param bool $enabled ノート有効判定
     * @param int $noteNo ノート値
     */
    public function __construct($device, $count, $enabled, $noteNo) {
        $this->device = $device;
        $this->count = $count;
        $this->enabled = $enabled;
        $this->noteNo = $noteNo;
    }

    /**
     * 実行可能状態確認
     * @return bool 実行判定
     */
    public function isAction() {
        return (--$this->count < 0);
    }

    /**
     * 実行カウントを取得
     * @return int 実行カウント
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * 制御処理の実行
     * @return bool 継続実行判定
     */
    public function action() {
        if ($this->enabled) {
            $this->device->setNoteOn($this->noteNo);
        } else {
            $this->device->setNoteOff();
        }
        return false;
    }
}

/**
 * オーディオデバイスの抽象クラス
 */
abstract class AudioDevice
{
    /**  @var int 基本増幅量 */
    const BASE_AMP = 0x800;

    /** @var int サンプリングレート */
    protected $sampleRate;

    /** @var int 音量 */
    protected $volume;				//   0 ~ 15

    /** @var int 音量オフセット */
    protected $offsetVolume;		//   0 ~ 15

    /** @var int ノート番号 */
    protected $noteNo;				//   0 ~

    /** @var int ノートオフセット */
    protected $offsetNote;			// -64 ~ 63

    /** @var int 音程 */
    protected $tone;

    /** @var double 周期カウント */
    protected $cycleCount;

    /** @var double 周期増分 */
    protected $cyclecycleDelta;

    /** @var double 開始遅延時間 */
    protected $late;

    /** @var int 開始遅延カウント */
    protected $lateCount;

    /** @var double 発音遅延時間 */
    protected $delay;

    /** @var int 発音遅延カウント */
    protected $delayCount;

    /** @var double 増幅量 */
    protected $amp;

    /** @var bool 停止判定 */
    protected $stopped;

    /** @var DeviceController[] デバイス制御情報 */
    protected $controls;

    /**
     * コンストラクタ
     * @param int $sampleRate サンプリングレート
     */
    public function __construct($sampleRate) {
        $this->sampleRate = $sampleRate;
        $this->late = 0;
        $this->reset();
    }

    /**
     * 再初期化
     */
    public function reset() {
        $this->lateCount = (int)($this->late * $this->sampleRate);
        $this->volume = 0;
        $this->offsetVolume = 0;
        $this->nodeNo = 0;
        $this->offsetNote = 0;
        $this->tone = 440;
        $this->cycleCount = 0;
        $this->cyclecycleDelta = 0;
        $this->delay = 0;
        $this->delayCount = 0;
        $this->amp = 0;
        $this->stopped = true;
        $this->controls = [];
    }

    /**
     * サンプリングレートを設定
     * @param int $value
     */
    public function setSampleRate($value) {
        $this->sampleRate = $value;
    }

    /**
     * 開始遅延時間を設定
     * @param double $value
     */
    public function setLate($value) {
        $this->late = $value;
        $this->lateCount = (int)($this->sampleRate * $this->late);
        //echo '$this->lateCount ='.$this->lateCount."\n";
    }

    /**
     * 音色を設定
     * @param int $value 音色
     */
    public function setVoice($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new VoiceControl($this, $value));
        } else {
            $this->setVoiceValue($value);
        }
    }

    /**
     * 音量を設定
     * @param int $value 音量
     */
    public function setVolume($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new VolumeControl($this, $value));
        } else {
            $this->setVolumeValue($value);
        }
    }

    /**
     * 音量オフセットを設定
     * @param int $value 音量オフセット
     */
    public function setOffsetVolume($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new OffsetVolumeControl($this, $value));
        } else {
            $this->setOffsetVolumeValue($value);
        }
    }

    /**
     * ノート番号を設定
     * @param int $value ノート番号
     */
    public function setNoteNo($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new NoteNoControl($this, $value));
        } else {
            $this->setNoteNoValue($value);
        }
    }

    /**
     * ノートオフセットを設定
     * @param int $value ノートオフセット
     */
    public function setOffsetNote($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new OffsetNoteControl($this, $value));
        } else {
            $this->setOffsetNoteValue($value);
        }
    }

    /**
     * 周波数オフセットを設定
     * @param int $value 周波数オフセット
     */
    public function setOffsetFrequency($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new OffsetFrequencyControl($this, $value));
        } else {
            $this->setOffsetFrequencyValue($value);
        }
    }

    /**
     * 発音遅延時間を設定
     * @param double $value 発音遅延時間
     */
    public function setDelay($value) {
        if ($this->lateCount > 0) {
            $this->addControl(new DelayControl($this, $value));
        } else {
            $this->setDelayValue($value);
        }
    }

    /**
     * ノートオン
     * @param int $value ノート値
     */
    public function noteOn($noteNo = null) {
        if ($this->lateCount > 0) {
            $this->addControl(new NoteControl($this, $this->getLateCount(), true, $noteNo));
        } else {
            $this->setNoteOn($noteNo);
        }
    }

    /**
     * ノートオフ
     */
    public function noteOff() {
        if ($this->lateCount > 0) {
            $this->addControl(new NoteControl($this, $this->getLateCount(), false, null));
        } else {
            $this->setNoteOff();
        }
    }

    /**
     * 遅延カウントの取得
     * @return int 遅延カウント
     */
    public function getLateCount() {
        $count = 0;
        foreach ($this->controls as $it) {
            $count += $it->getCount();
        }
        return $this->lateCount - $count;
    }

    /**
     * 遅延実行情報の追加
     * @param DeviceController $control 制御情報
     */
    public function addControl($control) {
        $this->controls[] = $control;
    }

    /**
     * 音色を設定
     * @param int $value 音色
     */
    public function setVoiceValue($value) {
    }

    /**
     * 音量を設定
     * @param int $value 音量
     */
    public function setVolumeValue($value) {
        $this->volume = AudioConst::getValue($value, 0, 15);
    }

    /**
     * 音量オフセットを設定
     * @param int $value 音量オフセット
     */
    public function setOffsetVolumeValue($value) {
        $this->offsetVolume = AudioConst::getValue($value, 0, 15);
    }

    /**
     * ノート番号を設定
     * @param int $value ノート番号
     */
    public function setNoteNoValue($value) {
        $this->noteNo = $value;
    }

    /**
     * ノートオフセットを設定
     * @param int $value ノートオフセット
     */
    public function setOffsetNoteValue($value) {
        $this->offsetNote = AudioConst::getValue($value, -64, 63);
    }

    /**
     * 周波数オフセットを設定
     * @param int $value 周波数オフセット
     */
    public function setOffsetFrequencyValue($value) {
    }

    /**
     * 発音遅延時間を設定
     * @param double $value 発音遅延時間
     */
    public function setDelayValue($value) {
        $this->delay = $value;
    }

    /**
     * ノートオン設定
     * @param int $noteNo ノート値
     */
    public function setNoteOn($noteNo) {
        $this->stopped = false;
        if ($noteNo !== null) {
            $this->noteNo = $noteNo;
        }
        $this->delayCount = (int)($this->sampleRate * $this->delay);
    }

    /**
     * ノートオフ設定
     */
    public function setNoteOff() {
        $this->stopped = true;
        $this->cycleCount = 0;
    }

    /**
     * 登録済みデバイス制御情報数を取得
     * return int デバイス制御情報数
     */
    public function getControlCount() {
        return count($this->controls);
    }

    /**
     * サンプリング
     * @return int サンプリング情報
     */
    public function sampling() {
        $v = 0;

        while (count($this->controls) > 0) {
            $control = $this->controls[0];
            if ($control->isAction()) {
                array_shift($this->controls);

                if (!$control->action()) {
                    break;
                }

            } else {
                break;
            }
        }

        if (!$this->stopped) {
            if ($this->delayCount > 0) {
                --$this->delayCount;

            } else {
                $v = $this->getSample();
            }
        }

        return $v;
    }

    /**
     * サンプリング値を取得
     * @return int サンプリング値
     */
    abstract public function getSample();

    /**
     * 現在の周波数を取得
     * @return int 周波数
     */
    abstract public function getCurrentFrequency();
}
