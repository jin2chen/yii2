<?php
namespace mole\yii\db;

use yii\validators\Validator;

class ActiveRecord extends \yii\db\ActiveRecord
{
    const FIND_ALL = 'findAll';
    const FIND_DELETED = 'findDeleted';
    const FIND_UN_DELETED = 'findUnDeleted';

    public static $deleted = false;

    /**
     * @inheritdoc
     */
    public static function find($find = self::FIND_UN_DELETED)
    {
        if (static::$deleted === false) {
            return parent::find();
         }
        
        if ($find === self::FIND_UN_DELETED) {
            return parent::find()->andWhere([static::$deleted => false]);
        } elseif ($find === self::FIND_DELETED) {
            return parent::find()->andWhere([static::$deleted => true]);
        } else {
            return parent::find();
        }
    }

    /**
     * Adds a validation rule to this model.
     * You can also directly manipulate [[validators]] to add or remove validation rules.
     * This method provides a shortcut.
     * @param string|array $attributes the attribute(s) to be validated by the rule
     * @param mixed $validator the validator for the rule.This can be a built-in validator name,
     * a method name of the model class, an anonymous function, or a validator class name.
     * @param array $options the options (name-value pairs) to be applied to the validator
     * @return $this the model itself
     */
    public function addRule($attributes, $validator, $options = [])
    {
        $validators = $this->getValidators();
        $validators->append(Validator::createValidator($validator, $this, (array) $attributes, $options));

        return $this;
    }
}
