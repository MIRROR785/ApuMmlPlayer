<?php
//
// MmlContainer.php
//
// Written by @MIRROR_
//

class MmlContainer {
	public $define;
	public $envelope;
	public $music;

	public function __construct() {
		$this->define = [];
		$this->envelope = [];
		$this->music = [];
	}

	public function getMusicCount($type) {
		if (!array_key_exists($this->define, $type)) {
			$count = 0;
			$regex = '/^'.$type.'/is';
			foreach (array_keys($this->music) as $key) {
				if (preg_match($regex, $key)) {
					++$count;
				}
			}
			$this->define[$type] = $count;
		}

		return intval($this->define[$type]);
	}
}

class MmlDefine {
	public $name;
	public $value;

	public function __construct($name = null, $value = null) {
		$this->name = $name;
		$this->value = $value;
	}

	public function getKey() {
		return strtoupper($this->name);
	}
}

class MmlEnvelope {
	public $index;
	public $values;

	public function __construct($index = null, $values = null) {
		$this->index = $index;
		$this->values = ($values !== null) ? $values : [];
	}

	public function add($value) {
		$this->values[] = $value;
	}

	public function getKey() {
		return $this->index;
	}
}

class MmlMusic {
	const TYPE_SE = 'SE';
	const TYPE_BGM = 'BGM';
	const TYPE_SUB = 'SUB';

	public $type;
	public $index;
	public $values;

	public function __construct($type = null, $index = null, $values = null) {
		$this->type = $type;
		$this->index = $index;
		$this->values = ($values !== null) ? $values : [];
	}

	public function add($track, $value) {
		if ($track === null) {
			$track = 'TR0';
		}

		$tr = (array_key_exists($track, $this->values)) ? $this->values[$track] : [];
		$tr[] = $value;
		$this->values[$track] = $tr;
	}

	public function getKey() {
		$key = strtoupper($this->type);
		if ($key === 'S') {
			$key = MmlMusic::TYPE_SUB;
		}
		return $key.$this->index;
	}
}

class MmlDefineWriter {
	private $define;

	public function __construct($define) {
		$this->define = $define;
	}

	public function set($value) {
		$this->define->value = $value;
	}

	public function push($container) {
		$key = $this->define->getKey();
		$container->define[$key] = $this->define;
	}
}

class MmlEnvelopeWriter {
	private $envelope;

	public function __construct($envelope) {
		$this->envelope = $envelope;
	}

	public function setIndex($index) {
		$this->envelope->index = $index;
	}

	public function add($value) {
		$this->envelope->add($value);
	}

	public function push($container) {
		$key = $this->envelope->getKey();
		$container->envelope[$key] = $this->envelope;
	}
}

class MmlMusicWriter {
	public $music;
	public $track;

	public function __construct($music) {
		$this->music = $music;
	}

	public function setIndex($index) {
		$this->music->index = $index;
	}

	public function add($value) {
		if (preg_match('/^TR/is', $value)) {
			$this->track = $value;
		} else {
			$this->music->add($this->track, $value);
		}
	}

	public function push($container) {
		$key = $this->music->getKey();
		$container->music[$key] = $this->music;
	}
}

class MmlContainerSyntaxErrorException extends Exception {
	public function __construct(){}
}

class MmlContainerLexStatus {
	const NOTHING       = 0;
	const COMMENT_BLOCK = 1; // /**/
	const COMMENT_LINE  = 2; // //
	const SINGLE_QUOTE  = 3; // ''
	const DOUBLE_QUOTE  = 4; // ""
	const PARENTHESES   = 5; // ()
	const CURLY_BRACKET = 6; // {}
	const VALUE         = 7; // #name, value, name, index, values

	public $status;
	public $terminater;
	public $value;

	public function __construct($status = MmlContainerLexStatus::NOTHING,
								$terminater = null,
								$value = '') {
		$this->status = $status;
		$this->terminater = $terminater;
		$this->value = $value;
	}
}

class MmlContainerParser {
	const STATUS_READY  = 0;
	const STATUS_READ   = 1;
	const STATUS_DONE   = 2;
	const STATUS_ERROR  =-1;

	const NOTHING       = 0;

	const LEX_TYPE_VALUE         = 0x01;
	const LEX_TYPE_ESCAPE        = 0x02;
	const LEX_TYPE_SINGLE_QUOTE  = 0x03;
	const LEX_TYPE_DOUBLE_QUOTE  = 0x04;
	const LEX_TYPE_PARENTHESES   = 0x05;
	const LEX_TYPE_CURLY_BRACKET = 0x06;
	const LEX_TYPE_DEFINE        = 0x07;
	const LEX_TYPE_COMMENT_BLOCK = 0x08;
	const LEX_TYPE_COMMENT_LINE  = 0x09;
	const LEX_TYPE_SPACE         = 0x0a;
	const LEX_TYPE_TAB           = 0x0b;
	const LEX_TYPE_COMMA         = 0x0c;

	const LEX_ATTR_QUOTE         = 0x01;
	const LEX_ATTR_BLOCK         = 0x02;
	const LEX_ATTR_COMMENT       = 0x04;
	const LEX_ATTR_SEPARATOR     = 0x08;
	const LEX_ATTR_START         = 0x10;
	const LEX_ATTR_END           = 0x20;
	const LEX_ATTR_EOL           = 0x80;

	const PARSE_TYPE_DEFINE         = 1;	// #name value
	const PARSE_TYPE_ENVELOPE       = 2;	// E[nvelope] (index) {values}
	const PARSE_TYPE_MUSIC          = 3;	// name (index) {values}

	const PARSE_PARAM_NAME          = 0;
	const PARSE_PARAM_PAREN_START   = 1;
	const PARSE_PARAM_INDEX         = 2;
	const PARSE_PARAM_PAREN_END     = 3;
	const PARSE_PARAM_BRACKET_START = 4;
	const PARSE_PARAM_VALUE         = 5;
	const PARSE_PARAM_BRACKET_END   = 6;

	public $status;
	public $message;

	public $line;
	public $row;
	public $column;

	private $blockInfos;
	private $lexStatusStack;

	private $nameInfos;
	private $parseType;
	private $parseParam;
	private $parseValue;

	private $container;

	public function __construct($container) {
		$this->container = $container;

		$this->blockInfos = [
			"'" => new MmlContainerLexStatus(MmlContainerLexStatus::SINGLE_QUOTE , "'"),
			'"' => new MmlContainerLexStatus(MmlContainerLexStatus::DOUBLE_QUOTE , '"'),
			'(' => new MmlContainerLexStatus(MmlContainerLexStatus::PARENTHESES  , ')'),
			'{' => new MmlContainerLexStatus(MmlContainerLexStatus::CURLY_BRACKET, '}'),

			'*' => new MmlContainerLexStatus(MmlContainerLexStatus::COMMENT_BLOCK),
			'/' => new MmlContainerLexStatus(MmlContainerLexStatus::COMMENT_LINE ),
			];

		$this->nameInfos = [
			'/^#/' => MmlContainerParser::PARSE_TYPE_DEFINE,
			'/^(E|Envelope)$/is' => MmlContainerParser::PARSE_TYPE_ENVELOPE,
			'/^(SE|BGM|S|SUB)$/is' => MmlContainerParser::PARSE_TYPE_MUSIC,
			];

		$this->clear();
	}

	public function clear() {
		$this->status = MmlContainerParser::STATUS_READY;
		$this->message = '';

		$this->line = '';
		$this->row = 0;
		$this->column = 0;

		$this->lexStatusStack = [new MmlContainerLexStatus(), new MmlContainerLexStatus()];
		$this->clearParseValue();
	}

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
			&& $this->parseType === MmlContainerParser::NOTHING) {
			$this->status = MmlContainerParser::STATUS_DONE;
		}
	}

	private function pushLexStatus($lex) {
		array_push($this->lexStatusStack, $lex);
	}

	private function popLexStatus() {
		$lex = array_pop($this->lexStatusStack);
		return $lex;
	}

	private function getLexStatus() {
		$count = count($this->lexStatusStack);
		$lex = $this->lexStatusStack[$count - 1];
		return $lex;
	}

	private function analyzeChar($ch0, $ch1, &$lexType, &$lexAttr) {
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
		case '/':
			switch ($ch1) {
			case '/':
				$lexType = MmlContainerParser::LEX_TYPE_COMMENT_LINE;
				$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_START;
				break;
			case '*':
				$lexType = MmlContainerParser::LEX_TYPE_COMMENT_BLOCK;
				$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_START;
				break;
			}
			break;
		case '*':
			switch ($ch1) {
			case '/':
				$lexType = MmlContainerParser::LEX_TYPE_COMMENT_BLOCK;
				$lexAttr = MmlContainerParser::LEX_ATTR_COMMENT | MmlContainerParser::LEX_ATTR_END;
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

	private function analyzeStatusValue($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		$quoteEnabled = false;
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

		case MmlContainerLexStatus::PARENTHESES:
		case MmlContainerLexStatus::CURLY_BRACKET:
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

	private function analyzeStatusCommentLine($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		// Check block end.
		if ($lexAttr & MmlContainerParser::LEX_ATTR_EOL) {
			$lex = $this->popLexStatus();
		}
		$ch0 = null;
	}

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

	private function clearParseValue() {
		$this->parseType = MmlContainerParser::NOTHING;
		$this->parseParam = MmlContainerParser::PARSE_PARAM_NAME;
		$this->parseValue = null;
	}

	private function parseTypeName($lex) {
		$this->parseType = $this->findParseType($lex->value);
		switch ($this->parseType) {
		case MmlContainerParser::PARSE_TYPE_DEFINE:
			if (mb_strlen($lex->value) <= 1) {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found define name.';
				throw new MmlContainerSyntaxErrorException();
			}
			$this->parseParam = MmlContainerParser::PARSE_PARAM_VALUE;
			$this->parseValue = new MmlDefineWriter(new MmlDefine(mb_substr($lex->value, 1)));
			break;
		case MmlContainerParser::PARSE_TYPE_ENVELOPE:
			$this->parseParam = MmlContainerParser::PARSE_PARAM_PAREN_START;
			$this->parseValue = new MmlEnvelopeWriter(new MmlEnvelope());
			break;
		case MmlContainerParser::PARSE_TYPE_MUSIC:
			$this->parseParam = MmlContainerParser::PARSE_PARAM_PAREN_START;
			$this->parseValue = new MmlMusicWriter(new MmlMusic($lex->value));
			break;
		default:
			$this->status = MmlContainerParser::STATUS_ERROR;
			$this->message = 'Illegal type name. ('.$lex->value.')';
			throw new MmlContainerSyntaxErrorException();
		}
	}

	private function parseTypeDefine($lex) {
		$this->parseValue->set($lex->value);
		$this->parseValue->push($container);
		$this->clearParseValue();
	}

	private function parseTypeValue($lex) {
		switch ($this->parseParam) {
		case MmlContainerParser::PARSE_PARAM_PAREN_START:
			if ($lex->value !== '(') {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found parentheses. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			++$this->parseParam;
			break;

		case MmlContainerParser::PARSE_PARAM_INDEX:
			if (!ctype_digit($lex->value)){
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Illegal index parameter. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			$this->parseValue->setIndex(intval($lex->value));
			++$this->parseParam;
			break;

		case MmlContainerParser::PARSE_PARAM_PAREN_END:
			if ($lex->value !== ')') {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found parentheses. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			++$this->parseParam;
			break;

		case MmlContainerParser::PARSE_PARAM_BRACKET_START:
			if ($lex->value !== '{') {
				$this->status = MmlContainerParser::STATUS_ERROR;
				$this->message = 'Not found curly bracket. ('.$lex->value.')';
				throw new MmlContainerSyntaxErrorException();
			}
			++$this->parseParam;
			break;

		case MmlContainerParser::PARSE_PARAM_VALUE:
		case MmlContainerParser::PARSE_PARAM_BRACKET_END:
			if ($lex->value === '}') {
				$this->parseValue->push($this->container);
				$this->clearParseValue();
			} else {
				$this->parseValue->add($lex->value);
			}
			break;
		}
	}

	private function parseValue($lex) {
		//echo 'parseValue: status='.$lex->status.', value='.$lex->value."\n";
		switch ($this->parseType) {
		case MmlContainerParser::PARSE_TYPE_DEFINE:
			$this->parseTypeDefine($lex);
			break;
		case MmlContainerParser::PARSE_TYPE_ENVELOPE:
		case MmlContainerParser::PARSE_TYPE_MUSIC:
			$this->parseTypeValue($lex);
			break;
		default:
			$this->parseTypeName($lex);
			break;
		}
	}
}
