<?php
/**
 * AudioPacker.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Audio;

/**
 * オーディオデータのパッキングインターフェース
 */
interface AudioPackerInterface
{
    /**
     * パッキング
     * @param double $scale スケール値
     * @param double[]|double[][] $values オーディオデータ
     * @return unsigne char|unsigned short|unsigned int パッキングデータ
     */
    function packing($scale, $values);
}

/**
 * モノラル8ビットパッキング制御クラス
 */
class Monaural8bitPacker implements AudioPackerInterface
{
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
        $value = (AudioConst::getValue($value * $scale, -0x8000, 0x7fff) + 0x8000) / 256;
        return pack('C', (int)$value);
    }
}

/**
 * モノラル16ビットパッキング制御クラス
 */
class Monaural16bitPacker implements AudioPackerInterface
{
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
        $value = AudioConst::getValue($value * $scale, -0x8000, 0x7fff);
        return pack('v', $value);
    }
}

/**
 * モノラル32ビットfloatパッキング制御クラス
 */
class Monaural32bitFloatPacker implements AudioPackerInterface
{
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
        $value = AudioConst::getValue($value * $scale, -0x8000, 0x7fff);
        $value = $value * $scale / 32768.0;
        return pack('g', $value);
    }
}

/**
 * ステレオ8ビットパッキング制御クラス
 */
class Stereo8bitPacker implements AudioPackerInterface
{
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
        $left  = (AudioConst::getValue($left  * $scale, -0x8000, 0x7fff) + 0x8000) / 256;
        $right = (AudioConst::getValue($right * $scale, -0x8000, 0x7fff) + 0x8000) / 256;
        return pack('CC', $left, $right);
    }
}

/**
 * ステレオ16ビットパッキング制御クラス
 */
class Stereo16bitPacker implements AudioPackerInterface
{
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
        $left  = AudioConst::getValue($left  * $scale, -0x8000, 0x7fff);
        $right = AudioConst::getValue($right * $scale, -0x8000, 0x7fff);
        return pack('vv', $left, $right);
    }
}

/**
 * ステレオ32ビットfloatパッキング制御クラス
 */
class Stereo32bitFloatPacker implements AudioPackerInterface
{
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
        $left  = AudioConst::getValue($left  * $scale, -0x8000, 0x7fff);
        $right = AudioConst::getValue($right * $scale, -0x8000, 0x7fff);
        $left  = $left  * $scale / 32768.0;
        $right = $right * $scale / 32768.0;
        return pack('gg', $left, $right);
    }
}

/**
 * オーディオパッキング制御クラス
 */
class AudioPacker
{
    /**
     * インスタンスの生成
     * @param int $channelCount チャンネル数
     * @param int $sampleBits 量子化ビット数
     */
    public static function create($channelCount, $sampleBits) {
        $packer = null;

        switch ($sampleBits) {
        case 8:
            $packer = ($channelCount === 1) ? new Monaural8bitPacker()  : new Stereo8bitPacker();
            break;
        case 32:
            $packer = ($channelCount === 1) ? new Monaural32bitFloatPacker()  : new Stereo32bitFloatPacker();
            break;
        default:
            $packer = ($channelCount === 1) ? new Monaural16bitPacker() : new Stereo16bitPacker();
            break;
        }

        return $packer;
    }
}
