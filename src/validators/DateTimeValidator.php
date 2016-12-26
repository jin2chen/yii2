<?php
namespace mole\yii\validators;

use Yii;
use yii\validators\Validator;

/**
 * DateValidator validates that the attribute value is a valid date.
 */
class DateTimeValidator extends Validator
{
    /**
     * @var string
     */
    public $format = 'Y-m-d';
    /**
     * @var boolean
     */
    public $toTimestamp = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('app', '{attribute} is not a valid date.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        try {
            $timestamp = (int) Yii::$app->formatter->asTimestamp($model->$attribute);
            if (date($this->format, $timestamp) != $model->$attribute) {
                $this->addError($model, $attribute, $this->message);
            } elseif ($this->toTimestamp) {
                $model->$attribute = $timestamp;
            }
        } catch (\Exception $e) {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
