<?php
/**
 * AudioDevice.php
 *
 * @author @MIRROR_
 * @license MIT
 */

/**
 * オーディオデバイスを一元管理するためのインターフェース
 */
interface AudioDevice {
	/**  @var int 基本増幅量 */
	const baseAmp = 0x2000;

	/**
	 * サンプリングレートを設定
	 * @param int $value
	 */
	function setSampleRate($value);

	/**
	 * 音色を設定
	 * @param int $value
	 */
	function setVoice($value);

	/**
	 * 音量を設定
	 * @param int $value
	 */
	function setVolume($value);

	/**
	 * ノート番号を設定
	 * @param int $value
	 */
	function setNoteNo($value);

	/**
	 * 音量オフセットを設定
	 * @param int $value
	 */
	function setOffsetVolume($value);

	/**
	 * ノートオフセットを設定
	 * @param int $value
	 */
	function setOffsetNote($value);

	/**
	 * 周波数オフセットを設定
	 * @param int $value
	 */
	function setOffsetFrequency($value);

	/**
	 * ノートオン設定
	 * @param int $noteNo ノート番号
	 */
	function noteOn($noteNo);

	/**
	 * ノートオフ設定
	 */
	function noteOff();

	/**
	 * サンプリング
	 * @return double サンプリング情報
	 */
	function sampling();
}
