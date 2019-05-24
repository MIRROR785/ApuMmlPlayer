<?php
interface AudioDevice {
	const baseAmp = 0x2000;
	function setSampleRate($value);
	function setVoice($value);
	function setVolume($value);
	function setNote($value);
	function setOffsetVolume($value);
	function setOffsetNote($value);
	function setOffsetFrequency($value);
	function noteOn($value);
	function noteOff();
	function sampling();
}

interface AudioMixer {
	function mixing($positions, $values);
}

interface AudioPacker {
	function packing($scale, $values);
}

class AudioUtil {
	private static $frequencies = [
		/* o1  c     32.703 */     0, // 設定不可
		/*     c+    34.648 */     0, // 設定不可
		/*     d     36.708 */     0, // 設定不可
		/*     d+    38.891 */     0, // 設定不可
		/*     e     41.203 */     0, // 設定不可
		/*     f     43.654 */     0, // 設定不可
		/*     f+    46.249 */     0, // 設定不可
		/*     g     48.999 */     0, // 設定不可
		/*     g+    51.913 */     0, // 設定不可
		/*     a     55.000 */    55,
		/*     a+    58.270 */    58,
		/*     b     61.735 */    61,
		/* o2  c     65.406 */    65,
		/*     c+    69.296 */    69,
		/*     d     73.416 */    73,
		/*     d+    77.782 */    77,
		/*     e     82.407 */    82,
		/*     f     87.307 */    87,
		/*     f+    92.499 */    92,
		/*     g     97.999 */    97,
		/*     g+   103.826 */   103,
		/*     a    110.000 */   110,
		/*     a+   116.541 */   116,
		/*     b    123.471 */   123,
		/* o3  c    130.813 */   130,
		/*     c+   138.591 */   138,
		/*     d    146.832 */   146,
		/*     d+   155.563 */   155,
		/*     e    164.814 */   164,
		/*     f    174.614 */   174,
		/*     f+   184.997 */   184,
		/*     g    195.998 */   195,
		/*     g+   207.652 */   207,
		/*     a    220.000 */   220,
		/*     a+   233.082 */   233,
		/*     b    246.942 */   246,
		/* o4  c    261.626 */   261,
		/*     c+   277.183 */   277,
		/*     d    293.665 */   293,
		/*     d+   311.127 */   311,
		/*     e    329.628 */   329,
		/*     f    349.228 */   349,
		/*     f+   369.994 */   369,
		/*     g    391.995 */   391,
		/*     g+   415.305 */   415,
		/*     a    440.000 */   440, // マスターチューニング
		/*     a+   466.164 */   466,
		/*     b    493.883 */   493,
		/* o5  c    523.251 */   523,
		/*     c+   554.365 */   554,
		/*     d    587.330 */   587,
		/*     d+   622.254 */   622,
		/*     e    659.255 */   659,
		/*     f    698.456 */   698,
		/*     f+   739.989 */   739,
		/*     g    783.991 */   783,
		/*     g+   830.609 */   830,
		/*     a    880.000 */   880,
		/*     a+   932.328 */   932,
		/*     b    987.767 */   987,
		/* o6  c   1046.502 */  1046,
		/*     c+  1108.731 */  1108,
		/*     d   1174.659 */  1174,
		/*     d+  1244.508 */  1244,
		/*     e   1318.510 */  1318,
		/*     f   1396.913 */  1396,
		/*     f+  1479.978 */  1479,
		/*     g   1567.982 */  1567,
		/*     g+  1661.219 */  1661,
		/*     a   1760.000 */  1760,
		/*     a+  1864.655 */  1864,
		/*     b   1975.533 */  1975,
		/* o7  c   2093.005 */  2093,
		/*     c+  2217.461 */  2217,
		/*     d   2349.318 */  2349,
		/*     d+  2489.016 */  2489,
		/*     e   2637.020 */  2637,
		/*     f   2793.826 */  2793,
		/*     f+  2959.955 */  2959,
		/*     g   3135.963 */  3135,
		/*     g+  3322.438 */  3322,
		/*     a   3520.000 */  3520,
		/*     a+  3729.310 */  3729,
		/*     b   3951.066 */  3951,
		/* o8  c   4186.009 */  4186,
		/*     c+  4434.922 */  4434,
		/*     d   4698.636 */  4698,
		/*     d+  4978.032 */  4978,
		/*     e   5274.041 */  5274,
		/*     f   5587.652 */  5587,
		/*     f+  5919.911 */  5919,
		/*     g   6271.927 */  6271,
		/*     g+  6644.875 */  6644,
		/*     a   7040.000 */  7040,
		/*     a+  7458.620 */  7458,
		/*     b   7902.133 */  7902,
		/* o9  c   8372.018 */  8372,
		/*     c+  8869.844 */  8869,
		/*     d   9397.272 */  9397,
		/*     d+  9956.064 */  9956,
		/*     e  10548.082 */ 10548,
		/*     f  11175.304 */ 11175,
		/*     f+ 11839.822 */ 11839,
		/*     g  12543.854 */ 12543,
		/*     g+ 13289.750 */ 13289,
		/*     a  14080.000 */ 14080,
		/*     a+ 14917.240 */ 14917,
		/*     b  15804.266 */ 15804,
		];

	private static $noiseFrequencies = [
		/* F: */ 0x7F2,
		/* E: */ 0x3F9,
		/* D: */ 0x1FC,
		/* C: */ 0x17D,
		/* B: */ 0x0FE,
		/* A: */ 0x0BE,
		/* 9: */ 0x07F,
		/* 8: */ 0x065,
		/* 7: */ 0x050,
		/* 6: */ 0x040,
		/* 5: */ 0x030,
		/* 4: */ 0x020,
		/* 3: */ 0x010,
		/* 2: */ 0x008,
		/* 1: */ 0x004,
		/* 0: */ 0x002,
		];

	private static $maxNoteNo;

	public static function initialize() {
		AudioUtil::$maxNoteNo = count(AudioUtil::$frequencies) - 1;
	}

	public static function getNoteNo($octave, $keyNo) {
		return ($octave - 1) * 12 + $keyNo;
	}

	public static function getFrequency($noteNo) {
		return AudioUtil::$frequencies[AudioUtil::getValue($noteNo, 0, AudioUtil::$maxNoteNo)];
	}

	public static function getNoiseNo($octave, $keyNo) {
		return ($octave - 1) * 12 + $keyNo;
	}

	public static function getNoiseFrequency($noiseNo) {
		return 1789772.5 / AudioUtil::$noiseFrequencies[$noiseNo & 0x0f];
	}

	public static function getValue($value, $minValue, $maxValue) {
		if ($value < $minValue)      $value = $minValue;
		else if ($value > $maxValue) $value = $maxValue;

		return $value;
	}
}

class PulseDevice implements AudioDevice {
	private $sampleRate;

	private $dutyCycle = 0;			// 0:12.5%, 1:25%, 2:50%, 3:75%
	private $volume = 0;			//   0 ~ 15
	private $note = 0;				//   0 ~

	private $offsetVolume = 0;		//   0 ~ 15
	private $offsetNote = 0;		// -64 ~ 63
	private $offsetFrequency = 0;	// -64 ~ 63

	private $amp = 0;
	private $tone = 440;
	private $theta = 0;
	private $theta_delta = 0;

	private $stopped = true;

	public function __construct($sampleRate) {
		$this->noteOff();
		$this->setSampleRate($sampleRate);
	}

	public function setSampleRate($value) {
		$this->sampleRate = $value;
	}

	public function setVoice($value) {
		$this->dutyCycle = $value & 3;
	}

	public function setVolume($value) {
		$this->volume = AudioUtil::getValue($value, 0, 15);
	}

	public function setNote($value) {
		$this->note = $value;
	}

	public function setOffsetVolume($value) {
		$this->offsetVolume = AudioUtil::getValue($value, 0, 15);
	}

	public function setOffsetNote($value) {
		$this->offsetNote = AudioUtil::getValue($value, -64, 63);
	}

	public function setOffsetFrequency($value) {
		$this->offsetFrequency = AudioUtil::getValue($value, -64, 63);
	}

	public function noteOn($noteNo) {
		$this->stopped = false;
		$this->note = $noteNo;
	}

	public function noteOff() {
		$this->stopped = true;
		$this->theta = 0;
	}

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

			$noteNo = $this->note + $this->offsetNote;
			$this->tone = AudioUtil::getFrequency($noteNo) + $this->offsetFrequency;
			$this->theta_delta = $this->tone * 2 * M_PI / $this->sampleRate;
			$this->theta += $this->theta_delta;
		}

		return $v;
	}
}

class TriangleDevice implements AudioDevice {
	private $sampleRate;

	private $note = 0;				//   0 ~

	private $offsetNote = 0;		// -64 ~ 63
	private $offsetFrequency = 0;	// -64 ~ 63

	private $amp = 0;
	private $tone = 440;
	private $theta = 0;
	private $theta_delta = 0;

	private $stopped = true;

	public function __construct($sampleRate) {
		$this->amp = AudioDevice::baseAmp;
		$this->noteOff();
		$this->setSampleRate($sampleRate);
	}

	public function setSampleRate($value) {
		$this->sampleRate = $value;
	}

	public function setVoice($value) {
	}

	public function setVolume($value) {
	}

	public function setNote($value) {
		$this->note = $value;
	}

	public function setOffsetVolume($value) {
	}

	public function setOffsetNote($value) {
		$this->offsetNote = AudioUtil::getValue($value, -64, 63);
	}

	public function setOffsetFrequency($value) {
		$this->offsetFrequency = AudioUtil::getValue($value, -64, 63);
	}

	public function noteOn($noteNo) {
		$this->stopped = false;
		$this->note = $noteNo;
	}

	public function noteOff() {
		$this->stopped = true;
	}

	public function sampling() {
		$d = (2 * acos(cos($this->theta)) - M_PI) / M_PI;
		$v = floor($this->amp * $d);
		$v -= $v % ((AudioDevice::baseAmp - 0x100) / 8);

		if (!$this->stopped) {
			$noteNo = $this->note + $this->offsetNote;
			$this->tone = AudioUtil::getFrequency($noteNo) + $this->offsetFrequency;
			$this->theta_delta = $this->tone * 2 * M_PI / $this->sampleRate;
			$this->theta += $this->theta_delta;
		}

		return $v;
	}
}

class NoiseDevice implements AudioDevice {
	private $sampleRate;

	private $shortFreq = false;
	private $volume = 0;			//   0 ~ 15
	private $note = 0;				//   0 ~

	private $offsetVolume = 0;		//   0 ~ 15
	private $offsetNote = 0;		// -64 ~ 63

	private $amp = 0;
	private $tone = 440;
	private $theta = 0;
	private $theta_delta = 0;

	private $reg = 0x8000;
	private $edge = false;

	private $stopped = true;

	public function __construct($sampleRate) {
		$this->noteOff();
		$this->setSampleRate($sampleRate);
	}

	public function setSampleRate($value) {
		$this->sampleRate = $value;
	}

	public function setVoice($value) {
		$this->shortFreq = ($value != 0);
	}

	public function setVolume($value) {
		$this->volume = AudioUtil::getValue($value, 0, 15);
	}

	public function setNote($value) {
		$this->note = $value;
	}

	public function setOffsetVolume($value) {
		$this->offsetVolume = AudioUtil::getValue($value, 0, 15);
	}

	public function setOffsetNote($value) {
		$this->offsetNote = AudioUtil::getValue($value, -64, 63);
	}

	public function setOffsetFrequency($value) {
	}

	public function noteOn($noteNo) {
		$this->stopped = false;
		$this->note = $noteNo;
	}

	public function noteOff() {
		$this->stopped = true;
		$this->theta = 0;
	}

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

			$noteNo = $this->note + $this->offsetNote;
			$this->tone = AudioUtil::getNoiseFrequency($noteNo);
			$this->theta_delta = $this->tone * 2 * M_PI / $this->sampleRate;
			$this->theta += $this->theta_delta;
		}

		return $v;
	}
}

class MonauralMixer implements AudioMixer {
	public function mixing($positions, $values) {
		$count = count($values);
		$value = 0;

		for ($i = 0; $i < $count; ++$i) {
			$p = $positions[$i];
			$v = $values[$i];

			// Scale : -1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0
			//      =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5 ~ 1.75~ 2.0
			//                                       ______.......------+++
			//         ______.......------++++++*****
			$s = AudioUtil::getValue($p[1] + 1.0, 0, 2);

			$value += $v * $s;
		}

		return $value;
	}
}

class StereoMixer implements AudioMixer {
	public function mixing($positions, $values) {
		$count = count($values);
		$l = 0;
		$r = 0;

		for ($i = 0; $i < $count; ++$i) {
			$p = $positions[$i];

			// Pan(left) : -1.5 ~-1.25~-1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5
			//          =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 0.75~ 0.5 ~ 0.25~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0
			//             ______.......------++++++*****+++++------......_____________________________
			// n + 0.5     -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0   1.25  1.5   1.75  2.0
			// abs(n)       1.0   0.75  0.5   0.25  0.0   0.25  0.5   0.75  1.0   1.25  1.5   1.75  2.0
			// 1.0 - n      0.0   0.25  0.5   0.75  1.0   0.75  0.5   0.25  0.0  -0.25 -0.5  -0.75 -1.0
			$pl = AudioUtil::getValue(1.0 - abs($p[0] + 0.5), 0, 1);

			// Pan(right): -1.5 ~-1.25~-1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5
			//          =>  0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 0.75~ 0.5 ~ 0.25~ 0.0
			//             ______________________________.......------++++++*****+++++------......_____
			// n - 0.5     -2.0  -1.75 -1.5  -1.25 -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0
			// abs(n)       2.0   1.75  1.5   1.25  1.0   0.75  0.5   0.25  0.0   0.25  0.5   0.75  1.0
			// 1.0 - n     -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0   0.25  0.5   0.25  0.0
			$pr = AudioUtil::getValue(1.0 - abs($p[0] - 0.5), 0, 1);

			// Scale : -1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0
			//      =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5 ~ 1.75~ 2.0
			//                                       ______.......------+++
			//         ______.......------++++++*****
			$s = AudioUtil::getValue($p[1] + 1.0, 0, 2);

			$v = $values[$i] * $s;
			$l += $v * $pl;
			$r += $v * $pr;
		}

		return [$l, $r];
	}
}

class Monaural8bitPacker implements AudioPacker {
	public function packing($scale, $values) {
		$value = 0;
		foreach ($values as $v) {
			$value += $v;
		}
		$value = AudioUtil::getValue($value * $scale, -0x8000, 0x7fff);
		return pack('C', ($value + 0x8000) / 256);
	}
}

class Monaural16bitPacker implements AudioPacker {
	public function packing($scale, $values) {
		$value = 0;
		foreach ($values as $v) {
			$value += $v;
		}
		$value = AudioUtil::getValue($value * $scale, -0x8000, 0x7fff);
		return pack('v', $value);
	}
}

class Stereo8bitPacker implements AudioPacker {
	public function packing($scale, $values) {
		$left  = 0;
		$right = 0;
		foreach ($values as $v) {
			$left  += $v[0];
			$right += $v[1];
		}
		$left  = AudioUtil::getValue($left  * $scale, -0x8000, 0x7fff);
		$right = AudioUtil::getValue($right * $scale, -0x8000, 0x7fff);
		return pack('CC', ($left + 0x8000) / 256, ($right + 0x8000) / 256);
	}
}

class Stereo16bitPacker implements AudioPacker {
	public function packing($scale, $values) {
		$left  = 0;
		$right = 0;
		foreach ($values as $v) {
			$left  += $v[0];
			$right += $v[1];
		}
		$left  = AudioUtil::getValue($left  * $scale, -0x8000, 0x7fff);
		$right = AudioUtil::getValue($right * $scale, -0x8000, 0x7fff);
		return pack('vv', $left, $right);
	}
}

class PseudoApu {
	public $pulse1;
	public $pulse2;
	public $triangle;
	public $noise;

	public $devices;
	public $trackNumbers;

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

	public function reset() {
		foreach ($this->devices as $dev) {
			$dev->noteOff();
		}
	}

	public function sampling() {
		$values = [];

		foreach ($this->trackNumbers as $tr) {
			$values[] = $this->devices[$tr]->sampling();
		}
		return $values;
	}
}

class AudioUnit {
	public $name;
	public $apu;
	public $trackNumbers;
	public $positions;

	public function __construct($sampleRate, $params) {
		foreach ($params as $key => $value) {
			switch ($key) {
			case 'Name':
				// ユニット名
				$this->name = $value;
				break;

			case 'Devices':
				// トラック、定位
				$this->positions = array_values($value);
				if ($this->positions === $value) {
					$this->trackNumbers = $this->positions;

					$this->positions = [];
					foreach ($this->trackNumbers as $tr) {
						$this->positions[$tr] = [0, 0];
					}

				} else {
					$this->trackNumbers = array_keys($value);
				}
				break;
			}
		}

		// 擬似APU設定
		$this->apu = new PseudoApu($sampleRate, $this->trackNumbers);
	}
}

class ApuMmlPlayer {
	public $sampleRate = 44100;		// CD quality
	public $sampleBits = 16;		// 8 or 16 bits
	public $nChannel = 2;			// 1:Monaural, 2:Stereo
	public $volumeScale = 0.8;

	public $sampleTime = 1.0;		// seconds
	public $loopEnd = false;

	private $audioUnits;
	private $mixer;
	private $packer;

	const MaxSampleTime = 5 * 60;

	public function __construct() {
		AudioUtil::initialize();
		$this->setup();
	}

	private function validate() {
		if (0 <= $this->sampleTime) $this->sampleTime = 1.0;
		if ($this->sampleTime > ApuMmlPlayer::MaxSampleTime) $this->sampleTime = ApuMmlPlayer::MaxSampleTime;
	}

	// 'Devices' => [1, 2, 3, 4];	// 1:pulse1, 2:pulse2, 3:triangle, 4:noise
	public function setup($args = [['Devices'=>[1, 2, 3, 4]]]) {
		// オーディオユニット設定
		$this->audioUnits = [];
		foreach ($args as $arg) {
			$audioUnit = new AudioUnit($this->sampleRate, $arg);
			if (is_null($audioUnit->name)) {
				$audioUnit->name = count($this->audioUnits);
				$this->audioUnits[] = $audioUnit;
			} else {
				$this->audioUnits[$audioUnit->name] = $audioUnit;
			}
		}

		// 出力設定
		if ($this->nChannel == 1) {
			// ミキサー
			$this->mixer = new MonauralMixer($this->sampleBits);

			// パッカー
			switch ($this->sampleBits) {
			case 8:
				$this->packer = new Monaural8bitPacker();
				break;

			case 16:
			default:
				$this->packer = new Monaural16bitPacker();
				break;
			}

		} else {
			// ミキサー
			$this->mixer = new StereoMixer($this->sampleBits);

			// パッカー
			switch ($this->sampleBits) {
			case 8:
				$this->packer = new Stereo8bitPacker();
				break;

			case 16:
			default:
				$this->packer = new Stereo16bitPacker();
				break;
			}
		}
	}

	public function play($mml) {
		// プロパティ検証
		$this->validate();

		// サンプリング初期化
		$sequenceCount = floor($this->sampleRate / 60);
		$totalSamples = $this->sampleRate * $this->sampleTime;
		$data = '';


		// コメント除去
		// 改行調整
		// シーケンスデータ生成

		for ($i = 0; $i < $totalSamples; ++$i) {
			// シーケンス制御
			if ($i % $sequenceCount == 0) {

			}

			// サウンドサンプリング
			$rawData = [];
			foreach ($this->audioUnits as $audioUnit) {
				// サンプリング
				$sampling = $audioUnit->apu->sampling();

				// ミキシング
				$mixing = $this->mixer->mixing($audioUnit->positions, $sampling);

				$rawData[] = $mixing;
			}

			// パッキング
			$data .= $this->packer->packing($this->volumeScale, $rawData);
		}

		return $data;
	}

	public function testSound($notes) {
		// プロパティ検証
		$this->validate();

		// サンプリング初期化
		$totalSamples = $this->sampleRate * $this->sampleTime;
		$data = '';

		// テストサウンド設定
		foreach ($notes as $name => $values) {
			$apu = $this->audioUnits[$name]->apu;
			foreach ($values as $tr => $note) {
				$tr = $apu->devices[$tr];
				$tr->setVoice($note['Voice']);
				$tr->setVolume($note['Volume']);
				$tr->noteOn($note['NoteNo']);
			}
		}

		// テストサウンドサンプリング
		for ($i = 0; $i < $totalSamples; ++$i) {

			$rawData = [];
			foreach ($this->audioUnits as $audioUnit) {
				// サンプリング
				$sampling = $audioUnit->apu->sampling();

				// ミキシング
				$mixing = $this->mixer->mixing($audioUnit->positions, $sampling);

				$rawData[] = $mixing;
			}

			// パッキング
			$data .= $this->packer->packing($this->volumeScale, $rawData);
		}

		return $data;
	}
}
