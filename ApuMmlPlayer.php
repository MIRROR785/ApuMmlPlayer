<?php
//
// ApuMmlPlayer.php
//
// Written by @MIRROR_
//
require_once('PseudoApu.php');
require_once('MmlContainer.php');

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

	public function play($mml, $name, $loop = 0) {
		// プロパティ検証
		$this->validate();

		// サンプリング初期化
		$sequenceCount = floor($this->sampleRate / 60);
		$totalSamples = $this->sampleRate * $this->sampleTime;
		$data = '';

		// TODO : シーケンサー制御初期化

		for ($i = 0; $i < $totalSamples; ++$i) {
			// TODO : シーケンサー制御
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
