<?php
namespace mole\yii\rest;

use yii\base\BootstrapInterface;
use yii\base\Object;
use yii\base\Event;
use yii\web\Response;

/**
 * Register events.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class Bootstrap extends Object implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $this->response();
    }
    
    protected function response()
    {
        Event::on(Response::className(), Response::EVENT_BEFORE_SEND, function ($event) {
            // Clean output.
            for ($level = ob_get_level(); $level > 0; --$level) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            
            /* @var $response \yii\web\Response */
            /* @var $event \yii\base\Event */
            $response = $event->sender;
            $response->format = Response::FORMAT_JSON;
            
        });
    }
}
