<?php
/**
 * MmlContainer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * MML�f�[�^���i�[����N���X
 */
class MmlContainer {
	/** @var MmlDefine[] ��`�l�z�� */
	private $define;

	/** @var MmlEnvelope[] �G���x���[�v�z�� */
	private $envelope;

	/** @var MmlMusic[] �ȏ��z�� */
	private $music;

	/**
	 * �R���X�g���N�^
	 */
	public function __construct() {
		$this->define = [];
		$this->envelope = [];
		$this->music = [];
	}

	/**
	 * ��`�̗L���𔻒�
	 * @param string $name ��`��
	 * @param bool ���茋��
	 */
	public function isDefine($name) {
		$tmp = new MmlDefine($name);
		return array_key_exists($this->define, $tmp->getKey());
	}

	/**
	 * �G���x���[�v�̗L���𔻒�
	 * @param int $index �C���f�b�N�X
	 * @return bool ���茋��
	 */
	public function hasEnvelope($index) {
		$tmp = new MmlEnvelope($index);
		return array_key_exists($this->envelope, $tmp->getKey());
	}

	/**
	 * �ȏ��̗L���𔻒�
	 * @param MmlMusic::TYPE_SE|TYPE_BGM|TYPE_SUB $type �ȏ����
	 * @param int $index �C���f�b�N�X
	 * @return ���茋��
	 */
	public function hasMusic($type, $index) {
		$tmp = new MmlMusic($type, $index);;
		return array_key_exists($this->music, $tmp->getKey());
	}

	/**
	 * ��`��ݒ�
	 * @param MmlDefine $define ��`���
	 */
	public function setDefine($define) {
		$this->define[$define->getKey()] = $define;
	}

	/**
	 * �G���x���[�v����ݒ�
	 * @param MmlEnvelope $envelope �G���x���[�v���
	 */
	public function setEnvelope($envelope) {
		$this->envelope[$envelope->getKey()] = $envelope;
	}

	/**
	 * �ȏ���ݒ�
	 * @param MmlMusic $music �ȏ��
	 */
	public function setMusic($music) {
		$this->music[$music->getKey()] = $music;
	}

	/**
	 * ��`�����擾
	 * @param string $name ��`��
	 * @return MmlDefine|null ��`���
	 */
	public function getDefine($name) {
		$tmp = new MmlDefine($name);
		$key = $tmp->getKey();
		return array_key_exists($this->define, $key) ?
			$this->define[$key] : null;
	}

	/**
	 * �G���x���[�v�����擾
	 * @param int $index �C���f�b�N�X
	 * @return MmlEnvelope|null �G���x���[�v���
	 */
	public function getDevelope($index) {
		$tmp = new MmlEnvelope($index);
		$key = $tmp->getKey();
		return array_key_exists($this->envelope, $key) ?
			$this->envelope[$key] : null;
	}

	/**
	 * �ȏ����擾
	 * @param MmlMusic::TYPE_SE|TYPE_BGM|TYPE_SUB $type �ȏ����
	 * @param int $index �C���f�b�N�X
	 * @return MmlMusic|null �ȏ��
	 */
	public function getMusic($type, $index) {
		$tmp = new MmlMusic($type, $index);
		$key = $tmp->getKey();
		return array_key_exists($this->music, $key) ?
			$this->music[$key] : null;
	}

	/**
	 * �Ȑ����擾
	 * @param MmlMusic::TYPE_SE|TYPE_BGM|TYPE_SUB $type �ȏ����
	 * @return int �Ȑ�
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
 * ��`���N���X
 */
class MmlDefine {
	/** @var string ��`�� */
	public $name;

	/** @var string|int ��`�l */
	public $value;

	/**
	 * �R���X�g���N�^
	 * @param string $name ��`��
	 * @param string|int $value ��`�l
	 */
	public function __construct($name = null, $value = null) {
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * �����L�[���擾
	 * @return string �����L�[
	 */
	public function getKey() {
		return strtoupper($this->name);
	}
}

/**
 * �G���x���[�v���N���X
 */
class MmlEnvelope {
	/** @var int �C���f�b�N�X */
	public $index;

	/** @var string[] ��`�l�z�� */
	public $values;

	/**
	 * �R���X�g���N�^
	 * @param int $index �C���f�b�N�X
	 * @param string[] ��`�l�z��
	 */
	public function __construct($index = null, $values = null) {
		$this->index = $index;
		$this->values = ($values !== null) ? $values : [];
	}

	/**
	 * ��`�l�ǉ�
	 * @param string $value ��`�l
	 */
	public function add($value) {
		$this->values[] = $value;
	}

	/**
	 * �����L�[���擾
	 * @return int �����L�[
	 */
	public function getKey() {
		return $this->index;
	}
}

/**
 * �ȏ��N���X
 */
class MmlMusic {
	/** @var string �Ȏ��SE */
	const TYPE_SE = 'SE';

	/** @var string �Ȏ��BGM */
	const TYPE_BGM = 'BGM';

	/** @var string �Ȏ��SUB */
	const TYPE_SUB = 'SUB';

	/**
	 * ��ʔ���
	 * @param string $type ��ʒl
	 * @return bool ���茋��
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

	/** @var string �Ȏ�� */
	public $type;

	/** @var int �C���f�b�N�X */
	public $index;

	/** @var MmlDefine[] ��`���z�� */
	public $define;

	/** @var string=>string[] �g���b�N���z�� */
	public $values;

	/**
	 * �R���X�g���N�^
	 * @param string $type �Ȏ��
	 * @param int $index �C���f�b�N�X
	 * @param MmlDefine[] $define ��`���z��
	 * @param string=>string[] �g���b�N���z��
	 */
	public function __construct($type = null, $index = null, $define = null, $values = null) {
		$this->type = $type;
		$this->index = $index;
		$this->define = ($define !=null) ? $define : [];
		$this->values = ($values !== null) ? $values : [];
	}

	/**
	 * �g���b�N���̒ǉ�
	 * @param string $track �g���b�N��
	 * @param string $value �g���b�N���
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
	 * �����L�[���擾
	 * @return string �����L�[
	 */
	public function getKey() {
		$key = strtoupper($this->type);
		if ($key === 'S') {
			$key = MmlMusic::TYPE_SUB;
		}
		return $key.$this->index;
	}

	/**
	 * ��`����
	 * @param string $name ��`��
	 * @return bool ���茋��
	 */
	public function isDefine($name) {
		$tmp = new MmlDefine($name);
		return array_key_exists($this->define, $tmp->getKey());
	}

	/**
	 * ��`���ݒ�
	 * @param MmlDefine $define ��`���
	 */
	public function setDefine($define) {
		$this->define[$define->getKey()] = $define;
	}

	/**
	 * ��`���擾
	 * @param string $name ��`��
	 * @return MmlDefine ��`���
	 */
	public function getDefine($name) {
		$tmp = new MmlDefine($name);
		$key = $tmp->getKey();
		return array_key_exists($this->define, $key) ?
			$this->define[$key] : null;
	}
}

/**
 * ��`��񏑂����݃N���X
 */
class MmlDefineWriter {
	/** @var MmlDefine ��`��� */
	private $define;

	/**
	 * �R���X�g���N�^
	 * @param MmlDefine $define ��`���
	 */
	public function __construct($define) {
		$this->define = $define;
	}

	/**
	 * ��`�l�ݒ�
	 * @param string|int $value ��`�l
	 */
	public function set($value) {
		$this->define->value = $value;
	}

	/**
	 * ��`�����R���e�i�֓o�^
	 * @param MmlContainer|MmlMusic $container �R���e�i
	 */
	public function push($container) {
		$container->setDefine($this->define);
	}

	/**
	 * �R���e�i���擾
	 * @return null �R���e�i
	 */
	public function getContainer() {
		return null;
	}
}

/**
 * �G���x���[�v��񏑂����݃N���X
 */
class MmlEnvelopeWriter {
	/** @var MmlEnvelope �G���x���[�v��� */
	private $envelope;

	/**
	 * �R���X�g���N�^
	 * @param MmlEnvelope �G���x���[�v���
	 */
	public function __construct($envelope) {
		$this->envelope = $envelope;
	}

	/**
	 * �C���f�b�N�X�̐ݒ�
	 * @param int $index �C���f�b�N�X
	 */
	public function setIndex($index) {
		$this->envelope->index = $index;
	}

	/**
	 * �G���x���[�v�l�̒ǉ�
	 * @param string|int $value �G���x���[�v�l
	 */
	public function add($value) {
		$this->envelope->add($value);
	}

	/**
	 * �G���x���[�v�����R���e�i�֓o�^
	 * @param MmlContainer $container �R���e�i
	 */
	public function push($container) {
		$container->setEnvelope($this->envelope);
	}

	/**
	 * �R���e�i���擾
	 * @return null �R���e�i
	 */
	public function getContainer() {
		return null;
	}
}

/**
 * �ȏ�񏑂����݃N���X
 */
class MmlMusicWriter {
	/** @var MmlMusic �ȏ�� */
	public $music;

	/** @var string �g���b�N�� */
	public $track;

	/**
	 * �R���X�g���N�^
	 * @param MmlMusic �ȏ��
	 */
	public function __construct($music) {
		$this->music = $music;
	}

	/**
	 * �C���f�b�N�X�̐ݒ�
	 * @param int $index �C���f�b�N�X
	 */
	public function setIndex($index) {
		$this->music->index = $index;
	}

	/**
	 * �Ȓl�̒ǉ�
	 * @param string|int $value �Ȓl
	 */
	public function add($value) {
		if (preg_match('/^TR/is', $value)) {
			$this->track = $value;
		} else {
			$this->music->add($this->track, $value);
		}
	}

	/**
	 * �ȏ����R���e�i�֓o�^
	 * @param MmlContainer $container �R���e�i
	 */
	public function push($container) {
		$container->setMusic($this->music);
	}

	/**
	 * �R���e�i���擾
	 * @return MmlMusic �R���e�i
	 */
	public function getContainer() {
		return $this->music;
	}
}

/**
 * MML�R���e�i�̃V���^�b�N�X�G���[��O�N���X
 */
class MmlContainerSyntaxErrorException extends Exception {
	public function __construct(){}
}

/**
 * MML�R���e�i�̎����͏�ԃN���X
 */
class MmlContainerLexStatus {
	/** @var �����ԁF�l�Ȃ� */
	const NOTHING       = 0;

	/** @var �����ԁF�R�����g�u���b�N */
	const COMMENT_BLOCK = 1; // /**/

	/** @var �����ԁF�R�����g���C�� */
	const COMMENT_LINE  = 2; // //

	/** @var �����ԁF�V���O���N�H�[�g */
	const SINGLE_QUOTE  = 3; // ''

	/** @var �����ԁF�_�u���N�H�[�g */
	const DOUBLE_QUOTE  = 4; // ""

	/** @var �����ԁF�ۊ��� */
	const PARENTHESES   = 5; // ()

	/** @var �����ԁF�g���� */
	const CURLY_BRACKET = 6; // {}

	/** @var �����ԁF�l */
	const VALUE         = 7; // #name, value, name, index, values

	/** @var ������ */
	public $status;

	/** @var �I�[���� */
	public $terminater;

	/** @var ��͒l */
	public $value;

	/**
	 * �R���X�g���N�^
	 * @param int $status ������
	 * @param char $terminater �I�[����
	 * @param string $value ��͒l
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
 * MML�R���e�i�̍\����͏��N���X
 */
class MmlContainerParserInfo {
	/** @var int ��͎�� */
	public $type;

	/** @var int �p�����[�^��� */
	public $param;

	/** @var ��͒l */
	public $value;

	/**
	 * �R���X�g���N�^
	 * @param int $type ��͎��
	 * @param int $param �p�����[�^���
	 * @param string $value ��͒l
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
 * MML�R���e�i�̍\����̓N���X
 */
class MmlContainerParser {
	/** @var int �\����͏�ԁF������� */
	const STATUS_READY  = 0;

	/** @var int �\����͏�ԁF�ǂݍ��ݒ� */
	const STATUS_READ   = 1;

	/** @var int �\����͏�ԁF�ǂݍ��݊��� */
	const STATUS_DONE   = 2;

	/**  @var int �\����͏�ԁF�G���[���� */
	const STATUS_ERROR  =-1;


	/** @var int �l�Ȃ� */
	const NOTHING       = 0;


	/** @var int �����͎�ʁF�l */
	const LEX_TYPE_VALUE         = 0x01;

	/** @var int �����͎�ʁF�G�X�P�[�v */
	const LEX_TYPE_ESCAPE        = 0x02;

	/** @var int �����͎�ʁF�V���O���N�H�[�g */
	const LEX_TYPE_SINGLE_QUOTE  = 0x03;

	/** @var int �����͎�ʁF�_�u���E�H�[�g */
	const LEX_TYPE_DOUBLE_QUOTE  = 0x04;

	/** @var int �����͎�ʁF�ۊ��� */
	const LEX_TYPE_PARENTHESES   = 0x05;

	/** @var int �����͎�ʁF�g���� */
	const LEX_TYPE_CURLY_BRACKET = 0x06;

	/** @var int �����͎�ʁF��` */
	const LEX_TYPE_DEFINE        = 0x07;

	/** @var int �����͎�ʁF�R�����g�u���b�N */
	const LEX_TYPE_COMMENT_BLOCK = 0x08;

	/** @var int �����͎�ʁF�R�����g���C�� */
	const LEX_TYPE_COMMENT_LINE  = 0x09;

	/** @var int �����͎�ʁF�X�y�[�X */
	const LEX_TYPE_SPACE         = 0x0a;

	/** @var int �����͎�ʁF�^�u */
	const LEX_TYPE_TAB           = 0x0b;

	/** @var int �����͎�ʁF�J���} */
	const LEX_TYPE_COMMA         = 0x0c;


	/** @var int �����͑����F�N�H�[�g */
	const LEX_ATTR_QUOTE         = 0x01;

	/** @var int �����͑����F�u���b�N */
	const LEX_ATTR_BLOCK         = 0x02;

	/** @var int �����͑����F�R�����g */
	const LEX_ATTR_COMMENT       = 0x04;

	/** @var int �����͑����F��؂� */
	const LEX_ATTR_SEPARATOR     = 0x08;

	/** @var int �����͑����F�J�n */
	const LEX_ATTR_START         = 0x10;

	/** @var int �����͑����F�I�� */
	const LEX_ATTR_END           = 0x20;

	/** @var int �����͑����F�A������ */
	const LEX_ATTR_DOUBLE_CHAR   = 0x40;

	/** @var int �����͑����F�s�� */
	const LEX_ATTR_EOL           = 0x80;


	/** @var int ��͎�ʁF��` */
	const PARSE_TYPE_DEFINE         = 1;	// #name value

	/** @var int ��͎�ʁF�G���x���[�v */
	const PARSE_TYPE_ENVELOPE       = 2;	// E[nvelope] (index) {values}

	/** @var int ��͎�ʁF�ȏ�� */
	const PARSE_TYPE_MUSIC          = 3;	// name (index) {values}


	/** @var int �p�����[�^��ʁF���� */
	const PARSE_PARAM_NAME          = 0;

	/** @var int �p�����[�^��ʁF�ۊ��ʊJ�n */
	const PARSE_PARAM_PAREN_START   = 1;

	/** @var int �p�����[�^��ʁF�C���f�b�N�X */
	const PARSE_PARAM_INDEX         = 2;

	/** @var int �p�����[�^��ʁF�ۊ��ʏI�� */
	const PARSE_PARAM_PAREN_END     = 3;

	/** @var int �p�����[�^��ʁF�g���ʊJ�n */
	const PARSE_PARAM_BRACKET_START = 4;

	/** @var int �p�����[�^��ʁF�l */
	const PARSE_PARAM_VALUE         = 5;

	/** @var int �p�����[�^��ʁF�g���ʏI�� */
	const PARSE_PARAM_BRACKET_END   = 6;


	/** @var int ��͏�� */
	public $status;

	/** @var string ��̓��b�Z�[�W */
	public $message;

	/** @var string ��͍s */
	public $line;

	/** @var int ��͍s�ԍ� */
	public $row;

	/** @var int ��͗�ԍ� */
	public $column;

	/** @var char=>MmlContainerLexStatus �u���b�N���z�� */
	private $blockInfos;

	/** @var MmlContainerLexStatus[] �����͏�ԃX�^�b�N */
	private $lexStatusStack;

	/** @var regex=>MmlContainerParse::PARSE_TYPE_* ���ʎq���̔z�� */
	private $nameInfos;

	/** @var MmlContainerParserInfo[] �\����͏��X�^�b�N */
	private $parseInfoStack;

	/** @var MmlContainerParserInfo �\����͏�� */
	private $parseInfo;

	/** @var MmlContainer MML�R���e�i */
	private $container;

	/**
	 * �R���X�g���N�^
	 * @param MmlContainer $container MML�R���e�i
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
	 * ��͏�Ԃ��N���A
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
	 * MML�̉��
	 * @throw MmlContainerSyntaxErrorException ��̓G���[������
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
	 * �����͏�Ԃ��X�^�b�N�֊i�[
	 * @param MmlContainerLexStatus $lex �����͏��
	 */
	private function pushLexStatus($lex) {
		array_push($this->lexStatusStack, $lex);
	}

	/**
	 * �����͏�Ԃ��X�^�b�N����擾
	 * @return MmlContainerLexStatus �����͏��
	 */
	private function popLexStatus() {
		$lex = array_pop($this->lexStatusStack);
		return $lex;
	}

	/**
	 * ���O�̎����͏�Ԃ��Q��
	 * @return MmlContainerLexStatus �����͏��
	 */
	private function getLexStatus() {
		$count = count($this->lexStatusStack);
		$lex = $this->lexStatusStack[$count - 1];
		return $lex;
	}

	/**
	 * �����̉��
	 * @param char &$ch0 ����͕���
	 * @param char &$ch1 ����͕���
	 * @param MmlContainerParser::LEX_TYPE_* &$lexType �����͎�ʊi�[��
	 * @param MmlContainerParser::LEX_ATTR_* &$lexAttr �����͑����i�[��
	 * @throw MmlContainerSyntaxErrorException ��̓G���[������
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
	 * ��ԂȂ����̎�����
	 * @param MmlContainerParser::LEX_TYPE_* $lexType �����͎��
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr �����͑���
	 * @param MmlContainerLexStatus &$lex �����͏��
	 * @param char &$ch0 ����͕���
	 * @param char &$ch1 ����͕���
	 * @throw MmlContainerSyntaxErrorException ��̓G���[������
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
	 * �l��͏�Ԏ��̎�����
	 * @param MmlContainerParser::LEX_TYPE_* $lexType �����͎��
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr �����͑���
	 * @param MmlContainerLexStatus &$lex �����͏��
	 * @param char &$ch0 ����͕���
	 * @param char &$ch1 ����͕���
	 * @throw MmlContainerSyntaxErrorException ��̓G���[������
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
	 * �R�����g�u���b�N��Ԏ��̎�����
	 * @param MmlContainerParser::LEX_TYPE_* $lexType �����͎��
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr �����͑���
	 * @param MmlContainerLexStatus &$lex �����͏��
	 * @param char &$ch0 ����͕���
	 * @param char &$ch1 ����͕���
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
	 * �R�����g���C����Ԏ��̎�����
	 * @param MmlContainerParser::LEX_TYPE_* $lexType �����͎��
	 * @param MmlContainerParser::LEX_ATTR_* $lexAttr �����͑���
	 * @param MmlContainerLexStatus &$lex �����͏��
	 * @param char &$ch0 ����͕���
	 * @param char &$ch1 ����͕���
	 */
	private function analyzeStatusCommentLine($lexType, $lexAttr, &$lex, &$ch0, &$ch1) {
		// Check block end.
		if ($lexAttr & MmlContainerParser::LEX_ATTR_EOL) {
			$lex = $this->popLexStatus();
		}
		$ch0 = null;
	}

	/**
	 * �\����͎�ʂ�����
	 * @param string �����l
	 * @return MmlContainerParser::PARSE_TYPE_* �\����͎��
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
	 * �\����͏����X�^�b�N�֊i�[
	 */
	private function pushParseInfo() {
		if ($this->parseInfo !== null) {
			array_push($this->parseInfoStack, $this->parseInfo);
		}
		$this->parseInfo = null;
	}

	/**
	 * �\����͏����X�^�b�N����擾
	 */
	private function popParseInfo() {
		if (count($this->parseInfoStack) > 0) {
			$this->parseInfo = array_pop($this->parseInfoStack);
		} else {
			$this->parseInfo = new MmlContainerParserInfo();;
		}
	}

	/**
	 * �w��I�t�Z�b�g�ʒu�̍\����͏����Q��
	 * @param int $offset �I�t�Z�b�g�ʒu
	 * @return �\����͏��
	 */
	private function getParseInfoStack($offset = 1) {
		$count = count($this->parseInfoStack);
		$index = $count - $offset;
		return (0 <= $index && $index < $count) ? $this->parseInfoStack[$index] : null;
	}

	/**
	 * �\����͒l�R���e�i���擾
	 * @return MmlContainer|MmlMusic �\����͒l�R���e�i
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
	 * �^���ʎq�̍\�����
	 * @param MmlContainerLexStatus $lex �����͏��
	 * @param MmlContainerParser::PARSE_TYPE_* $parseType �\����͎��
	 * @throw MmlContainerSyntaxErrorException ��̓G���[������
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
	 * ��`�^�̍\�����
	 * @param MmlContainerLexStatus $lex �����͏��
	 */
	private function parseTypeDefine($lex) {
		$this->parseInfo->value->set($lex->value);
		$this->parseInfo->value->push($this->getParseValuecontainer());
		$this->popParseInfo();
	}

	/**
	 * �l�^�̍\�����
	 * @param MmlContainerLexStatus $lex �����͏��
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
	 * ��͎�ʖ��̍\�����
	 * @param MmlContainerLexStatus $lex �����͏��
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
