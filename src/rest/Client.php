<?php
namespace mole\yii\rest;

use yii\base\Component;
use yii\base\InvalidConfigException;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\GuzzleHttp;

/**
 * HTTP request client.
 * 
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class Client extends Component
{
    public $baseUrl;
    public $tokenPath = '/token';
    public $token;
    public $clientId;
    public $clientOptions = [
        'timeout' => 60,
        'http_errors' => false
    ];
    
    private $_client;
    private $_restObject;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->clientId === null) {
            throw new InvalidConfigException('Client::clientId must be set.');
        }
        if ($this->baseUrl === null) {
            throw new InvalidConfigException('Client::baseUrl must be set.');
        }
    }
    
    /**
     * Fetch a token through username and password.
     * @param string $username
     * @param string $password
     * @return string|boolean
     */
    public function fetchToken($username, $password)
    {
        $client = $this->client;
        $url = static::buildUrl($this->baseUrl, $this->tokenPath);
        $response = $client->post($url, [
            'json' => [
                'username' => $username,
                'password' => $password,
                'grant_type' => 'password',
                'client_id' => 'fixt-mobile-client'
            ]
        ]);
    
        $this->_restObject->result($response);
        if ($this->_restObject->isValid()) {
            return $this->_restObject->json()['access_token'];
        }
    
        return false;
    }
    
    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new Client($this->clientOptions);
            $handler = $this->_client->getConfig('handler');
            $handler->push(Middleware::mapRequest(function($request) {
                $this->_restObject = new RestObject($request);
                return $request;
            }));
        }
    
        return $this->_client;
    }
    
    public function __call($name, $params)
    {
        $methods = [
            'get', 'getasync', 'post', 'postasync',
            'put', 'putasync', 'patch', 'patchasync'
        ];
    
        if (in_array(strtolower($name), $methods)) {
            if (count($params) < 1) {
                throw new \InvalidArgumentException('Magic request methods require a URI and optional options array');
            }
    
            $uri = $params[0];
            if (isset($params[1]) && is_array($params[1])) {
                $params = $params[1];
                if (isset($params['headers'])) {
                    $params['headers']['Authorization'] = 'Bearer ' . $this->token;
                } else {
                    $params['headers'] = [
                        'Authorization' => 'Bearer ' . $this->token,
                    ];
                }
            } else {
                $params = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                    ]
                ];
            }
            $query = [];
            if (isset($params['query'])) {
                $query = $params['query'];
                unset($params['query']);
            }
            $uri = static::buildUrl($this->baseUrl, $uri, $query);
            $result = call_user_func_array([$this->client, $name], [$uri, $params]);
            $this->_restObject->result($result);
            return $this->_restObject;
        }
    
        return parent::__call($name, $params);
    }
    
    /**
     * Build url.
     * If the path index of :xx and params has key `xx`, the path will be replace by the params[xx] value.
     *
     * ~~~
     * Client::buildUrl('http://localhost/rest', '/user/:id', ['id' => 1, 'status' => 0]);
     * //=> http://localhost/rest/user/1?status=0
     * ~~~
     *
     * @param string $baseUrl http://localhost/rest
     * @param string $path /user/:id
     * @param array $params query params for url.
     * @return string
     */
    public static function buildUrl($baseUrl, $path, $params = [])
    {
        if (!is_array($params)) {
            $params = [];
        }
    
        $path = preg_replace_callback('/:(\w+)/', function($matches) use (&$params) {
            if (isset($params[$matches[1]])) {
                $tmp = $params[$matches[1]];
                unset($params[$matches[1]]);
                return urlencode($tmp);
            }
    
            return '';
        }, $path);
    
            unset($params['r'], $params['_pjax']);
            $path = trim(preg_replace('#/{2,}#', '/', $path), '/');
            if (!empty($params)) {
                $path .= '?' . http_build_query($params);
            }
    
            return rtrim($baseUrl, '/') . '/' . rtrim($path);
    }
}