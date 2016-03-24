<?php
namespace mole\yii;

use Yii;
use mole\helpers\Utils as XUtils;

/**
 * Utils functions base YII framework.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class Utils
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
     * @return string
     */
    public static function encrypt($data)
    {
        $data = Yii::$app->security
            ->encryptByKey(
                json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
                Yii::$app->params['cryptSalt']
            );

        $data = XUtils::urlSafeB64Encode($data);
        return $data;
    }

    /**
     * Decrypt data.
     *
     * @param string $data
     * @return mixed If can't decrypt, return false.
     */
    public static function decrypt($data)
    {
        $data = XUtils::urlSafeB64Decode($data);
        $data = Yii::$app->security
            ->decryptByKey($data, Yii::$app->params['cryptSalt']);

        if ($data === false) {
            return false;
        }

        $data = json_decode($data, true);
        return $data;
    }

    /**
     * Remove rules of validator for some fields.
     *
     * @param array $rules rules of the Model::rules()
     * @param array $attributes
     * @return array
     */
    public static function cleanRules($rules, $attributes = [])
    {
        foreach ($rules as $i => $rule) {
            if (!is_array($rule[0])) {
                $rule[0] = [$rule[0]];
            }

            foreach ($rule[0] as $k => $attribute) {
                if (in_array($attribute, $attributes)) {
                    unset($rule[0][$k]);
                }
            }

            $rule[0] = array_values($rule[0]);
            if (empty($rule[0])) {
                unset($rules[$i]);
            }
        }

        return array_values($rules);
    }
}
