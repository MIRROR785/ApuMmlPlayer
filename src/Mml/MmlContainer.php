<?php
/**
 * MmlContainer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Mml;

use MIRROR785\ApuMmlPlayer\Container\Json;

/**
 * MMLデータを格納するクラス
 */
class MmlContainer
{
    /** @var string 曲名 */
    public $title;

    /** @var string 作曲者 */
    public $composer;

    /** @var string 編曲者 */
    public $arranger;

    /** @var int[] トラック番号配列 */
    public $trackNumbers;

    /** @var array トラック情報配列 */
    public $tracks;

    /**
     * コンストラクタ
     * @param string|array $args 設定情報
     *
     * string (JSON)の場合：
     * {
     *   "Title":    "曲名",   //（省略可）
     *   "Composer": "作曲者", //（省略可）
     *   "Arranger": "編曲者", //（省略可）
     *   "Tracks": {
     *       "トラック番号"  // 0:global, 1:pulse1, 2:pulse2, 3:triangle, 4:noise
     *        : "MML", ...
     *    }
     * }
     *
     * arrayの場合：
     * [
     *   'Title'    => '曲名',   //（省略可）
     *   'Composer' => '作曲者', //（省略可）
     *   'Arranger' => '編曲者', //（省略可）
     *   'Tracks'   => [
     *       トラック番号  // 0:global, 1:pulse1, 2:pulse2, 3:triangle, 4:noise
     *        => 'MML', ...
     *    ]
     * ]
     */
    public function __construct($args = null) {
        $this->title = '';
        $this->composer = '';
        $this->arranger = '';
        $this->trackNumbers = [];
        $this->tracks = [];

        if ($args === null) return;

        $values = is_array($args) ? $args : Json::ToArray($args);

        foreach ($values as $key => $value) {
            switch ($key) {
            case 'Title':
                // 曲名
                $this->title = $value;
                break;

            case 'Composer':
                // 作曲名
                $this->composer = $value;
                break;

            case 'Arranger':
                // 編曲名
                $this->arranger = $value;
                break;

            case 'Tracks':
                // トラック
                $this->trackNumbers = array_keys($value);
                foreach ($this->trackNumbers as $tr) {
                    $mml = preg_replace("/\r*\n|\r|\t| +/", "", $value[$tr]) . "\n";
                    $this->tracks[$tr] = preg_split("//u", $mml, -1, PREG_SPLIT_NO_EMPTY); // utf8
                }
                break;
            }
        }
    }

    /**
     * MMLテキストの解析
     * @param テキスト
     * @return コンテナ情報
     */
    public static function parse($text) {
        $container = new MmlContainer();
        $text = preg_replace("/\/\*.*\*\//", "", $text);
        $lines = preg_split("/\r*\n|\r/", $text, -1, PREG_SPLIT_NO_EMPTY);
        $trackNo = 0;
        $tracks = [0=>'', 1=>'', 2=>'', 3=>'', 4=>''];

        foreach ($lines as $line) {
            $l = ltrim(preg_replace("/\/\/.*/", "", $line));
            $l = preg_replace("/[ \t]+/", " ", $l);
            $c = strpos($l, ' ');

            if ($c === false) {
                $tracks[$trackNo] .= $line;

            } else {
                $key = substr($l, 0, $c);
                $value = substr($l, $c + 1);

                switch ($key) {
                case '#Title':
                    // 曲名
                    $container->title = $value;
                    break;

                case '#Composer':
                    // 作曲名
                    $container->composer = $value;
                    break;

                case '#Arranger':
                    // 編曲名
                    $container->arranger = $value;
                    break;

                default:
                    $tr = strtoupper($key);
                    switch ($tr) {
                    case 'TR0':
                        $trackNo = 0;
                        break;
                    case 'TR1':
                        $trackNo = 1;
                        break;
                    case 'TR2':
                        $trackNo = 2;
                        break;
                    case 'TR3':
                        $trackNo = 3;
                        break;
                    case 'TR4':
                        $trackNo = 4;
                        break;
                    default:
                        $value = $l;
                        break;
                    }

                    $tracks[$trackNo] .= $value;
                    break;
                }
            }
        }

        for ($trackNo = 0; $trackNo <= 4; ++$trackNo) {
            $mml = $tracks[$trackNo];
            if ($mml !== '') {
                $mml = preg_replace("/\r*\n|\r|\t| +/", "", $mml) . "\n";
                $container->tracks[$trackNo] = preg_split("//u", $mml, -1, PREG_SPLIT_NO_EMPTY);
                if ($trackNo > 0) {
                    $container->trackNumbers[] = $trackNo;
                }
            }
        }

        return $container;
    }
}
