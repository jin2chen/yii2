<?php
namespace mole\yii\web;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use app\components\ErrorCode;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class ErrorHandler extends \yii\web\ErrorHandler
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
            'codeString' => '',
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof BadRequestHttpException) {
            $trace = $exception->getTrace();
            $trace = $trace[0];
            if (isset($trace['class']) && isset($trace['function'])) {
                if ($trace['class'] === 'yii\web\JsonParser' && $trace['function'] === 'parse') {
                    $data['code'] = ErrorCode::INVALID_DATA;
                    $data['message'] = 'JSON parse error, request body is invalid.';
                } elseif ($trace['class'] === 'yii\web\Controller' && $trace['function'] == 'beforeAction') {
                    $data['code'] = ErrorCode::BAD_REQUEST;
                } elseif ($trace['class'] === 'yii\web\Controller' && $trace['function'] == 'bindActionParams') {
                    $data['code'] = ErrorCode::BAD_REQUEST;
                }
            }
        } elseif ($exception instanceof NotFoundHttpException) {
            $trace = $exception->getTrace();
            $trace = $trace[0];
            if ($trace['class'] === 'yii\web\Request' && $trace['function'] === 'resolve') {
                $data['code'] = ErrorCode::URL_NOT_FOUND;
            } elseif ($trace['class'] === 'yii\web\Application' && $trace['function'] === 'handleRequest') {
                $data['code'] = ErrorCode::URL_NOT_FOUND;
            }
        } elseif ($exception instanceof UnauthorizedHttpException) {
            if ($exception->getCode() == 0) {
                $data['code'] = ErrorCode::UNAUTHORIZED;
            }
        } elseif ($exception instanceof ForbiddenHttpException) {
            if ($exception->getCode() == 0) {
                $data['code'] = ErrorCode::FORBIDDEN;
            }
        }

        $data['codeString'] = ErrorCode::code2string($data['code']);
        if ($response->format ===  Response::FORMAT_JSON || isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $response->format = Response::FORMAT_JSON;
            if ($exception instanceof HttpException && $exception->statusCode != 500) {
                $response->setStatusCode($exception->statusCode);
            } else {
                $response->setStatusCode(500);
                $data['message'] = ErrorCode::message(ErrorCode::INTERNAL_SERVER_ERROR);
                if (YII_DEBUG) {
                    $data['trace'] = explode("\n", $exception->__toString());
                }
            }

            $response->data = $data;
            $response->send();
        } else {
            parent::renderException($exception);
        }
    }
}
