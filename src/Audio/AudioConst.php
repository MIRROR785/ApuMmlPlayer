<?php
/**
 * AudioConst.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Audio;

/**
 * オーディオ関連の定数クラス
 */
class AudioConst
{
    /** @var int[] 周波数配列 */
    private static $frequencies = [
        /* o1  c     32.703 */     0, // 設定不可
        /*     c+    34.648 */     0, // 設定不可
        /*     d     36.708 */     0, // 設定不可
        /*     d+    38.891 */     0, // 設定不可
        /*     e     41.203 */     0, // 設定不可
        /*     f     43.654 */     0, // 設定不可
        /*     f+    46.249 */     0, // 設定不可
        /*     g     48.999 */     0, // 設定不可
        /*     g+    51.913 */     0, // 設定不可
        /*     a     55.000 */    55,
        /*     a+    58.270 */    58,
        /*     b     61.735 */    61,
        /* o2  c     65.406 */    65,
        /*     c+    69.296 */    69,
        /*     d     73.416 */    73,
        /*     d+    77.782 */    77,
        /*     e     82.407 */    82,
        /*     f     87.307 */    87,
        /*     f+    92.499 */    92,
        /*     g     97.999 */    97,
        /*     g+   103.826 */   103,
        /*     a    110.000 */   110,
        /*     a+   116.541 */   116,
        /*     b    123.471 */   123,
        /* o3  c    130.813 */   130,
        /*     c+   138.591 */   138,
        /*     d    146.832 */   146,
        /*     d+   155.563 */   155,
        /*     e    164.814 */   164,
        /*     f    174.614 */   174,
        /*     f+   184.997 */   184,
        /*     g    195.998 */   195,
        /*     g+   207.652 */   207,
        /*     a    220.000 */   220,
        /*     a+   233.082 */   233,
        /*     b    246.942 */   246,
        /* o4  c    261.626 */   261,
        /*     c+   277.183 */   277,
        /*     d    293.665 */   293,
        /*     d+   311.127 */   311,
        /*     e    329.628 */   329,
        /*     f    349.228 */   349,
        /*     f+   369.994 */   369,
        /*     g    391.995 */   391,
        /*     g+   415.305 */   415,
        /*     a    440.000 */   440, // マスターチューニング
        /*     a+   466.164 */   466,
        /*     b    493.883 */   493,
        /* o5  c    523.251 */   523,
        /*     c+   554.365 */   554,
        /*     d    587.330 */   587,
        /*     d+   622.254 */   622,
        /*     e    659.255 */   659,
        /*     f    698.456 */   698,
        /*     f+   739.989 */   739,
        /*     g    783.991 */   783,
        /*     g+   830.609 */   830,
        /*     a    880.000 */   880,
        /*     a+   932.328 */   932,
        /*     b    987.767 */   987,
        /* o6  c   1046.502 */  1046,
        /*     c+  1108.731 */  1108,
        /*     d   1174.659 */  1174,
        /*     d+  1244.508 */  1244,
        /*     e   1318.510 */  1318,
        /*     f   1396.913 */  1396,
        /*     f+  1479.978 */  1479,
        /*     g   1567.982 */  1567,
        /*     g+  1661.219 */  1661,
        /*     a   1760.000 */  1760,
        /*     a+  1864.655 */  1864,
        /*     b   1975.533 */  1975,
        /* o7  c   2093.005 */  2093,
        /*     c+  2217.461 */  2217,
        /*     d   2349.318 */  2349,
        /*     d+  2489.016 */  2489,
        /*     e   2637.020 */  2637,
        /*     f   2793.826 */  2793,
        /*     f+  2959.955 */  2959,
        /*     g   3135.963 */  3135,
        /*     g+  3322.438 */  3322,
        /*     a   3520.000 */  3520,
        /*     a+  3729.310 */  3729,
        /*     b   3951.066 */  3951,
        /* o8  c   4186.009 */  4186,
        /*     c+  4434.922 */  4434,
        /*     d   4698.636 */  4698,
        /*     d+  4978.032 */  4978,
        /*     e   5274.041 */  5274,
        /*     f   5587.652 */  5587,
        /*     f+  5919.911 */  5919,
        /*     g   6271.927 */  6271,
        /*     g+  6644.875 */  6644,
        /*     a   7040.000 */  7040,
        /*     a+  7458.620 */  7458,
        /*     b   7902.133 */  7902,
        /* o9  c   8372.018 */  8372,
        /*     c+  8869.844 */  8869,
        /*     d   9397.272 */  9397,
        /*     d+  9956.064 */  9956,
        /*     e  10548.082 */ 10548,
        /*     f  11175.304 */ 11175,
        /*     f+ 11839.822 */ 11839,
        /*     g  12543.854 */ 12543,
        /*     g+ 13289.750 */ 13289,
        /*     a  14080.000 */ 14080,
        /*     a+ 14917.240 */ 14917,
        /*     b  15804.266 */ 15804,
        ];

    /** @var int[] ノイズ周数配列 */
    private static $noiseFrequencies = [
        /* F: */ 0x7F2,
        /* E: */ 0x3F9,
        /* D: */ 0x1FC,
        /* C: */ 0x17D,
        /* B: */ 0x0FE,
        /* A: */ 0x0BE,
        /* 9: */ 0x07F,
        /* 8: */ 0x065,
        /* 7: */ 0x050,
        /* 6: */ 0x040,
        /* 5: */ 0x030,
        /* 4: */ 0x020,
        /* 3: */ 0x010,
        /* 2: */ 0x008,
        /* 1: */ 0x004,
        /* 0: */ 0x002,
        ];

    /** @var int 最大ノート番号 */
    private static $maxNoteNo = 0;

    /**
     * 初期化処理
     */
    public static function initialize() {
        if (AudioConst::$maxNoteNo <= 0) {
            AudioConst::$maxNoteNo = count(AudioConst::$frequencies) - 1;
        }
    }

    /**
     * ノート番号を取得
     * @param int $octave オクターブ
     * @param int $keyNo  キー番号
     * @return int ノート番号
     */
    public static function getNoteNo($octave, $keyNo) {
        return ($octave - 1) * 12 + $keyNo;
    }

    /**
     * 周波数を取得
     * @param int $noteNo ノート番号
     * @return 周波数
     */
    public static function getFrequency($noteNo) {
        return AudioConst::$frequencies[AudioConst::getValue($noteNo, 0, AudioConst::$maxNoteNo)];
    }

    /**
     * ノイズ番号を取得
     * @param int $octave オクターブ
     * @param int $keyNo  キー番号
     * @return ノイズ番号
     */
    public static function getNoiseNo($octave, $keyNo) {
        return ($octave - 1) * 12 + $keyNo;
    }

    /**
     * ノイズ周波数を取得
     * @param int $noiseNo  ノイズ番号
     * @return ノイズ周波数
     */
    public static function getNoiseFrequency($noiseNo) {
        return 1789772.5 / AudioConst::$noiseFrequencies[$noiseNo & 0x0f];
    }

    /**
     * 範囲内の値を取得
     * @param int|double $value    値
     * @param int|double $minValue 最小値
     * @param int|double $maxValue 最大値
     * @return int|double 範囲内の値
     */
    public static function getValue($value, $minValue, $maxValue) {
        if ($value < $minValue)      $value = $minValue;
        else if ($value > $maxValue) $value = $maxValue;
        return $value;
    }
}
