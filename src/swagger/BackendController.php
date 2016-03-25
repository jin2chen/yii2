<?php
namespace mole\yii\swagger;

use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Swagger backend endpoint.
 *
 * ~~~
 * [
 *     'controllerMap' => [
 *         'class' => 'mole\yii\swagger\BackendController',
 *         'scheme' => 'path'
 *     ],
 * ]
 * ~~~
 *
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class BackendController extends Controller
{
    /**
     * @var boolean
     */
    public $enableCsrfValidation = false;
    /**
     * @var string scheme file path.
     */
    public $scheme;

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $scheme = Yii::getAlias($this->scheme);
        if ($request->isPut) {
            file_put_contents($scheme, $request->getRawBody());
            return 'OK';
        }

        $response->headers->set('Content-type', 'application/yaml');
        $response->format = Response::FORMAT_HTML;
        return file_get_contents($scheme);
    }
}
