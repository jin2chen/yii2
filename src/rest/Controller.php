<?php
namespace mole\yii\rest;

/**
 * Rest base controller.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class Controller extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
}