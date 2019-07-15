<?php
/**
 * MmlContainer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * MMLデータを格納するクラス
 */
class MmlContainer {
	/** @var MmlDefine[] 定義値配列 */
	private $define;

	/** @var MmlEnvelope[] エンベロープ配列 */
	private $envelope;

	/** @var MmlMusic[] 曲情報配列 */
	private $music;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		$this->define = [];
		$this->envelope = [];
		$this->music = [];
	}

	/**
	 * 定義の有無を判定
	 * @param string $name 定義名
	 * @param bool 判定結果
	 */
	public function isDefine($name) {
		$tmp = new MmlDefine($name);
		return array_key_exists($this->define, $tmp->getKey());
	}

	/**
	 * エンベロープの有無を判定
	 * @param int $index インデックス
	 * @return bool 判定結果
	 */
	public function hasEnvelope($index) {
		$tmp = new MmlEnvelope($index);
		return array_key_exists($this->envelope, $tmp->getKey());
	}

	/**
	 * 曲情報の有無を判定
	 * @param MmlMusic::TYPE_SE|TYPE_BGM|TYPE_SUB $type 曲情報種別
	 * @param int $index インデックス
	 * @return 判定結果
	 */
	public function hasMusic($type, $index) {
		$tmp = new MmlMusic($type, $index);;
		return array_key_exists($this->music, $tmp->getKey());
	}

	/**
	 * 定義を設定
	 * @param MmlDefine $define 定義情報
	 */
	public function setDefine($define) {
		$this->define[$define->getKey()] = $define;
	}

	/**
	 * エンベロープ情報を設定
	 * @param MmlEnvelope $envelope エンベロープ情報
	 */
	public function setEnvelope($envelope) {
		$this->envelope[$envelope->getKey()] = $envelope;
	}

	/**
	 * 曲情報を設定
	 * @param MmlMusic $music 曲情報
	 */
	public function setMusic($music) {
		$this->music[$music->getKey()] = $music;
	}

	/**
	 * 定義情報を取得
	 * @param string $name 定義名
	 * @return MmlDefine|null 定義情報
	 */
	public function getDefine($name) {
		$tmp = new MmlDefine($name);
		$key = $tmp->getKey();
		return array_key_exists($this->define, $key) ?
			$this->define[$key] : null;
	}

	/**
	 * エンベロープ情報を取得
	 * @param int $index インデックス
	 * @return MmlEnvelope|null エンベロープ情報
	 */
	public function getDevelope($index) {
		$tmp = new MmlEnvelope($index);
		$key = $tmp->getKey();
		return array_key_exists($this->envelope, $key) ?
			$this->envelope[$key] : null;
	}

	/**
	 * 曲情報を取得
	 * @param MmlMusic::TYPE_SE|TYPE_BGM|TYPE_SUB $type 曲情報種別
	 * @param int $index インデックス
	 * @return MmlMusic|null 曲情報
	 */
	public function getMusic($type, $index) {
		$tmp = new MmlMusic($type, $index);
		$key = $tmp->getKey();
		return array_key_exists($this->music, $key) ?
			$this->music[$key] : null;
	}

	/**
	 * 曲数を取得
	 * @param MmlMusic::TYPE_SE|TYPE_BGM|TYPE_SUB $type 曲情報種別
	 * @return int 曲数
	 */
	public function getMusicCount($type) {
		$count = 0;

		if (MmlMusic::isType($type)) {
			$name = '#'.$type;
			$define = $this->getDefine($name);
			if ($define !== null) {
				$count = intval($define->value);

			} else {
				$regex = '/^'.$type.'/is';
				foreach (array_keys($this->music) as $key) {
					if (preg_match($regex, $key)) {
						++$count;
					}
				}
				$tmp = new MmlDefine($name, $count);
				$this->setDefine($tmp);
			}
		}

		return $count;
	}
}

/**
 * 定義情報クラス
 */
class MmlDefine {
	/** @var string 定義名 */
	public $name;

	/** @var string|int 定義値 */
	public $value;

	/**
	 * コンストラクタ
	 * @param string $name 定義名
	 * @param string|int $value 定義値
	 */
	public function __construct($name = null, $value = null) {
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * 検索キーを取得
	 * @return string 検索キー
	 */
	public function getKey() {
		return strtoupper($this->name);
	}
}

/**
 * エンベロープ情報クラス
 */
class MmlEnvelope {
	/** @var int インデックス */
	public $index;

	/** @var string[] 定義値配列 */
	public $values;

	/**
	 * コンストラクタ
	 * @param int $index インデックス
	 * @param string[] 定義値配列
	 */
	public function __construct($index = null, $values = null) {
		$this->index = $index;
		$this->values = ($values !== null) ? $values : [];
	}

	/**
	 * 定義値追加
	 * @param string $value 定義値
	 */
	public function add($value) {
		$this->values[] = $value;
	}

	/**
	 * 検索キーを取得
	 * @return int 検索キー
	 */
	public function getKey() {
		return $this->index;
	}
}

/**
 * 曲情報クラス
 */
class MmlMusic {
	/** @var string 曲種別SE */
	const TYPE_SE = 'SE';

	/** @var string 曲種別BGM */
	const TYPE_BGM = 'BGM';

	/** @var string 曲種別SUB */
	const TYPE_SUB = 'SUB';

	/**
	 * 種別判定
	 * @param string $type 種別値
	 * @return bool 判定結果
	 */
	public static function isType($type) {
		$type = strtoupper($type);
		if ($type === 'S') {
			$type = MmlMusic::TYPE_SUB;
		}

		return ($type === MmlMusic::TYPE_SE
				|| $type === MmlMusic::TYPE_BGM
				|| $type === MmlMusic::SUB);
	}

	/** @var string 曲種別 */
	public $type;

	/** @var int インデックス */
	public $index;

	/** @var MmlDefine[] 定義情報配列 */
	public $define;

	/** @var string=>string[] トラック情報配列 */
	public $values;

	/**
	 * コンストラクタ
	 * @param string $type 曲種別
	 * @param int $index インデックス
	 * @param MmlDefine[] $define 定義情報配列
	 * @param string=>string[] トラック情報配列
	 */
	public function __construct($type = null, $index = null, $define = null, $values = null) {
		$this->type = $type;
		$this->index = $index;
		$this->define = ($define !=null) ? $define : [];
		$this->values = ($values !== null) ? $values : [];
	}

	/**
	 * トラック情報の追加
	 * @param string $track トラック名
	 * @param string $value トラック情報
	 */
	public function add($track, $value) {
		if ($track === null) {
			$track = 'TR0';
		}

		$tr = (array_key_exists($track, $this->values)) ? $this->values[$track] : [];
		$tr[] = $value;
		$this->values[$track] = $tr;
	}

	/**
	 * 検索キーを取得
	 * @return string 検索キー
	 */
	public function getKey() {
		$key = strtoupper($this->type);
		if ($key === 'S') {
			$key = MmlMusic::TYPE_SUB;
		}
		return $key.$this->index;
	}

	/**
	 * 定義判定
	 * @param string $name 定義名
	 * @return bool 判定結果
	 */
	public function isDefine($name) {
		$tmp = new MmlDefine($name);
		return array_key_exists($this->define, $tmp->getKey());
	}

	/**
	 * 定義情報設定
	 * @param MmlDefine $define 定義情報
	 */
	public function setDefine($define) {
		$this->define[$define->getKey()] = $define;
	}

	/**
	 * 定義情報取得
	 * @param string $name 定義名
	 * @return MmlDefine 定義情報
	 */
	public function getDefine($name) {
		$tmp = new MmlDefine($name);
		$key = $tmp->getKey();
		return array_key_exists($this->define, $key) ?
			$this->define[$key] : null;
	}
}

/**
 * 定義情報書き込みクラス
 */
class MmlDefineWriter {
	/** @var MmlDefine 定義情報 */
	private $define;

	/**
	 * コンストラクタ
	 * @param MmlDefine $define 定義情報
	 */
	public function __construct($define) {
		$this->define = $define;
	}

	/**
	 * 定義値設定
	 * @param string|int $value 定義値
	 */
	public function set($value) {
		$this->define->value = $value;
	}

	/**
	 * 定義情報をコンテナへ登録
	 * @param MmlContainer|MmlMusic $container コンテナ
	 */
	public function push($container) {
		$container->setDefine($this->define);
	}

	/**
	 * コンテナを取得
	 * @return null コンテナ
	 */
	public function getContainer() {
		return null;
	}
}

/**
 * エンベロープ情報書き込みクラス
 */
class MmlEnvelopeWriter {
	/** @var MmlEnvelope エンベロープ情報 */
	private $envelope;

	/**
	 * コンストラクタ
	 * @param MmlEnvelope エンベロープ情報
	 */
	public function __construct($envelope) {
		$this->envelope = $envelope;
	}

	/**
	 * インデックスの設定
	 * @param int $index インデックス
	 */
	public function setIndex($index) {
		$this->envelope->index = $index;
	}

	/**
	 * エンベロープ値の追加
	 * @param string|int $value エンベロープ値
	 */
	public function add($value) {
		$this->envelope->add($value);
	}

	/**
	 * エンベロープ情報をコンテナへ登録
	 * @param MmlContainer $container コンテナ
	 */
	public function push($container) {
		$container->setEnvelope($this->envelope);
	}

	/**
	 * コンテナを取得
	 * @return null コンテナ
	 */
	public function getContainer() {
		return null;
	}
}

/**
 * 曲情報書き込みクラス
 */
class MmlMusicWriter {
	/** @var MmlMusic 曲情報 */
	public $music;

	/** @var string トラック名 */
	public $track;

	/**
	 * コンストラクタ
	 * @param MmlMusic 曲情報
	 */
	public function __construct($music) {
		$this->music = $music;
	}

	/**
	 * インデックスの設定
	 * @param int $index インデックス
	 */
	public function setIndex($index) {
		$this->music->index = $index;
	}

	/**
	 * 曲値の追加
	 * @param string|int $value 曲値
	 */
	public function add($value) {
		if (preg_match('/^TR/is', $value)) {
			$this->track = $value;
		} else {
			$this->music->add($this->track, $value);
		}
	}

	/**
	 * 曲情報をコンテナへ登録
	 * @param MmlContainer $container コンテナ
	 */
	public function push($container) {
		$container->setMusic($this->music);
	}

	/**
	 * コンテナを取得
	 * @return MmlMusic コンテナ
	 */
	public function getContainer() {
		return $this->music;
	}
}

/**
 * MMLコンテナのシンタックスエラー例外クラス
 */
class MmlContainerSyntaxErrorException extends Exception {
	public function __construct(){}
}

/**
 * MMLコンテナの字句解析状態クラス
 */
class MmlContainerLexStatus {
	/** @var 字句状態：値なし */
	const NOTHING       = 0;

	/** @var 字句状態：コメントブロック */
	const COMMENT_BLOCK = 1; // /**/

	/** @var 字句状態：コメントライン */
	const COMMENT_LINE  = 2; // //

	/** @var 字句状態：シングルクォート */
	const SINGLE_QUOTE  = 3; // ''

	/** @var 字句状態：ダブルクォート */
	const DOUBLE_QUOTE  = 4; // ""

	/** @var 字句状態：丸括弧 */
	const PARENTHESES   = 5; // ()

	/** @var 字句状態：波括弧 */
	const CURLY_BRACKET = 6; // {}

	/** @var 字句状態：値 */
	const VALUE         = 7; // #name, value, name, index, values

	/** @var 字句状態 */
	public $status;

	/** @var 終端文字 */
	public $terminater;

	/** @var 解析値 */
	public $value;

	/**
	 * コンストラクタ
	 * @param int $status 字句状態
	 * @param char $terminater 終端文字
	 * @param string $value 解析値
	 */
	public function __construct($status = MmlContainerLexStatus::NOTHING,
								$terminater = null,
								$value = '') {
		$this->status = $status;
		$this->terminater = $terminater;
		$this->value = $value;
	}
}

/**
 * MMLコンテナの構文解析情報クラス
 */
class MmlContainerParserInfo {
	/** @var int 解析種別 */
	public $type;

	/** @var int パラメータ種別 */
	public $param;

	/** @var 解析値 */
	public $value;

	/**
	 * コンストラクタ
	 * @param int $type 解析種別
	 * @param int $param パラメータ種別
	 * @param string $value 解析値
	 */
	public function __construct($type = MmlContainerParser::NOTHING,
								$param = MmlContainerParser::PARSE_PARAM_NAME,
								$value = null) {
		$this->type = $type;
		$this->param = $param;
		$this->value = $value;
	}
}

/**
 * MMLコンテナの構文解析クラス
 */
class MmlContainerParser {
	/** @var int 構文解析状態：初期状態 */
	const STATUS_READY  = 0;

	/** @var int 構文解析状態：読み込み中 */
	const STATUS_READ   = 1;

	/** @var int 構文解析状態：読み込み完了 */
	const STATUS_DONE   = 2;

	/**  @var int 構文解析状態：エラー発生 */
	const STATUS_ERROR  =-1;


	/** @var int 値なし */
	const NOTHING       = 0;


	/** @var int 字句解析種別：値 */
	const LEX_TYPE_VALUE         = 0x01;

	/** @var int 字句解析種別：エスケープ */
	const LEX_TYPE_ESCAPE        = 0x02;

	/** @var int 字句解析種別：シングルクォート */
	const LEX_TYPE_SINGLE_QUOTE  = 0x03;

	/** @var int 字句解析種別：ダブルウォート */
	const LEX_TYPE_DOUBLE_QUOTE  = 0x04;

	/** @var int 字句解析種別：丸括弧 */
	const LEX_TYPE_PARENTHESES   = 0x05;

	/** @var int 字句解析種別：波括弧 */
	const LEX_TYPE_CURLY_BRACKET = 0x06;

	/** @var int 字句解析種別：定義 */
	const LEX_TYPE_DEFINE        = 0x07;

	/** @var int 字句解析種別：コメントブロック */
	const LEX_TYPE_COMMENT_BLOCK = 0x08;

	/** @var int 字句解析種別：コメントライン */
	const LEX_TYPE_COMMENT_LINE  = 0x09;

	/** @var int 字句解析種別：スペース */
	const LEX_TYPE_SPACE         = 0x0a;

	/** @var int 字句解析種別：タブ */
	const LEX_TYPE_TAB           = 0x0b;

	/** @var int 字句解析種別：カンマ */
	const LEX_TYPE_COMMA         = 0x0c;


	/** @var int 字句解析属性：クォート */
	const LEX_ATTR_QUOTE         = 0x01;

	/** @var int 字句解析属性：ブロック */
	const LEX_ATTR_BLOCK         = 0x02;

	/** @var int 字句解析属性：コメント */
	const LEX_ATTR_COMMENT       = 0x04;

	/** @var int 字句解析属性：区切り */
	const LEX_ATTR_SEPARATOR     = 0x08;

	/** @var int 字句解析属性：開始 */
	const LEX_ATTR_START         = 0x10;

	/** @var int 字句解析属性：終了 */
	const LEX_ATTR_END           = 0x20;

	/** @var int 字句解析属性：連結文字 */
	const LEX_ATTR_DOUBLE_CHAR   = 0x40;

	/** @var int 字句解析属性：行末 */
	const LEX_ATTR_EOL           = 0x80;


	/** @var int 解析種別：定義 */
	const PARSE_TYPE_DEFINE         = 1;	// #name value

	/** @var int 解析種別：エンベロープ */
	const PARSE_TYPE_ENVELOPE       = 2;	// E[nvelope] (index) {values}

	/** @var int 解析種別：曲情報 */
	const PARSE_TYPE_MUSIC          = 3;	// name (index) {values}


	/** @var int パラメータ種別：名称 */
	const PARSE_PARAM_NAME          = 0;

	/** @var int パラメータ種別：丸括弧開始 */
	const PARSE_PARAM_PAREN_START   = 1;

	/** @var int パラメータ種別：インデックス */
	const PARSE_PARAM_INDEX         = 2;

	/** @var int パラメータ種別：丸括弧終了 */
	const PARSE_PARAM_PAREN_END     = 3;

	/** @var int パラメータ種別：波括弧開始 */
	const PARSE_PARAM_BRACKET_START = 4;

	/** @var int パラメータ種別：値 */
	const PARSE_PARAM_VALUE         = 5;

	/** @var int パラメータ種別：波括弧終了 */
	const PARSE_PARAM_BRACKET_END   = 6;


	/** @var int 解析状態 */
	public $status;

	/** @var string 解析メッセージ */
	public $message;

	/** @var string 解析行 */
	public $line;

	/** @var int 解析行番号 */
	public $row;

	/** @var int 解析列番号 */
	public $column;

	/** @var char=>MmlContainerLexStatus ブロック情報配列 */
	private $blockInfos;

	/** @var MmlContainerLexStatus[] 字句解析状態スタック */
	private $lexStatusStack;

	/** @var regex=>MmlContainerParse::PARSE_TYPE_* 識別子名称配列 */
	private $nameInfos;

	/** @var MmlContainerParserInfo[] 構文解析情報スタック */
	private $parseInfoStack;

	/** @var MmlContainerParserInfo 構文解析情報 */
	private $parseInfo;

	/** @var MmlContainer MMLコンテナ */
	private $container;

	/**
	 * コンストラクタ
	 * @param MmlContainer $container MMLコンテナ
	 */
	public function __construct($container) {
		$this->container = $container;

		$this->blockInfos = [
			"'" => new MmlContainerLexStatus(MmlContainerLexStatus::SINGLE_QUOTE , "'"),
			'"' => new MmlContainerLexStatus(MmlContainerLexStatus::DOUBLE_QUOTE , '"'),
			'(' => new MmlContainerLexStatus(MmlContainerLexStatus::PARENTHESES  , ')'),
			'{' => new MmlContainerLexStatus(MmlContainerLexStatus::CURLY_BRACKET, '}'),

			'*' => new MmlContainerLexStatus(MmlContainerLexStatus::COMMENT_BLOCK),
			'/' => new MmlContainerLexStatus(MmlContainerLexStatus::COMMENT_LINE ),
			';' => new MmlContainerLexStatus(MmlContainerLexStatus::COMMENT_LINE ),
			];

		$this->nameInfos = [
			'/^#/' => MmlContainerParser::PARSE_TYPE_DEFINE,
			'/^(E|Envelope)$/is' => MmlContainerParser::PARSE_TYPE_ENVELOPE,
			'/^(SE|BGM|S|SUB)$/is' => MmlContainerParser::PARSE_TYPE_MUSIC,
			];

		$this->clear();
	}

	/**
	 * 解析状態をクリア
	 */
	public function clear() {
		$this->status = MmlContainerParser::STATUS_READY;
		$this->message = '';

		$this->line = '';
		$this->row = 0;
		$this->column = 0;

		$this->lexStatusStack = [new MmlContainerLexStatus(), new MmlContainerLexStatus()];
		$this->parseInfoStack = [];
		$this->parseInfo = new MmlContainerParserInfo();
	}

	/**
	 * MMLの解析
	 * @throw MmlContainerSyntaxErrorException 解析エラー発生時
	 */
	public function parse($mml) {
		$this->status = MmlContainerParser::STATUS_READ;
		$this->message = '';
		$lines = preg_split("/\r*\n|\r/", $mml);
		$lex = $this->popLexStatus();

		foreach ($lines as $line) {
			++$this->row;
			$this->column = 0;
			$this->line = $line;

			$chBuf = preg_split("//u", $line, -1, PREG_SPLIT_NO_EMPTY); // utf8
			//$chBuf = str_split($line."\n"); // ascii
			$count = count($chBuf);
			$chBuf[] = "\n";

			while ($this->column < $count) {
				$ch0 = $chBuf[$this->column++];
				$ch1 = $chBuf[$this->column];

				$lexType = MmlContainerParser::LEX_TYPE_VALUE;
				$lexAttr = 0;
				$this->analyzeChar($ch0, $ch1, $lexType, $lexAttr);
				//var_dump($lex);

				switch ($lex->status) {
				case MmlContainerLexStatus::NOTHING:
					//echo 'analyzeStatusNothing()'."\n";
					$this->analyzeStatusNothing($lexType, $lexAttr, $lex, $ch0, $ch1);
					break;

				case MmlContainerLexStatus::VALUE:
					//echo 'analyzeStatusValue()'."\n";
					$this->analyzeStatusValue($lexType, $lexAttr, $lex, $ch0, $ch1);
					break;

				case MmlContainerLexStatus::COMMENT_BLOCK:
					//echo 'analyzeStatusCommentBlock()'."\n";
					$this->analyzeStatusCommentBlock($lexType, $lexAttr, $lex, $ch0, $ch1);
					break;

				case MmlContainerLexStatus::COMMENT_LINE:
					//echo 'analyzeStatusCommentLine()'."\n";
					$this->analyzeStatusCommentLine($lexType, $lexAttr, $lex, $ch0, $ch1);
					break;

				default:
					$this->status = MmlContainerParser::STATUS_ERROR;
					$this->message = 'Not found lex status. ('.$lex->status.')';
					throw new MmlContainerSyntaxErrorException();
				}
				//var_dump($lex);
				//echo 'count='.count($this->lexStatusStack)."\n";
			}
		}

		//var_dump($lex);
		$this->pushLexStatus($lex);

		if ($this->status === MmlContainerParser::STATUS_READ
			&& $this->parseInfo->type === MmlContainerParser::NOTHING) {
			$this->status = MmlContainerParser::STATUS_DONE;
		}
	}

	/**
	 * 字句解析状態をスタックへ格納
	 * @param MmlContainerLexStatus $lex 字句解析状態
	 */
	private function pushLexStatus($lex) {
		array_push($this->lexStatusStack, $lex);
	}

	/**
	 * 字句解析状態をスタックから取得
	 * @return MmlContainerLexStatus 字句解析状態
	 */
	private function popLexStatus() {
		$lex = array_pop($this->lexStatusStack);
		return $lex;
	}

	/**
	 * 直前の字句解析状態を参照
	 * @return MmlContainerLexStatus 字句解析状態
	 */
	private function getLexStatus() {
		$count = count($this->lexStatusStack);
		$lex = $this->lexStatusStack[$count - 1];
		return $lex;
	}

	/**
	 * 文字の解析
	 * @param char &$ch0 現解析文字
	 * @param char &$ch1 次解析文字
	 * @param MmlContainerParser::LEX_TYPE_* &$lexType 字句解析種別格納先
	 * @param MmlContainerParser::LEX_ATTR_* &$lexAttr 字句解析属性格納先
	 * @throw MmlContainerSyntaxErrorException 解析エラー発生時
	 */
	private function analyzeChar(&$ch0, &$ch1, &$lexType, &$lexAttr) {
		// Check character type and attribute.
		switch ($ch0) {
		case "\\":
			if ($ch1 !== "\n") {
				$lexType = MmlContainerParser::LEX_TYPE_ESCAPE;
				$ch0 .= $ch1;
				++$this->column;
			} else {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found escape char.';
				throw new MmlContainerSyntaxErrorException();
			}
			break;
		case "'":
			$lexType = MmlContainerParser::LEX_TYPE_SINGLE_QUOTE;
			$lexAttr = MmlContainerParser::LEX_ATTR_QUOTE | MmlContainerParser::LEX_ATTR_BLOCK | MmlContainerParser::LEX_ATTR_SEPARATOR;
			break;
		case '"':
			$lexType = MmlContainerParser::LEX_TYPE_DOUBLE_QUOTE;
			$lexAttr = MmlContainerParser::LEX_ATTR_QUOTE | MmlContainerParser::LEX_ATTR_BLOCK | MmlContainerParser::LEX_ATTR_SEPARATOR;
			break;
		case '(':
			$lexType = MmlContainerParser::LEX_TYPE_PARENTHESES;
			$lexAttr = MmlContainerParser::LEX_ATTR_BLOCK | MmlContainerParser::LEX_ATTR_SEPARATOR | MmlContainerParser::LEX_ATTR_START;
			break;
		case ')':
			$lexType = MmlContainerParser::LEX_TYPE_PARENTHESES;
			$lexAttr = MmlContainerParser::LEX_ATTR_BLOCK | MmlContainerParser::LEX_ATTR_SEPARATOR | MmlContainerParser::LEX_ATTR_END;
			break;
		case '{':
			$lexType = MmlContainerParser::LEX_TYPE_CURLY_BRACKET;
			$lexAttr = MmlContainerParser::LEX_ATTR_BLOCK | MmlContainerParser::LEX_ATTR_SEPARATOR | MmlContainerParser::LEX_ATTR_START;
			break;
		case '}':
			$lexType = MmlContainerParser::LEX_TYPE_CURLY_BRACKET;
			$lexAttr = MmlContainerParser::LEX_ATTR_BLOCK | MmlContainerParser::LEX_ATTR_SEPARATOR | MmlContainerParser::LEX_ATTR_END;
			break;
		case '#':
			$lexType = MmlContainerParser::LEX_TYPE_DEFINE;
			break;
		case ';':
			$lexType = MmlContainerParser::LEX_TYPE_COMMENT_LINE;
			$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_START;
			break;
		case '/':
			switch ($ch1) {
			case '/':
				$lexType = MmlContainerParser::LEX_TYPE_COMMENT_LINE;
				$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_START | MmlContainerParser::LEX_ATTR_DOUBLE_CHAR;
				break;
			case '*':
				$lexType = MmlContainerParser::LEX_TYPE_COMMENT_BLOCK;
				$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_START | MmlContainerParser::LEX_ATTR_DOUBLE_CHAR;
				break;
			}
			break;
		case '*':
			switch ($ch1) {
			case '/':
				$lexType = MmlContainerParser::LEX_TYPE_COMMENT_BLOCK;
				$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_END | MmlContainerParser::LEX_ATTR_DOUBLE_CHAR;
				break;
			}
			break;
		case ' ':
			$lexType = MmlContainerParser::LEX_TYPE_SPACE;
			$lexAttr = MmlContainerParser::LEX_ATTR_SEPARATOR;
			break;
		case "\t":
			$lexType = MmlContainerParser::LEX_TYPE_TAB;
			$lexAttr = MmlContainerParser::LEX_ATTR_SEPARATOR;
			break;
		case ',':
			$lexType = MmlContainerParser::LEX_TYPE_COMMA;
			$lexAttr = MmlContainerParser::LEX_ATTR_SEPARATOR;
			break;
		}

		if ($ch1 === "\n") {
			$lexAttr |= MmlContainerParser::LEX_ATTR_EOL;
		}
	}

	/**
	 * 状態なし時の字句解析
	 * @param MmlContainerParser::LEX_TYPE_* $lexType 字句解析種別
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr 字句解析属性
	 * @param MmlContainerLexStatus &$lex 字句解析状態
	 * @param char &$ch0 現解析文字
	 * @param char &$ch1 次解析文字
	 * @throw MmlContainerSyntaxErrorException 解析エラー発生時
	 */
	private function analyzeStatusNothing($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		$pre = $this->getLexStatus();
		//echo 'pre='; var_dump($pre);
		switch ($pre->status) {
		case MmlContainerLexStatus::PARENTHESES:
		case MmlContainerLexStatus::CURLY_BRACKET:
			// Check block end.
			if ($ch0 === $pre->terminater) {
				//echo 'find block end. '.$ch0."\n";
				// Parse value.
				$lex = $this->popLexStatus();
				$lex->value = $ch0;
				$this->parseValue($lex);
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::NOTHING);
				return;
			}
			break;
		}

		switch ($lexType) {
		case MmlContainerParser::LEX_TYPE_COMMENT_BLOCK:
		case MmlContainerParser::LEX_TYPE_COMMENT_LINE:
			if ($lexAttr & MmlContainerParser::LEX_ATTR_START) {
				$this->pushLexStatus($lex);
				if ($lexAttr & MmlContainerParser::LEX_ATTR_DOUBLE_CHAR) {
					$bi = $this->blockInfos[$ch1];
					++$this->column;
				} else {
					$bi = $this->blockInfos[$ch0];
				}
				$lex = new MmlContainerLexStatus($bi->status);
				$ch0 = null;
			} else {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found comment start.';
				throw new MmlContainerSyntaxErrorException();
			}
			break;

		case MmlContainerParser::LEX_TYPE_SINGLE_QUOTE:
		case MmlContainerParser::LEX_TYPE_DOUBLE_QUOTE:
			$bi = $this->blockInfos[$ch0];
			$lex = new MmlContainerLexStatus($bi->status, $bi->terminater);
			$this->pushLexStatus($lex);
			$lex = new MmlContainerLexStatus(MmlContainerLexStatus::VALUE);
			$ch0 = null;
			break;

		case MmlContainerParser::LEX_TYPE_PARENTHESES:
		case MmlContainerParser::LEX_TYPE_CURLY_BRACKET:
			//echo 'find block start. '.$ch0."\n";
			if ($lexAttr & MmlContainerParser::LEX_ATTR_START) {
				$bi = $this->blockInfos[$ch0];
				$lex = new MmlContainerLexStatus($bi->status, $bi->terminater, $ch0);
				$this->parseValue($lex);
				$this->pushLexStatus($lex);
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::VALUE);
				$ch0 = null;
			} else {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found block start.';
				throw new MmlContainerSyntaxErrorException();
			}
			break;

		case MmlContainerParser::LEX_TYPE_SPACE:
		case MmlContainerParser::LEX_TYPE_TAB:
			$ch0 = null;
			break;
		}

		if ($ch0 !== null) {
			// Get value.
			if ($lex->status === MmlContainerLexStatus::NOTHING) {
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::VALUE);
			}
			$lex->value .= $ch0;
			//var_dump($lex);
			//echo $this->column.':'.$lex->value."\n";
		}
	}

	/**
	 * 値解析状態時の字句解析
	 * @param MmlContainerParser::LEX_TYPE_* $lexType 字句解析種別
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr 字句解析属性
	 * @param MmlContainerLexStatus &$lex 字句解析状態
	 * @param char &$ch0 現解析文字
	 * @param char &$ch1 次解析文字
	 * @throw MmlContainerSyntaxErrorException 解析エラー発生時
	 */
	private function analyzeStatusValue($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		$quoteEnabled = false;
		$bracketEnabled = false;
		$pre = $this->getLexStatus();
		//echo 'pre='; var_dump($pre);
		switch ($pre->status) {
		case MmlContainerLexStatus::SINGLE_QUOTE:
		case MmlContainerLexStatus::DOUBLE_QUOTE:
			$quoteEnabled = true;
			// Check block end.
			if ($ch0 === $pre->terminater) {
				//echo 'find block end. '.$ch0."\n";
				// Parse value.
				$pre->value = $lex->value;
				$this->parseValue($pre);
				$this->popLexStatus();
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::NOTHING);
				return;
			}
			break;

		case MmlContainerLexStatus::CURLY_BRACKET:
			$bracketEnabled = true;
		case MmlContainerLexStatus::PARENTHESES:
			// Check block end.
			if ($ch0 === $pre->terminater) {
				//echo 'find block end. '.$ch0."\n";
				// Parse value.
				$this->parseValue($lex);
				$lex = $this->popLexStatus();
				$lex->value = $ch0;
				$this->parseValue($lex);
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::NOTHING);
				return;
			}
			break;
		}

		if ($ch0 !== null && !$quoteEnabled) {
			// Check comment and block start.
			switch ($lexType) {
			case MmlContainerParser::LEX_TYPE_COMMENT_BLOCK:
			case MmlContainerParser::LEX_TYPE_COMMENT_LINE:
				if ($lexAttr & MmlContainerParser::LEX_ATTR_START) {
					$this->pushLexStatus($lex);
					$bi = $this->blockInfos[$ch1];
					$lex = new MmlContainerLexStatus($bi->status);
					$ch0 = null;
					++$this->column;
				} else {
					$this->status = MmlContainerParser::STATUS_ERROR;
					$this->message = 'Not found comment start.';
					throw new MmlContainerSyntaxErrorException();
				}
				break;

			case MmlContainerParser::LEX_TYPE_SINGLE_QUOTE:
			case MmlContainerParser::LEX_TYPE_DOUBLE_QUOTE:
				if ($lex->value !== '') {
					// Parse value.
					$this->parseValue($lex);
				}
				$bi = $this->blockInfos[$ch0];
				$lex = new MmlContainerLexStatus($bi->status, $bi->terminater);
				$this->pushLexStatus($lex);
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::VALUE);
				$ch0 = null;
				break;

			case MmlContainerParser::LEX_TYPE_PARENTHESES:
			case MmlContainerParser::LEX_TYPE_CURLY_BRACKET:
				//echo 'find block start. '.$ch0."\n";
				if ($lexAttr & MmlContainerParser::LEX_ATTR_START) {
					if ($lex->value !== '') {
						// Parse value.
						$this->parseValue($lex);
					}
					$bi = $this->blockInfos[$ch0];
					$lex = new MmlContainerLexStatus($bi->status, $bi->terminater, $ch0);
					$this->parseValue($lex);
					$this->pushLexStatus($lex);
					$lex = new MmlContainerLexStatus(MmlContainerLexStatus::VALUE);
					$ch0 = null;
				} else {
					$this->status = MmlContainerParser::STATUS_ERROR;
					$this->message = 'Not found block start.';
					throw new MmlContainerSyntaxErrorException();
				}
				break;

			case MmlContainerParser::LEX_TYPE_DEFINE:
				if ($lex->value !== '') {
					// Parse value.
					$this->parseValue($lex);
				}
				$lex = new MmlContainerLexStatus(MmlContainerLexStatus::VALUE);
				break;
			}
		}

		if ($ch0 !== null) {
			if (!$quoteEnabled
				&& ($lexAttr & (MmlContainerParser::LEX_ATTR_SEPARATOR|MmlContainerParser::LEX_ATTR_EOL))) {

				if (!($lexAttr & MmlContainerParser::LEX_ATTR_SEPARATOR) || $lex->value !== '') {
					if ($lexAttr & MmlContainerParser::LEX_ATTR_EOL) {
						// Get value.
						$lex->value .= $ch0;
						//var_dump($lex);
						//echo $this->column.':'.$lex->value."\n";
					}
					if ($lex->value !== '') {
						// Parse value.
						$this->parseValue($lex);
					}
					$lex = new MmlContainerLexStatus(MmlContainerLexStatus::NOTHING);
				}

			} else {
				// Get value.
				$lex->value .= $ch0;
				//var_dump($lex);
				//echo 'value: '.$this->column.':'.$lex->value."\n";
			}
		}
	}

	/**
	 * コメントブロック状態時の字句解析
	 * @param MmlContainerParser::LEX_TYPE_* $lexType 字句解析種別
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr 字句解析属性
	 * @param MmlContainerLexStatus &$lex 字句解析状態
	 * @param char &$ch0 現解析文字
	 * @param char &$ch1 次解析文字
	 */
	private function analyzeStatusCommentBlock($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		// Check block end.
		switch ($lexType) {
		case MmlContainerParser::LEX_TYPE_COMMENT_BLOCK:
			if ($lexAttr & MmlContainerParser::LEX_ATTR_END) {
				$lex = $this->popLexStatus();
				++$this->column;
			}
			break;
		}
		$ch0 = null;
	}

	/**
	 * コメントライン状態時の字句解析
	 * @param MmlContainerParser::LEX_TYPE_* $lexType 字句解析種別
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr 字句解析属性
	 * @param MmlContainerLexStatus &$lex 字句解析状態
	 * @param char &$ch0 現解析文字
	 * @param char &$ch1 次解析文字
	 */
	private function analyzeStatusCommentLine($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		// Check block end.
		if ($lexAttr & MmlContainerParser::LEX_ATTR_EOL) {
			$lex = $this->popLexStatus();
		}
		$ch0 = null;
	}

	/**
	 * 構文解析種別を検索
	 * @param string 検索値
	 * @return MmlContainerParser::PARSE_TYPE_* 構文解析種別
	 */
	private function findParseType($value) {
		$result = MmlContainerParser::NOTHING;
		foreach($this->nameInfos as $reg => $type) {
			if (preg_match($reg, $value)) {
				$result = $type;
				break;
			}
		}
		return $result;
	}

	/**
	 * 構文解析情報をスタックへ格納
	 */
	private function pushParseInfo() {
		if ($this->parseInfo !== null) {
			array_push($this->parseInfoStack, $this->parseInfo);
		}
		$this->parseInfo = null;
	}

	/**
	 * 構文解析情報をスタックから取得
	 */
	private function popParseInfo() {
		if (count($this->parseInfoStack) > 0) {
			$this->parseInfo = array_pop($this->parseInfoStack);
		} else {
			$this->parseInfo = new MmlContainerParserInfo();;
		}
	}

	/**
	 * 指定オフセット位置の構文解析情報を参照
	 * @param int $offset オフセット位置
	 * @return 構文解析情報
	 */
	private function getParseInfoStack($offset = 1) {
		$count = count($this->parseInfoStack);
		$index = $count - $offset;
		return (0 <= $index && $index < $count) ? $this->parseInfoStack[$index] : null;
	}

	/**
	 * 構文解析値コンテナを取得
	 * @return MmlContainer|MmlMusic 構文解析値コンテナ
	 */
	private function getParseValueContainer() {
		$container = null;
		$parseInfo = $this->getParseInfoStack();
		if ($parseInfo !== null) {
			$container = $parseInfo->value->getContainer();
		}
		return ($container === null) ? $this->container : $container;
	}

	/**
	 * 型識別子の構文解析
	 * @param MmlContainerLexStatus $lex 字句解析状態
	 * @param MmlContainerParser::PARSE_TYPE_* $parseType 構文解析種別
	 * @throw MmlContainerSyntaxErrorException 解析エラー発生時
	 */
	private function parseTypeName($lex, $parseType) {
		switch ($parseType) {
		case MmlContainerParser::PARSE_TYPE_DEFINE:
			if (mb_strlen($lex->value) <= 1) {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found define name.';
				throw new MmlContainerSyntaxErrorException();
			}
			$this->parseInfo = new MmlContainerParserInfo(
				$parseType,
				MmlContainerParser::PARSE_PARAM_VALUE,
				new MmlDefineWriter(new MmlDefine($lex->value)));
			break;
		case MmlContainerParser::PARSE_TYPE_ENVELOPE:
			$this->parseInfo = new MmlContainerParserInfo(
				$parseType,
				MmlContainerParser::PARSE_PARAM_PAREN_START,
				new MmlEnvelopeWriter(new MmlEnvelope()));
			break;
		case MmlContainerParser::PARSE_TYPE_MUSIC:
			$this->parseInfo = new MmlContainerParserInfo(
				$parseType,
				MmlContainerParser::PARSE_PARAM_PAREN_START,
				new MmlMusicWriter(new MmlMusic($lex->value)));
			break;
		default:
			$this->status = MmlContainerParser::STATUS_ERROR;
			$this->message = 'Illegal type name. ('.$lex->value.')';
			throw new MmlContainerSyntaxErrorException();
		}
	}

	/**
	 * 定義型の構文解析
	 * @param MmlContainerLexStatus $lex 字句解析状態
	 */
	private function parseTypeDefine($lex) {
		$this->parseInfo->value->set($lex->value);
		$this->parseInfo->value->push($this->getParseValuecontainer());
		$this->popParseInfo();
	}

	/**
	 * 値型の構文解析
	 * @param MmlContainerLexStatus $lex 字句解析状態
	 */
	private function parseTypeValue($lex) {
		switch ($this->parseInfo->param) {
		case MmlContainerParser::PARSE_PARAM_PAREN_START:
			if ($lex->value !== '(') {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found parentheses. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			++$this->parseInfo->param;
			break;

		case MmlContainerParser::PARSE_PARAM_INDEX:
			if (!ctype_digit($lex->value)){
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Illegal index parameter. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			$this->parseInfo->value->setIndex(intval($lex->value));
			++$this->parseInfo->param;
			break;

		case MmlContainerParser::PARSE_PARAM_PAREN_END:
			if ($lex->value !== ')') {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found parentheses. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			++$this->parseInfo->param;
			break;

		case MmlContainerParser::PARSE_PARAM_BRACKET_START:
			if ($lex->value !== '{') {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found curly bracket. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			++$this->parseInfo->param;
			break;

		case MmlContainerParser::PARSE_PARAM_VALUE:
		case MmlContainerParser::PARSE_PARAM_BRACKET_END:
			if ($lex->value === '}') {
				$this->parseInfo->value->push($this->getParseValuecontainer());
				$this->popParseInfo();
			} else {
				$parseType = $this->findParseType($lex->value);
				if ($parseType !== MmlContainerParser::NOTHING) {
					$this->pushParseInfo();
					$this->parseTypeName($lex, $parseType);
				} else {
					$this->parseInfo->value->add($lex->value);
				}
			}
			break;
		}
	}

	/**
	 * 解析種別毎の構文解析
	 * @param MmlContainerLexStatus $lex 字句解析状態
	 */
	private function parseValue($lex) {
		//echo 'parseValue: status='.$lex->status.', value='.$lex->value."\n";
		switch ($this->parseInfo->type) {
		case MmlContainerParser::PARSE_TYPE_DEFINE:
			$this->parseTypeDefine($lex);
			break;
		case MmlContainerParser::PARSE_TYPE_ENVELOPE:
		case MmlContainerParser::PARSE_TYPE_MUSIC:
			$this->parseTypeValue($lex);
			break;
		default:
			$parseType = $this->findParseType($lex->value);
			$this->parseTypeName($lex, $parseType);
			break;
		}
	}
}
