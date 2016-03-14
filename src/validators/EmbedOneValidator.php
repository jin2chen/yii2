<?php
namespace mole\yii\validators;

use Yii;
use yii\validators\Validator;

/**
 * Validate for embedded document.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class EmbedOneValidator extends Validator
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
        if (!is_array($model->{$attribute})) {
            $model->addError($attribute, 'invalid data.');
            return;
        }

        $config = [];
        if (is_string($this->embedded)) {
            $config['class'] = $this->embedded;
        } else {
            $config = $this->embedded;
        }

        /* @var $embedded \yii\base\Model */
        $embedded = Yii::createObject($config);
        $embedded->scenario = $model->scenario;
        $embedded->setAttributes($model->{$attribute});

        if (!$embedded->validate()) {
            $errors = $embedded->getErrors();
            foreach ($errors as $key => $error) {
                foreach ($error as $item) {
                    $model->addError("{$attribute}.{$key}", $item);
                }
            }
        } else {
            $model->{$attribute} = $embedded->toArray();
        }
    }
}
