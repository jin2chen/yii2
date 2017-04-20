<?php
namespace mole\yii;

use Yii;
use mole\helpers\BaseUtils;

/**
 * Utils functions base YII framework.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class Utils extends BaseUtils
{
    /**
     * Create an Auto-Incrementing ID for mongodb collection.
     *
     * @param string $name Collection name.
     * @return integer
     */
    public static function mongoid($name)
    {
        return Yii::$app->mongodb
            ->getCollection('autoid')
            ->mongoCollection
            ->findAndModify(
                ['_id' => $name],
                ['$inc' => ['id' => 1]],
                null,
                ['new' => true, 'upsert' => true]
            )['id'];
    }

    /**
     * Encrypt data.
     *
     * @param mixed $data
     * @param string $salt
     * @return string
     */
    public static function encrypt($data, $salt)
    {
        $data = Yii::$app->security
            ->encryptByKey(
                json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
                $salt
            );

        $data = static::urlSafeB64Encode($data);
        return $data;
    }

    /**
     * Decrypt data.
     *
     * @param string $data
     * @param string $salt
     * @return mixed If can't decrypt, return false.
     */
    public static function decrypt($data, $salt)
    {
        $data = static::urlSafeB64Decode($data);
        $data = Yii::$app->security
            ->decryptByKey($data, $salt);

        if ($data === false) {
            return false;
        }

        $data = json_decode($data, true);
        return $data;
    }
}
