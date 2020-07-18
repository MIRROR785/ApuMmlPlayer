<?php
/**
 * MmlConst.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Mml;

/**
 * MML関連の定数クラス
 */
class MmlConst
{
    /** @var int キー番号：ド */
    public const KNO_C  =  0;

    /** @var int キー番号：ド# */
    public const KNO_Cs =  1;

    /** @var int キー番号：レ */
    public const KNO_D  =  2;

    /** @var int キー番号：レ# */
    public const KNO_Ds =  3;

    /** @var int キー番号：ミ */
    public const KNO_E  =  4;

    /** @var int キー番号：ファ */
    public const KNO_F  =  5;

    /** @var int キー番号：ファ# */
    public const KNO_Fs =  6;

    /** @var int キー番号：ソ */
    public const KNO_G  =  7;

    /** @var int キー番号：ソ# */
    public const KNO_Gs =  8;

    /** @var int キー番号：ラ */
    public const KNO_A  =  9;

    /** @var int キー番号：ラ# */
    public const KNO_As  = 10;

    /** @var int キー番号：シ */
    public const KNO_B = 11;

    /** @var int キー番号：休 */
    public const KNO_R  = 12;

    /** @var int キー番号数（休符除く） */
    public const KNO_COUNT = 12;

    /** @var bool ノートオフ */
    public const NOTE_OFF = false;

    /** @var bool ノートオン */
    public const NOTE_ON  = true;
}
