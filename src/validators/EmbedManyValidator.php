<?php
namespace mole\yii\validators;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

class EmbedManyValidator extends Validator
{
    public $embeded;
    
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->{$attribute}) || ArrayHelper::isIndexed($model->{$attribute}, true)) {
            $model->addError($attribute, 'invalid data.');
            return;
        }
        
        $config = [];
        if (is_string($this->embedded)) {
            $config['class'] = $this->embedded;
        } else {
            $config = $this->embedded;
        }

        foreach ($model->{$attribute} as $i => $data) {
            /* @var $embedded \yii\base\Model */
            $embedded = Yii::createObject($config);
            $embedded->scenario = $model->scenario;
            $embedded->setAttributes($data);
            if (!$embedded->validate()) {
                foreach ($embedded->getErrors() as $key => $errors) {
                    foreach ($errors as $error) {
                        $model->addError("{$attribute}.{$i}.{$key}", $error);
                    }
                }
                return;
            } else {
                $model->{$attribute}[$i] = $embedded->toArray();
            }
            
            $embedded = null;
        }
    }
}