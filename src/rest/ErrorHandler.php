<?php
namespace mole\yii\rest;

use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Rest error handler.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class ErrorHandler extends \yii\base\ErrorHandler
{
    public function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }
        
        $data = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ];
        
        if ($exception instanceof HttpException) {
            $response->setStatusCode($exception->statusCode);
        } else {
            $response->setStatusCode(500);
            $data['message'] = Response::$httpStatuses[500];
        }
        
        $response->format = Response::FORMAT_JSON;
        $response->data = $data;
        $response->send();
    }
}
