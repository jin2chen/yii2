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
     * @var ActiveRecord
     */
    public $owner;
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
            ActiveRecord::EVENT_AFTER_VALIDATE => 'out',
            ActiveRecord::EVENT_BEFORE_INSERT => 'in',
            ActiveRecord::EVENT_AFTER_INSERT => 'out',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'in',
            ActiveRecord::EVENT_AFTER_UPDATE => 'out',
            ActiveRecord::EVENT_AFTER_FIND => 'out',
        ];
    }

    /**
     * Input.
     */
    public function in()
    {
        static::inJson($this->owner, $this->attributes, $this->owner->isNewRecord);
    }

    /**
     * Output.
     */
    public function out()
    {
        static::outJson($this->owner, $this->attributes, $this->owner->isNewRecord);
    }

    /**
     * @param ActiveRecord $model
     * @param array $attributes
     * @param bool $isNew
     */
    protected static function inJson(&$model, array $attributes, $isNew = false)
    {
        foreach ($attributes as $attribute) {
            if ($isNew && !isset($model[$attribute]) || !is_array($model[$attribute])) {
                $model[$attribute] = [];
            }

            if (isset($model[$attribute])) {
                $model[$attribute] = json_encode(is_array($model[$attribute]) ? $model[$attribute]: [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * @param ActiveRecord $model
     * @param array $attributes
     * @param bool $isNew
     */
    protected static function outJson(&$model, array $attributes, $isNew = false)
    {
        foreach ($attributes as $attribute) {
            if ($isNew) {
                // nothing
            }

            if (isset($model[$attribute]) && !is_array($model[$attribute])) {
                $model[$attribute] = json_decode($model[$attribute], true) ?: [];
            }
        }
    }
}
