<?php
namespace mole\yii\validators;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * Validate for embedded documents.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class EmbedManyValidator extends Validator
{
    /**
     * @var string Embedded Model class name.
     */
    public $embedded;

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->{$attribute}) || !ArrayHelper::isIndexed($model->{$attribute}, true)) {
            $model->addError($attribute, 'invalid data.');
            return;
        }

        $config = [];
        if (is_string($this->embedded)) {
            $config['class'] = $this->embedded;
        } else {
            $config = $this->embedded;
        }

        $items = [];
        foreach ($model->{$attribute} as $i => $data) {
            /* @var $embedded \yii\base\Model */
            $embedded = Yii::createObject($config);
            $embedded->scenario = $model->scenario;
            $embedded->setAttributes($data);
            if (!$embedded->validate()) {
                $items[] = $data;
                foreach ($embedded->getErrors() as $key => $errors) {
                    $errorKey = "{$attribute}.{$i}.{$key}";
                    foreach ($errors as $error) {
                        $model->addError($errorKey, $error);
                    }
                }
                return;
            } else {
                $items[] = $embedded->toArray();
            }

            $embedded = null;
        }

        $model->{$attribute} = $items;
    }
}
