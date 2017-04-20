<?php
namespace mole\yii\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Validate for embedded document.
 */
class EmbedOneValidator extends Validator
{
    /**
     * @var string|array|Model Embedded Model class name.
     */
    public $embedded;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_string($this->embedded)) {
            $embedded = Yii::createObject(['class' => $this->embedded]);
        } elseif (is_array($this->embedded)) {
            $embedded = Yii::createObject($this->embedded);
        } elseif ($this->embedded instanceof Model) {
            $embedded = $this->embedded;
        } else {
            $embedded = null;
        }

        $this->embedded = $embedded;
        if (!($this->embedded instanceof Model)) {
            throw new InvalidConfigException('EmbedOneValidator::embedded must be an array config or instance of Model.');
        }

    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->{$attribute})) {
            $model->addError($attribute, 'invalid data.');
            return;
        }

        $embedded = $this->embedded;
        $scenarios = $embedded->scenarios();
        if (isset($scenarios[$model->scenario])) {
            $embedded->scenario = $model->scenario;
        }
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
