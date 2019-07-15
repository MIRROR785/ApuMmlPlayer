<?php
/**
 * AudioUnit.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * 擬似APUとトラックの紐付けを管理するクラス
 */
class AudioUnit {
	/** @var string オーディオユニット名 */
	public $name;

	/** @var PseudoApu 擬似APU */
	public $apu;

	/** @var int サンプリングレート */
	public $sampleRate;

	/** @var int[] トラック番号配列 */
	public $trackNumbers;

	/** @var int->double[] 定位配列 */
	public $positions;

	/**
	 * コンストラクタ
	 * @param int サンプリングレート
	 * @param int[]|int=>double[] トラック、定位パラメータ
	 */
	public function __construct($sampleRate, $params) {
		$this->sampleRate = $sampleRate;

		foreach ($params as $key => $value) {
			switch ($key) {
			case 'Name':
				// ユニット名
				$this->name = $value;
				break;

			case 'Devices':
				// トラック、定位
				$options = array_values($value);
				if ($options === $value) {
					// デバイス詳細定義なしの場合はデフォルト値を設定
					$this->trackNumbers = $options;
					$this->positions = [];
					foreach ($this->trackNumbers as $tr) {
						$this->positions[$tr] = [0, 0];
					}

				} else {
					// デバイス詳細定義の取得
					$this->trackNumbers = array_keys($value);
					$this->positions = [];
					foreach ($this->trackNumbers as $tr) {
						$options = $value[$tr];
						$this->positions[$tr] = $options['Position'];
					}
				}
				break;
			}
		}

		// 擬似APU設定
		$this->apu = new PseudoApu($this->sampleRate, $this->trackNumbers);
	}
}
