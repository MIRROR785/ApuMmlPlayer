<?php
/**
 * MmlContainer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Mml;

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

    /** @var int->char[] トラック情報配列 */
    public $tracks;

    /**
     * コンストラクタ
     * @param (key=>value)[] $args 設定情報
     * [
     *   'Title'    => '曲名',   // 省略可
     *   'Composer' => '作曲者', // 省略可
     *   'Arranger' => '編曲者', // 省略可
     *   'Tracks'   => [
     *       トラック番号  // 0:global, 1:pulse1, 2:pulse2, 3:triangle, 4:noise
     *        => MML
     *    ],
     * ]
     */
    public function __construct($args) {
        $this->title = '';
        $this->composer = '';
        $this->arranger = '';
        $this->trackNumbers = [];
        $this->tracks = [];

        foreach ($args as $key => $value) {
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
}
