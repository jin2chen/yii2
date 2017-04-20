<?php

namespace mole\yii\web;

use mole\yii\Utils;

/**
 * Overwrite the getUserIP method.
 */
class Request extends \yii\web\Request
{
    /**
     * @inheritdoc
     */
    public function getUserIP()
    {
        return Utils::getIp();
    }
}
