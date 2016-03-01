<?php
namespace mole\yii\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * JSON encode for table fields.
 * 
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class MysqlJsonBehavior extends Behavior
{
    /**
     * Fields list, these fields will be json encode.
     * @var array
     */
    public $attributes = [];
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'in',
            ActiveRecord::EVENT_AFTER_INSERT => 'out',
            ActiveRecord::EVENT_AFTER_UPDATE => 'out',
            ActiveRecord::EVENT_AFTER_FIND => 'out',
        ]; 
    }
    
    public function in($event)
    {
        static::input($this->owner, $this->attributes, $this->owner->isNewRecord);
    }
    
    public function out($event)
    {
        static::output($this->owner, $this->attributes, $this->owner->isNewRecord);
    }
    
    public static function input(&$model, array $attrs, $isNew = false)
    {
        foreach ($attrs as $attr) {
            if ($isNew && !isset($model[$attr]) || !is_array($model[$attr])) {
                $model[$attr] = [];
            }
    
            if (isset($model[$attr])) {
                $model[$attr] = json_encode(is_array($model[$attr]) ? $model[$attr]: [], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            }
        }
    }
    
    public static function output(&$model, array $attrs, $isNew = false)
    {
        foreach ($attrs as $attr) {
            if (isset($model[$attr]) && !is_array($model[$attr])) {
                $model[$attr] = json_decode($model[$attr], true) ?: [];
            }
        }
    }
}