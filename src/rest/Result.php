<?php
namespace mole\yii\rest;

use yii\helpers\Json;
use yii\base\InvalidParamException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Handle HTTP response.
 * 
 * @author Jin Chen <jmole.chen@gmail.com>
 * @since 1.0
 */
class Result
{
    private $_request;
    private $_response;
    private $_promise;
    
    public function __construct(RequestInterface $request)
    {
        $this->_request = $request;
    }
    
    /**
     * @param \Psr\Http\Message\ResponseInterface|\GuzzleHttp\Promise\PromiseInterface $result
     * @throws InvalidParamException
     */
    public function result($result)
    {
        if ($result instanceof ResponseInterface) {
            $this->_response = $result;
        } elseif ($result instanceof PromiseInterface) {
            $this->_promise = $result;
        } else {
            throw new InvalidParamException('Reulst must instance of ResponseInterface or PromiseInterface');
        }
    }
    
    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        if ($this->_response === null) {
            $this->_response = $this->_promise->wait();
        }
    
        return $this->_response;
    }
    
    /**
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPromise()
    {
        return $this->_promise;
    }
    
    /**
     * @return number
     */
    public function getStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }
    
    /**
     * @return mixed
     */
    public function json()
    {
        $json = Json::decode($this->getResponse()->getBody());
        if ($this->getResponse()->getHeader('RESULT_WRAPPED')) {
            return $json['data'];
        }
    
        return $json;
    }
    
    /**
     * @return boolean
     */
    public function isValid()
    {
        return intval($this->getStatusCode() / 100) == 2;
    }
}