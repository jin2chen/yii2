<?php
namespace mole\yii\rest;

use Yii;
use yii\web\HttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Rest error handler.
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class ErrorHandler extends \yii\base\ErrorHandler
{
    /**
     * @inheritdoc
     */
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
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidateException) {
            $data['errors'] = $exception->getErrors();
        } elseif ($exception instanceof BadRequestHttpException) {
            $trace = $exception->getTrace();
            $trace = $trace[0];
            if (isset($trace['class']) && isset($trace['function'])) {
                if ($trace['class'] === 'yii\web\JsonParser' && $trace['function'] === 'parse') {
                    $data['code'] = 3900;
                } elseif ($trace['class'] === 'yii\web\Controller' && $trace['function'] == 'beforeAction') {
                    $data['code'] = 3901;
                } elseif ($trace['class'] === 'yii\web\Controller' && $trace['function'] == 'bindActionParams') {
                    $data['code'] = 3902;
                }
            }
        } elseif ($exception instanceof NotFoundHttpException) {
            $trace = $exception->getTrace();
            $trace = $trace[0];
            if ($trace['class'] === 'yii\web\Request' && $trace['function'] === 'resolve') {
                $data['code'] = 3910;
            } elseif ($trace['class'] === 'yii\web\Application' && $trace['function'] === 'handleRequest') {
                $data['code'] = 3910;
            }
        } elseif ($exception instanceof UnauthorizedHttpException) {
            if ($exception->getCode() == 0) {
                $data['code'] = 1001;
            }
        } elseif ($exception instanceof ForbiddenHttpException) {
            if ($exception->getCode() == 0) {
                $data['code'] = 1013;
            }
        }

        if ($exception instanceof HttpException) {
            $response->setStatusCode($exception->statusCode);
        } else {
            $response->setStatusCode(500);
            $data['message'] = Response::$httpStatuses[500];
            if (YII_DEBUG) {
                $data['trace'] = $exception->getTraceAsString();
            }
        }

        $response->format = Response::FORMAT_JSON;
        $response->data = $data;
        $response->send();
    }
}
