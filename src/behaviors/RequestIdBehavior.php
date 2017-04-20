<?php

namespace mole\yii\behaviors;

use Ramsey\Uuid\Uuid;
use yii\base\Application;
use yii\base\Behavior;
use yii\base\Event;

/**
 * Generate an id for each request.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class RequestIdBehavior extends Behavior
{
    /**
     * @var string
     */
    protected $requestId;


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'beforeRequest'
        ];
    }

    /**
     * @param Event $event
     */
    public function beforeRequest($event)
    {
        $this->requestId = Uuid::uuid4()->toString();

        /* @var Application $app */
        $app = $event->sender;
        if ($app instanceof \yii\web\Application) {
            $app->response->getHeaders()->set('X-Request-Id', $this->requestId);
        }
    }

    /**
     * Get current request id
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Generate outgoing request id for tracing purpose
     *
     * @return string
     */
    public function genOutRequestId()
    {
        return $this->requestId . ';' . Uuid::uuid4()->toString();
    }
}
