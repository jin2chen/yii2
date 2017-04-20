<?php

namespace mole\yii\validators;

use Yii;
use yii\helpers\Json;
use yii\validators\ValidationAsset;
use yii\validators\Validator;

/**
 * Class JsonValidator for validate JSON string.
 */
class JsonValidator extends Validator
{
    /**
     * @var string
     */
    public $message = '{attribute} is invalid JSON string.';


    /**
     * @inheritdoc
     */
    public function validateValue($value)
    {
        $valid = true;
        try {
            Json::decode($value);
        } catch (\Exception $e) {
            $valid = false;
        }

        return $valid ? null : [$this->message, []];
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = Yii::$app->getI18n()->format($this->message, [
            'attribute' => $model->getAttributeLabel($attribute),
        ], Yii::$app->language);

        $message = Json::htmlEncode($message);
        $js = <<<EOT
(function (attribute, value, messages) {
    try {
        JSON.parse(value);
    } catch (e) {
        messages.push({$message});
    }
})(attribute, value, messages);
EOT;
        ValidationAsset::register($view);
        return $js;
    }
}
