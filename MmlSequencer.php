<?php
/**
 * MmlSequencer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * カウントクラス
 */
class MmlCount {
	/** @var int カウント値 */
	public $count;

	/** @var int 最大値 */
	public $max;

	/**
	 * コンストラクタ
	 * @param int $count 初期値
	 * @param int $max 最大値
	 */
	public function __construct($count, $max) {
		$this->count = $count;
		$this->max = $max;
	}
}

/**
 * 位置情報クラス
 */
class MmlPoint {
	/** @var int 行 */
	public $row;

	/** @var int 列 */
	public $column;

	/**
	 * コンストラクタ
	 * @param int $row 行
	 * @param int $column 列
	 */
	public function __construct($row, $column) {
		$this->row = $row;
		$this->column = $column;
	}
}

/**
 * MMLのトラック演奏を管理するクラス
 */
class MmlTrack {
	/** @var MmlSequencer MML演奏管理 */
	private $sequencer;

	/** @var int トラック番号 */
	private $trackNo;

	/** @var string[] MML情報 */
	private $mml;

	/** @var bool 実行状態判定 */
	private $isRunning;

	/** @var bool ループ状態判定 */
	private $isLoop;

	/** @var MmlMusic 呼び出しサブルーチン */
	private $sub;


	/** @var int SEの優先度 */
	private $priority;

	/** @var int 4分音符の分解能 */
	private $timeBase;

	/** @var int Qコマンドの分母 */
	private $qMax;

	/** @var bool ゲートクォンタイズコマンド Q, q の入れ替え */
	private $qReverse;

	/** @var bool 相対オクターブコマンド <, > の入れ替え */
	private $octaveReverse;

	/** @var int リピートコマンド A のコンパイル方法 */
	private $repeatMode;

	/** @vat int タイコマンド ^ のコンパイル方法 */
	private $tieMode;

	/** @var int 休符コマンド r のコンパイル方法 */
	private $rest;

	/** @var int 休符コマンド w のコンパイル方法 */
	private $wait;

	/** @var int リリースボリュームコマンド Rv の初期値 */
	private $releaseVolume;

	/** @var int E@ コマンドの引数オフセット */
	private $offsetEat;

	/** @var int Ev コマンドの引数オフセット */
	private $offsetEv;

	/** @var int Em コマンドの引数オフセット */
	private $offsetEm;

	/** @var int En コマンドの引数オフセット */
	private $offsetEn;


	/** @var int 基本テンポ(BPM) */
	private $baseTenpo;

	/** @var int 基本音色 */
	private $baseVoice;

	/** @var int 基本音量 */
	private $baseVolume;

	/** @var int 基本音長 */
	private $baseLength;

	/** @var int 基本オクターブ */
	private $baseOctave;

	/** @var int テンポ */
	private $tenpo;

	/** @var int 音色 */
	private $voice;

	/** @var int 音量 */
	private $volume;

	/** @var int 音長 */
	private $length;

	/** @var int オクターブ */
	private $octave;

	/** @var int キー番号 */
	private $keyNo;

	/** @var MmlCount 拍カウント */
	private $tick;

	/** @var char[] MML演奏行 */
	private $line;

	/** @var MmlCount MML演奏行 */
	private $row;

	/** @var MmlCount MML演奏列 */
	private $column;

	/** @var MmlPoint ループ先頭位置 */
	private $loopTop;

	/** @var MmlPoint リピート先頭位置 */
	private $repeatTop;

	/**
	 * コンストラクタ
	 * @param MmlSequencer $sequencer MML演奏管理
	 * @param int $trackNo トラック番号
	 * @param string[] $mml MML情報
	 */
	public function __construct($sequencer, $trackNo, $mml) {
		$this->sequencer = $sequencer;
		$this->trackNo = $trackNo;
		$this->mml = $mml;
		$this->clear();
	}

	/**
	 * トラック演奏情報のクリア
	 */
	public function clear() {
		$this->isRunning = false;
		$this->isLoop = false;
		$this->sub = null;

		$this->priority = intval($this->sequencer->getDefine('#priority', 0));
		$this->timeBase = intval($this->sequencer->getDefine('#timebase', 24));
		$this->qMax = intval($this->sequencer->getDefine('#QMax', 8));
		$this->qReverse = ($this->sequencer->getDefine('#QReverse') !== null);
		$this->octaveReverse = ($this->sequencer->getDefine('#octaveReverse') !== null);
		$this->repeatMode = intval($this->sequencer->getDefine('#RepeatMode', 0));
		$this->tieMode = intval($this->sequencer->getDefine('#TieMode', 0));
		$this->rest = intval($this->sequencer->getDefine('#rest', 2));
		$this->wait = intval($this->sequencer->getDefine('#wait', 0));
		$this->releaseVolume = intval($this->sequencer->getDefine('#ReleaseVolume', 2));
		$this->offsetEat = intval($this->sequencer->getDefine('#offsetE@', 0));
		$this->offsetEv = intval($this->sequencer->getDefine('#offsetEv', 0));
		$this->offsetEm = intval($this->sequencer->getDefine('#offsetEm', 0));
		$this->offsetEn = intval($this->sequencer->getDefine('#offsetEn', 0));

		$this->baseTenpo = 120;
		$this->baseVoice = 0;
		$this->baseVolume = 8;
		$this->baseLength = 24;
		$this->baseOctave = 4;
		$this->tenpo = $this->baseTenpo;
		$this->voice = $this->baseVoice;
		$this->volume = $this->baseVolume;
		$this->length = $this->baseLength;
		$this->octave = $this->baseOctave;
		$this->keyNo = 0;
		$this->tick = new MmlCount(0, 0);;
		$this->line = null;
		$this->row = new MmlCount(-1, count($this->mml));
		$this->column = new MmlPoint(0, 0);
		$this->loopTop = null;
		$this->repeat = null;
		$this->repeatTop = null;
	}

	/**
	 * １サンプリングのトラック演奏
	 */
	public function tick($device) {
		$this->isRunning = false;

		// Call sub track
		if ($this->sub !== null) {
			$this->isRunning = $this->sub->tick();

			if ($this->isRunning) {
				return $this->isRunning;
			} else {
				$this->sub = null;
			}
		}

		// Update note
		if ($this->tick->count >= $this->tick->max) {
			$this->tick->count = 0;
			$this->tick->max = 0;

			while ($this->row->count < $this->row->max) {
				if (++$this->column->count < $this->column->max) {
					// Read next code


					continue;

				} else if (++$this->row->count < $this->row->max) {
					// Reset read column
					$this->column->count = -1;

				} else {
					// Loop
					$this->isLoop = true;
					if ($this->loopTop === null) {
						break;
					}

					$this->row->count = $this->loopTop->row;
					$this->column->count = $this->loopTop->Column - 1;
				}

				// Read next line
				$this->line = preg_split("//u", $this->mml[$this->row->count], -1, PREG_SPLIT_NO_EMPTY); // utf8
				$this->column->max = count($this->line);
			}
		}

		// Update envelope
		if ($this->tick->max > 0) {

			$this->isRunninc = true;
		}

		return $this->isRunning;
	}

	/**
	 * 演奏状態を取得
	 * @return [] 演奏状態配列
	 */
	public function getStatus() {
		if ($this->sub !== null) {
			return $this->sub->getStatus();
		} else {
			return [
				'isRunning' => $this->isRunning,
				'isLoop' => $this->isLoop,
				];
		}
	}
}

/**
 * MMLの演奏を管理するクラス
 */
class MmlSequencer {
	/** @var AudioUnit オーディオユニット */
	private $audioUnit;

	/** @var MmlContainer MMLコンテナ */
	private $container;

	/** @var MmlMusic::TYPE_* 曲種別 */
	private $type;

	/** @var int インデックス */
	private $index;

	/** @var MmlSequencer 親シーケンサ */
	private $parent;

	/** @var MmlMusic 曲情報 */
	private $music;

	/** @var MmlTrack[] トラック情報配列 */
	private $tracks;

	/** @var bool 実行状態判定 */
	private $isRunning;

	/** @var bool ループ状態判定 */
	private $isLoop;

	/**
	 * コンストラクタ
	 * @param AudioUnit $audioUnit オーディオユニット
	 * @param MmlContainer $container MMLコンテナ
	 * @param string|[] $target 演奏対象情報
	 * @param MmlSequencer $parent 親シーケンサ
	 */
	public function __construct($audioUnit, $container, $target, $parent = null) {
		$this->audioUnit = $audioUnit;
		$this->container = $container;
		$this->parent = $parent;
		$this->setMusic($target);
	}

	/**
	 * 曲情報の設定
	 * @param string|[] $target 演奏対象情報
	 */
	private function setMusic($target) {
		if (is_array($target)) {
			$this->type = strtoupper($target['Type']);
			$this->index = intval($target['Index']);

		} else {
			$regex = '/BGM|SE|SUB/is';
			$result = null;
			if (preg_match($regex, $target, $result)) {
				$this->type = strtoupper($result[0][0]);
				$this->index = intval(substr($target, strlen($this->type)));
			}
		}

		$this->music = $this->container->getMusic($this->type, $this->index);
		$this->tracks = [];

		foreach ($this->music->values as $key => $value) {
			$trackNo = intval(str_replace('TR', '', $key));
			$track = new MmlTrack($this, $trackNo, $value);
			$this->tracks[$trackNo] = $track;
		}

		$this->audioUnit->apu->reset();
	}

	/**
	 * 定義情報を取得
	 * @param string $name 定義名
	 * @param string|int $define デフォルト値
	 * @return string|int 定義値
	 */
	public function getDefine($name, $default = null) {
		$value = $this->music->getDefine($name);
		if ($value === null) {
			$value = ($this->parent !== null) ?
				$this->parent->getDefine($name) :
			$this->container->getDefine($name);
		}
		return $value;
	}

	/**
	 * サブルーチン演奏情報を取得
	 * @param int $index インデックス
	 * @return MmlSequencer MML演奏情報
	 */
	public function getSub($index) {
		return new MmlSequencer($this->apu, $this->container,
								['Type' => MmlMusic::TYPE_SUB,
								 'Index' => $index],
								$this);
	}

	/**
	 * １サンプリングの全トラック演奏
	 */
	public function tick() {
		$this->isRunning = false;
		$this->isLoop = true;

		foreach ($this->tracks as $tackNo => $track) {
			$device = $this->audioUnit->apu->devices[$trackNo];
			$this->isRunning |= $track->tick($device);
			$status = $track->getStatus();
			$this->isLoop &= $status['isLoop'];
		}

		return $this->isRunning;
	}

	/**
	 * 演奏状態を取得
	 * @return [] 演奏状態配列
	 */
	public function getStatus() {
		return [
			'isRunning' => $this->isRunning,
			'isLoop' => $this->isLoop,
			];
	}
}
