<?php
/**
 * ApuMmlPlayer.php
 *
 * @author @MIRROR_
 * @license MIT
 */
require_once('PseudoApu.php');
require_once('AudioUnit.php');
require_once('AudioMixer.php');
require_once('AudioPacker.php');
require_once('MmlContainer.php');
require_once('MmlSequencer.php');

/**
 * 擬似APUによるMMLプレイヤークラス
 */
class ApuMmlPlayer {
	/** @var int サンプリングレート */
	public $sampleRate = 44100;		// CD quality

	/** @var int サンプルビット数 */
	public $sampleBits = 16;		// 8 or 16 bits

	/** @var int チャンネル数 */
	public $nChannel = 2;			// 1:Monaural, 2:Stereo

	/** @var double ボリューム拡大率 */
	public $volumeScale = 0.8;

	/** @var double サンプリング時間(sec) */
	public $sampleTime = 1.0;		// seconds

	/** @var int ループカウンタ */
	public $loopCount = 0;

	/** @var bool ループ終了判定 */
	public $loopEnd = false;

	/** @var AudioUnit[] オーディオユニット配列 */
	private $audioUnits;

	/** @var AudioMixer オーディオミキサー */
	private $mixer;

	/** @var AudioPacker オーディオパッカー */
	private $packer;

	/** @var int サンプリング時間 */
	const MaxSampleTime = 5 * 60;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		AudioUtil::initialize();
		$this->setup();
	}

	/**
	 * 入力値の検証
	 */
	private function validate() {
		if (0 <= $this->sampleTime) $this->sampleTime = 1.0;
		if ($this->sampleTime > ApuMmlPlayer::MaxSampleTime) $this->sampleTime = ApuMmlPlayer::MaxSampleTime;
	}

	/**
	 * 設定
	 * @var (key=>value)[] $args 設定情報
	 * [[
	 *   'Name' => 'オーディオユニット名', // 省略可
	 *   'Devices' => [
	 *        デバイス番号   // 1:pulse1, 2:pulse2, 3:triangle, 4:noise
	 *          =>           // デバイス詳細定義（省略可）
	 *            ['Position' => [ -1.5 <= panning <= 1.5, -1.0 <= scale offset <= 1.0 ]],
	 *     ],
	 *   ],
	 * ]
	 */
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

	/**
	 * オーディオユニット毎の曲番を指定して演奏
	 * @param MmlContainer $container MMLデータコンテナ
	 * @param string[]|[key=>vallue] $sequenceMap シーケンサーと曲番のマッピング情報
	 * @return byte[] WAVデータ
	 */
	public function play($container, $sequenceMap = ['BGM1']) {
		// プロパティ検証
		$this->validate();

		// サンプリング初期化
		$sequenceCount = floor($this->sampleRate / 60);
		$totalSamples = $this->sampleRate * $this->sampleTime;
		$data = '';

		// シーケンサー制御初期化
		$aequencers = [];
		foreach ($sequenceMap as $key => $value) {
			$sequencers[$key] = new MmlSequencer($this->audioUnits[$key], $container, $value);
		}

		for ($i = 0; $i < $totalSamples; ++$i) {
			// シーケンサー制御
			if ($i % $sequenceCount === 0) {
				$isRunning = false;
				$isLoop = true;
				foreach ($sequencers as $sequencer) {
					$isRunning |= $sequencer->tick();
					$status = $sequencer->getStatus();
					$isLoop &= $status['isLoop'];
				}
				if (!$isRunning) {
					// 全停止の場合は終了
					break;
				}
				if ($isLoop) {
					// 全てのシーケンスがループ実行時は終了
					break;
				}
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
}
