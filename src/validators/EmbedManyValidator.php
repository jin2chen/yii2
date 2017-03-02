<?php
namespace mole\yii\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
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
            throw new InvalidConfigException('EmbedManyValidator::embedded must be an array config or instance of Model.');
        }

    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->{$attribute}) || !ArrayHelper::isIndexed($model->{$attribute}, true)) {
            $model->addError($attribute, 'invalid data.');
            return;
        }

        $items = [];
        foreach ($model->{$attribute} as $i => $data) {
            $embedded = $this->embedded;
            $scenarios = $embedded->scenarios();
            if (isset($scenarios[$model->scenario])) {
                $embedded->scenario = $model->scenario;
            }
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
