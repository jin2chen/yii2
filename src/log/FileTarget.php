<?php

namespace common\libraries\log;

use Yii;

/**
 * Log request ID, combine with RequestIdBehavior.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class FileTarget extends \yii\log\FileTarget
{
    /**
     * @inheritdoc
     */
    public function getMessagePrefix($message)
    {
        if (Yii::$app === null) {
            return '';
        }

        $requestId = Yii::$app->getRequestId() ?: '-';
        return parent::getMessagePrefix($message) . "[$requestId]";
    }
}