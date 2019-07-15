<?php
/**
 * PseudoApu.php
 *
 * @author @MIRROR_
 * @license MIT
 */
require_once('AudioUtil.php');
require_once('AudioDevice.php');

/**
 * 矩形波出力デバイスクラス
 */
class PulseDevice implements AudioDevice {
	/** @var int サンプリングレート */
	private $sampleRate;

	/** @var int デューティサイクル */
	private $dutyCycle = 0;			// 0:12.5%, 1:25%, 2:50%, 3:75%

	/** @var int 音量 */
	private $volume = 0;			//   0 ~ 15

	/** @var int ノート番号 */
	private $noteNo = 0;			//   0 ~

	/** @var int 音量オフセット */
	private $offsetVolume = 0;		//   0 ~ 15

	/** @var int ノートオフセット */
	private $offsetNote = 0;		// -64 ~ 63

	/** @var int 周波数オフセット */
	private $offsetFrequency = 0;	// -64 ~ 63

	/** @var double 増幅量 */
	private $amp = 0;

	/** @var int 音程 */
	private $tone = 440;

	/** @var double 角度 */
	private $theta = 0;

	/** @var double 角度増分 */
	private $theta_delta = 0;

	/** @var 停止判定 */
	private $stopped = true;

	/**
	 * コンストラクタ
	 * @param int $sampleRate サンプリングレート
	 */
	public function __construct($sampleRate) {
		$this->noteOff();
		$this->setSampleRate($sampleRate);
	}

	/**
	 * サンプリングレートを設定
	 * @param int $value
	 */
	public function setSampleRate($value) {
		$this->sampleRate = $value;
	}

	/**
	 * 音色を設定
	 * @param int $value
	 */
	public function setVoice($value) {
		$this->dutyCycle = $value & 3;
	}

	/**
	 * 音量を設定
	 * @param int $value
	 */
	public function setVolume($value) {
		$this->volume = AudioUtil::getValue($value, 0, 15);
	}

	/**
	 * ノート番号を設定
	 * @param int $value
	 */
	public function setNoteNo($value) {
		$this->noteNo = $value;
	}

	/**
	 * 音量オフセットを設定
	 * @param int $value
	 */
	public function setOffsetVolume($value) {
		$this->offsetVolume = AudioUtil::getValue($value, 0, 15);
	}

	/**
	 * ノートオフセットを設定
	 * @param int $value
	 */
	public function setOffsetNote($value) {
		$this->offsetNote = AudioUtil::getValue($value, -64, 63);
	}

	/**
	 * 周波数オフセットを設定
	 * @param int $value
	 */
	public function setOffsetFrequency($value) {
		$this->offsetFrequency = AudioUtil::getValue($value, -64, 63);
	}

	/**
	 * ノートオン設定
	 * @param int $value ノート値
	 */
	public function noteOn($noteNo = null) {
		$this->stopped = false;
		if ($noteNo !== null) {
			$this->noteNo = $noteNo;
		}
	}

	/**
	 * ノートオフ設定
	 */
	public function noteOff() {
		$this->stopped = true;
		$this->theta = 0;
	}

	/**
	 * サンプリング
	 * @return double サンプリング情報
	 */
	public function sampling() {
		$v = 0;

		if (!$this->stopped) {
			$this->amp = AudioDevice::baseAmp * AudioUtil::getValue($this->volume + $this->offsetVolume, 0, 15) / 15;
			$s = sin($this->theta);
			$c = cos($this->theta);

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

			$noteNo = $this->noteNo + $this->offsetNote;
			$this->tone = AudioUtil::getFrequency($noteNo) + $this->offsetFrequency;
			$this->theta_delta = $this->tone * 2 * M_PI / $this->sampleRate;
			$this->theta += $this->theta_delta;
		}

		return $v;
	}
}

/**
 * 三角波出力デバイスクラス
 */
class TriangleDevice implements AudioDevice {
	/** @var int サンプリングレート */
	private $sampleRate;

	/** @var int ノート番号 */
	private $noteNo = 0;			//   0 ~

	/** @var int ノートオフセット */
	private $offsetNote = 0;		// -64 ~ 63

	/** @var int 周波数オフセット */
	private $offsetFrequency = 0;	// -64 ~ 63

	/** @var double 増幅量 */
	private $amp = 0;

	/** @var int 音程 */
	private $tone = 440;

	/** @var double 角度 */
	private $theta = 0;

	/** @var double 角度増分 */
	private $theta_delta = 0;

	/** @var 停止判定 */
	private $stopped = true;

	/**
	 * コンストラクタ
	 * @param int $sampleRate サンプリングレート
	 */
	public function __construct($sampleRate) {
		$this->amp = AudioDevice::baseAmp;
		$this->noteOff();
		$this->setSampleRate($sampleRate);
	}

	/**
	 * サンプリングレートを設定
	 * @param int $value
	 */
	public function setSampleRate($value) {
		$this->sampleRate = $value;
	}

	/**
	 * 音色を設定
	 * @param int $value
	 */
	public function setVoice($value) {
		// 処理なし
	}

	/**
	 * 音量を設定
	 * @param int $value
	 */
	public function setVolume($value) {
		// 処理なし
	}

	/**
	 * ノート番号を設定
	 * @param int $value
	 */
	public function setNoteNo($value) {
		$this->noteNo = $value;
	}

	/**
	 * 音量オフセットを設定
	 * @param int $value
	 */
	public function setOffsetVolume($value) {
		// 処理なし
	}

	/**
	 * ノートオフセットを設定
	 * @param int $value
	 */
	public function setOffsetNote($value) {
		$this->offsetNote = AudioUtil::getValue($value, -64, 63);
	}

	/**
	 * 周波数オフセットを設定
	 * @param int $value
	 */
	public function setOffsetFrequency($value) {
		$this->offsetFrequency = AudioUtil::getValue($value, -64, 63);
	}

	/**
	 * ノートオン設定
	 * @param int $noteNo ノート番号
	 */
	public function noteOn($noteNo = null) {
		$this->stopped = false;
		if ($noteNo !== null) {
			$this->noteNo = $noteNo;
		}
	}

	/**
	 * ノートオフ設定
	 */
	public function noteOff() {
		$this->stopped = true;
	}

	/**
	 * サンプリング
	 * @return double サンプリング情報
	 */
	public function sampling() {
		$d = (2 * acos(cos($this->theta)) - M_PI) / M_PI;
		$v = floor($this->amp * $d);
		$v -= $v % ((AudioDevice::baseAmp - 0x100) / 8);

		if (!$this->stopped) {
			$noteNo = $this->noteNo + $this->offsetNote;
			$this->tone = AudioUtil::getFrequency($noteNo) + $this->offsetFrequency;
			$this->theta_delta = $this->tone * 2 * M_PI / $this->sampleRate;
			$this->theta += $this->theta_delta;
		}

		return $v;
	}
}

/**
 * ノイズ出力デバイスクラス
 */
class NoiseDevice implements AudioDevice {
	/** @var int サンプルレート */
	private $sampleRate;

	/** @var bool 短周期ノイズ判定 */
	private $shortFreq = false;

	/** @var int 音量 */
	private $volume = 0;			//   0 ~ 15

	/** @var int ノート番号 */
	private $noteNo = 0;			//   0 ~

	/** @var int 音量オフセット */
	private $offsetVolume = 0;		//   0 ~ 15

	/** @var int ノートオフセット */
	private $offsetNote = 0;		// -64 ~ 63

	/** @var double 増幅量 */
	private $amp = 0;

	/** @var int 音程 */
	private $tone = 440;

	/** @var double 角度 */
	private $theta = 0;

	/** @var double 角度増分 */
	private $theta_delta = 0;

	/** @var short ノイズレジスタ */
	private $reg = 0x8000;

	/** @var bool 励起判定 */
	private $edge = false;

	/** @var bool 停止判定 */
	private $stopped = true;

	/**
	 * コンストラクタ
	 * @param int $sampleRate サンプリングレート
	 */
	public function __construct($sampleRate) {
		$this->noteOff();
		$this->setSampleRate($sampleRate);
	}

	/**
	 * サンプリングレートを設定
	 * @param int $value
	 */
	public function setSampleRate($value) {
		$this->sampleRate = $value;
	}

	/**
	 * 音色を設定
	 * @param int $value
	 */
	public function setVoice($value) {
		$this->shortFreq = ($value != 0);
	}

	/**
	 * 音量を設定
	 * @param int $value
	 */
	public function setVolume($value) {
		$this->volume = AudioUtil::getValue($value, 0, 15);
	}

	/**
	 * ノート番号を設定
	 * @param int $value
	 */
	public function setNoteNo($value) {
		$this->noteNo = $value;
	}

	/**
	 * 音量オフセットを設定
	 * @param int $value
	 */
	public function setOffsetVolume($value) {
		$this->offsetVolume = AudioUtil::getValue($value, 0, 15);
	}

	/**
	 * ノートオフセットを設定
	 * @param int $value
	 */
	public function setOffsetNote($value) {
		$this->offsetNote = AudioUtil::getValue($value, -64, 63);
	}

	/**
	 * 周波数オフセットを設定
	 * @param int $value
	 */
	public function setOffsetFrequency($value) {
	}

	/**
	 * ノートオン設定
	 * @param int $value ノート値
	 */
	public function noteOn($noteNo = null) {
		$this->stopped = false;
		if ($noteNo !== null) {
			$this->noteNo = $noteNo;
		}
	}

	/**
	 * ノートオフ設定
	 */
	public function noteOff() {
		$this->stopped = true;
		$this->theta = 0;
	}

	/**
	 * サンプリング
	 * @return double サンプリング情報
	 */
	public function sampling() {
		$v = 0;

		if (!$this->stopped) {
			$this->amp = AudioDevice::baseAmp * AudioUtil::getValue($this->volume + $this->offsetVolume, 0, 15) / 15;
			$s = sin($this->theta);

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

			$noteNo = $this->noteNo + $this->offsetNote;
			$this->tone = AudioUtil::getNoiseFrequency($noteNo);
			$this->theta_delta = $this->tone * 2 * M_PI / $this->sampleRate;
			$this->theta += $this->theta_delta;
		}

		return $v;
	}
}

/**
 * 擬似APUクラス
 */
class PseudoApu {
	/** @var PulseDevice 矩形波デバイス1 */
	public $pulse1;

	/** @var PulseDevice 矩形波デバイス2 */
	public $pulse2;

	/** @var TriangleDevice 三角波デバイス */
	public $triangle;

	/** @var TriangleDevice ノイズデバイス */
	public $noise;

	/** @var AudioDevice[] デバイス配列 */
	public $devices;

	/** @var int[] トラック番号配列 */
	public $trackNumbers;

	/**
	 * コンストラクタ
	 * @param int $sampleRate サンプリングレート
	 * @param int[] $trackNumbers トラック番号配列
	 */
	public function __construct($sampleRate, $trackNumbers = [1, 2, 3, 4]) {
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
	}

	/**
	 * リセット
	 */
	public function reset() {
		foreach ($this->devices as $dev) {
			$dev->noteOff();
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
