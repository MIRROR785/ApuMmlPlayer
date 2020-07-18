<?php
/**
 * AudioMixer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Audio;

/**
 * オーディオデータのミキシングインターフェース
 */
interface AudioMixerInterface
{
    /**
     * ミキシング
     * @param int[] $trackNumbers トラック番号配列
     * @param int=>double[] $positions 定位配列
     * @param double[] $values トラック毎のサンプリング情報
     * @return double|double[] モノラルもしくはステレオミキシング結果
     */
    function mixing($trackNumbers, $positions, $values);
}

/**
 * モノラルミキシング制御クラス
 */
class MonauralMixer implements AudioMixerInterface
{
    /**
     * ミキシング
     * @param int[] $trackNumbers トラック番号配列
     * @param int=>double[] $positions 定位配列
     * @param double[] $values トラック毎のサンプリング情報
     * @return double モノラルミキシング結果
     */
    public function mixing($trackNumbers, $positions, $values) {
        $count = count($trackNumbers);
        $value = 0;

        for ($i = 0; $i < $count; ++$i) {
            $tr = $trackNumbers[$i];
            $p = $positions[$tr];
            $v = $values[$i];
            $s = $p[1];
            $value += $v * $s;
        }

        return $value;
    }
}

/**
 * ステレオミキシング制御クラス
 */
class StereoMixer implements AudioMixerInterface
{
    /**
     * ミキシング
     * @param int[] $trackNumbers トラック番号配列
     * @param int=>double[] $positions 定位配列
     * @param double[] $values トラック毎のサンプリング情報
     * @return double[] ステレオミキシング結果
     */
    public function mixing($trackNumbers, $positions, $values) {
        $count = count($trackNumbers);
        $l = 0;
        $r = 0;

//var_dump($positions);

        for ($i = 0; $i < $count; ++$i) {
            $tr = $trackNumbers[$i];
            $p = $positions[$tr];

            //var_dump($tr);
            //var_dump($p);

            // Pan(left) : -1.5 ~-1.25~-1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5
            //          =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 0.75~ 0.5 ~ 0.25~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0
            //             ______.......------++++++*****+++++------......_____________________________
            // n + 0.5     -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0   1.25  1.5   1.75  2.0
            // abs(n)       1.0   0.75  0.5   0.25  0.0   0.25  0.5   0.75  1.0   1.25  1.5   1.75  2.0
            // 1.0 - n      0.0   0.25  0.5   0.75  1.0   0.75  0.5   0.25  0.0  -0.25 -0.5  -0.75 -1.0
            $pl = AudioConst::getValue(1.0 - abs($p[0] + 0.5), -1.0, 1.0);

            // Pan(right): -1.5 ~-1.25~-1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5
            //          =>  0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 0.75~ 0.5 ~ 0.25~ 0.0
            //             ______________________________.......------++++++*****+++++------......_____
            // n - 0.5     -2.0  -1.75 -1.5  -1.25 -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0
            // abs(n)       2.0   1.75  1.5   1.25  1.0   0.75  0.5   0.25  0.0   0.25  0.5   0.75  1.0
            // 1.0 - n     -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0   0.25  0.5   0.25  0.0
            $pr = AudioConst::getValue(1.0 - abs($p[0] - 0.5), -1.0, 1.0);

//echo $pl.",".$pr."\n";

            $s = $p[1];
            $v = $values[$i];
            $l += $v * $pl * $s;
            $r += $v * $pr * $s;
        }

        return [$l, $r];
    }
}

/**
 * オーディオミキシング制御クラス
 */
class AudioMixer
{
    /**
     * インスタンスの生成
     * @param int $channelCount チャンネル数
     */
    public static function create($channelCount) {
        return ($channelCount == 1) ? new MonauralMixer() : new StereoMixer();
    }
}
