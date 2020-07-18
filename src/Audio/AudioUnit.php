<?php
/**
 * AudioUnit.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Audio;

/**
 * 擬似APUとトラックの紐付けを管理するクラス
 */
class AudioUnit
{
    /** @var string オーディオユニット名 */
    public $name;

    /** @var PseudoApu 擬似APU */
    public $apu;

    /** @var int サンプリングレート */
    public $sampleRate;

    /** @var int[] トラック番号配列 */
    public $trackNumbers;

    /** @var float[][] 定位情報配列 */
    public $positions;

    /** @var float[] 開始遅延時間配列 */
    public $lates;

    /** @var float[] 発音遅延時間配列 */
    public $delays;

    /**
     * コンストラクタ
     * @param int サンプリングレート
     * @param array デバイス詳細定義（トラック、定位・遅延パラメータ）
     */
    public function __construct($sampleRate, $params) {
        $this->name = null;
        $this->sampleRate = $sampleRate;
        $this->trackNumbers = [];
        $this->positions = [];
        $this->lates = [];
        $this->delays = [];

        foreach ($params as $key => $value) {
            switch ($key) {
            case 'Name':
                // ユニット名
                $this->name = $value;
                break;

            case 'Devices':
                // デバイス詳細定義
                $options = array_values($value);
                if ($options === $value) {
                    // デバイス詳細定義なしの場合はデフォルト値を設定
                    $this->trackNumbers = $options;
                    foreach ($this->trackNumbers as $tr) {
                        $this->positions[$tr] = [0.0, 1.0];
                        $this->delays[$tr] = 0.0;
                    }

                } else {
                    // デバイス詳細定義の取得
                    $this->trackNumbers = array_keys($value);
                    foreach ($this->trackNumbers as $tr) {
                        $options = $value[$tr];
                        if (array_key_exists('Position', $options)) {
                            $this->positions[$tr] = $options['Position'];
                        } else {
                            $this->positions[$tr] = [
                                array_key_exists('Pannning', $options) ? $options['Panning'] : 0.0,
                                array_key_exists('Scale', $options) ? $options['Scale'] : 1.0
                            ];
                        }
                        $this->lates[$tr] = array_key_exists('Late', $options) ? $options['Late'] : 0.0;
                        $this->delays[$tr] = array_key_exists('Delay', $options) ? $options['Delay'] : 0.0;
                    }
                }
                break;
            }
        }

        // 擬似APU設定
        $this->apu = new PseudoApu($this->sampleRate, $this->trackNumbers, $this->lates, $this->delays);
    }

    /**
     * 最大開始遅延時間を取得
     * @return 最大開始遅延時間
     */
    public function getMaxLate() {
        $result = 0;
        foreach ($this->lates as $late) {
            if ($result < $late) {
                $result = $late;
            }
        }
        return $result;
    }
}
