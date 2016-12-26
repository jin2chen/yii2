<?php
namespace mole\yii\behaviors;

use yii\base\Behavior;

/**
 * Attach events for class.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class EventBehavior extends Behavior
{
    /**
     * @var array eventKey => callable
     */
    public $events;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return $this->events;
    }
}
