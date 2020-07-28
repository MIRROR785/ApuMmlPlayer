<?php
/**
 * Json.php
 *
 * @author @MIRROR_
 * @license MIT
 */

namespace MIRROR785\ApuMmlPlayer\Container;

use Exception;

/**
 * JSONデータを取り扱うクラス
 */
class Json
{
    /** @var array エラー名配列 */
    private static $json_errors = null;

    /**
     * 初期化処理
     */
    public static function initialize() {
        if (Json::$json_errors === null) {
            $constants = get_defined_constants(true);
            Json::$json_errors = array();
            foreach ($constants["json"] as $name => $value) {
                if (!strncmp($name, "JSON_ERROR_", 11)) {
                    Json::$json_errors[$value] = $name;
                }
            }
        }
    }

    /**
     * JSONデータを配列に変換
     * @param string $json JSONデータ
     */
    public static function toArray($json) {
        Json::initialize();

        $values = json_decode($json, true, 10, JSON_NUMERIC_CHECK);

        if ($values === null) {
            $error = json_last_error();
            if (array_key_exists($error, Json::$json_errors)) {
                throw new Exception(Json::$json_errors[$error]);
            } else {
                throw new Exception('Parse error.');
            }
        }

        return $values;
    }
}
