<?php
/**
 * AudioPacker.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * オーディオデータのパッキングインターフェース
 */
interface AudioPacker {
	/**
	 * パッキング
	 * @param double $scale スケール値
	 * @param double[]|double[][] $values オーディオデータ
	 * @return unsigne char|unsigned short|unsigned int パッキングデータ
	 */
	function packing($scale, $values);
}

/**
 * モノラル8ビットパッカークラス
 */
class Monaural8bitPacker implements AudioPacker {
	/**
	 * パッキング
	 * @param double $scale スケール値
	 * @param double[] $values オーディオデータ
	 * @return unsigne char パッキングデータ
	 */
	public function packing($scale, $values) {
		$value = 0;
		foreach ($values as $v) {
			$value += $v;
		}
		$value = AudioUtil::getValue($value * $scale, -0x8000, 0x7fff);
		return pack('C', ($value + 0x8000) / 256);
	}
}

/**
 * モノラル16ビットパッカークラス
 */
class Monaural16bitPacker implements AudioPacker {
	/**
	 * パッキング
	 * @param double $scale スケール値
	 * @param double[] $values オーディオデータ
	 * @return unsigne short パッキングデータ
	 */
	public function packing($scale, $values) {
		$value = 0;
		foreach ($values as $v) {
			$value += $v;
		}
		$value = AudioUtil::getValue($value * $scale, -0x8000, 0x7fff);
		return pack('v', $value);
	}
}

/**
 * ステレオ8ビットパッカークラス
 */
class Stereo8bitPacker implements AudioPacker {
	/**
	 * パッキング
	 * @param double $scale スケール値
	 * @param double[][] $values オーディオデータ
	 * @return unsigne short パッキングデータ
	 */
	public function packing($scale, $values) {
		$left  = 0;
		$right = 0;
		foreach ($values as $v) {
			$left  += $v[0];
			$right += $v[1];
		}
		$left  = AudioUtil::getValue($left  * $scale, -0x8000, 0x7fff);
		$right = AudioUtil::getValue($right * $scale, -0x8000, 0x7fff);
		return pack('CC', ($left + 0x8000) / 256, ($right + 0x8000) / 256);
	}
}

/**
 * ステレオ16ビットパッカークラス
 */
class Stereo16bitPacker implements AudioPacker {
	/**
	 * パッキング
	 * @param double $scale スケール値
	 * @param double[][] $values オーディオデータ
	 * @return unsigne int パッキングデータ
	 */
	public function packing($scale, $values) {
		$left  = 0;
		$right = 0;
		foreach ($values as $v) {
			$left  += $v[0];
			$right += $v[1];
		}
		$left  = AudioUtil::getValue($left  * $scale, -0x8000, 0x7fff);
		$right = AudioUtil::getValue($right * $scale, -0x8000, 0x7fff);
		return pack('vv', $left, $right);
	}
}
