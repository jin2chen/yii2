<?php
namespace mole\yii\validators;

use Yii;
use yii\validators\Validator;

class EmbedOneValidator extends Validator
{
    public $embedded;
    
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @throws \yii\base\InvalidConfigException
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
