<?php
/**
 * AudioMixer.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * オーディオデータのミキシングインターフェース
 */
interface AudioMixer {
	/**
	 * ミキシング
	 * @oaram int[] $trackNumbers トラック番号配列
	 * @param int=>double[] $positions 定位配列
	 * @param double[] $values トラック毎のサンプリング情報
	 * @return double|double[] モノラルもしくはステレオミキシング結果
	 */
	function mixing($trackNumbers, $positions, $values);
}

/**
 * モノラルオーディオミキサークラス
 */
class MonauralMixer implements AudioMixer {
	/**
	 * ミキシング
	 * @oaram int[] $trackNumbers トラック番号配列
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

			// Scale : -1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0
			//      =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5 ~ 1.75~ 2.0
			//                                       ______.......------+++
			//         ______.......------++++++*****
			$s = AudioUtil::getValue($p[1] + 1.0, 0, 2);

			$value += $v * $s;
		}

		return $value;
	}
}

/**
 * ステレオオーディオミキサークラス
 */
class StereoMixer implements AudioMixer {
	/**
	 * ミキシング
	 * @oaram int[] $trackNumbers トラック番号配列
	 * @param int=>double[] $positions 定位配列
	 * @param double[] $values トラック毎のサンプリング情報
	 * @return double[] ステレオミキシング結果
	 */
	public function mixing($trackNumbers, $positions, $values) {
		$count = count($trackNumbers);
		$l = 0;
		$r = 0;

		for ($i = 0; $i < $count; ++$i) {
			$tr = $trackNumbers[$i];
			$p = $positions[$tr];

			// Pan(left) : -1.5 ~-1.25~-1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5
			//          =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 0.75~ 0.5 ~ 0.25~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0
			//             ______.......------++++++*****+++++------......_____________________________
			// n + 0.5     -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0   1.25  1.5   1.75  2.0
			// abs(n)       1.0   0.75  0.5   0.25  0.0   0.25  0.5   0.75  1.0   1.25  1.5   1.75  2.0
			// 1.0 - n      0.0   0.25  0.5   0.75  1.0   0.75  0.5   0.25  0.0  -0.25 -0.5  -0.75 -1.0
			$pl = AudioUtil::getValue(1.0 - abs($p[0] + 0.5), 0, 1);

			// Pan(right): -1.5 ~-1.25~-1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5
			//          =>  0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 0.75~ 0.5 ~ 0.25~ 0.0
			//             ______________________________.......------++++++*****+++++------......_____
			// n - 0.5     -2.0  -1.75 -1.5  -1.25 -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0
			// abs(n)       2.0   1.75  1.5   1.25  1.0   0.75  0.5   0.25  0.0   0.25  0.5   0.75  1.0
			// 1.0 - n     -1.0  -0.75 -0.5  -0.25  0.0   0.25  0.5   0.75  1.0   0.25  0.5   0.25  0.0
			$pr = AudioUtil::getValue(1.0 - abs($p[0] - 0.5), 0, 1);

			// Scale : -1.0 ~-0.75~-0.5 ~-0.25~ 0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0
			//      =>  0.0 ~ 0.25~ 0.5 ~ 0.75~ 1.0 ~ 1.25~ 1.5 ~ 1.75~ 2.0
			//                                       ______.......------+++
			//         ______.......------++++++*****
			$s = AudioUtil::getValue($p[1] + 1.0, 0, 2);

			$v = $values[$i] * $s;
			$l += $v * $pl;
			$r += $v * $pr;
		}

		return [$l, $r];
	}
}
