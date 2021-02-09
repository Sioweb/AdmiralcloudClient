<?php

namespace Sioweb\AdmiralcloudClient\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Sioweb\AdmiralcloudClient\Classes\MissingParameterException;
use Sioweb\AdmiralcloudClient\Classes\DatatypeException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use RuntimeException;
use Sioweb\AdmiralcloudClient\Classes\Signature;
use Symfony\Component\Dotenv\Dotenv;

class Request
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $rootDir;

    public function __construct($rootDir = null)
    {
        if($rootDir !== null) {
            $this->setRootDir(rtrim($rootDir, '/'));
        }

        $dotenv = new Dotenv(true);
        $dotenv->load($this->getRootDir().'/.env');

        $this->client = new Client([
            'headers' => [
                'x-admiralcloud-accesskey' => getenv('AC_API_KEY')
            ]
        ]);
        
        $this->apiVersion = getenv('AC_API_VERSION');
    }

    public function __call($method, $params)
    {
        if(method_exists($this, 'get' . ucfirst($method))) {
            return call_user_func_array([$this, 'get' . ucfirst($method)], $params);
        }
    }

    /**
     * 
     * @param string $rootDir 
     * @return void 
     */
    public function setRootDir(string $rootDir) : void
    {
        $this->rootDir = $rootDir;
    }

    /**
     * 
     * @return string 
     */
    private function getRootDir() : string
    {
        if($this->rootDir) {
            return $this->rootDir;
        }

        if(is_file($_SERVER['DOCUMENT_ROOT'] . '/composer.json')) {
            return $_SERVER['DOCUMENT_ROOT'];
        }

        return dirname($_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * 
     * @param array $Payload 
     * @param null|int $timestamp 
     * @return array 
     * @throws MissingParameterException 
     * @throws DatatypeException 
     * @throws GuzzleException 
     */
    private function getMediacontainer(array $Payload = [], ?int $timestamp = null) : array
    {
        $Params = $this->getParams(['mediacontainer', 'find'], $Payload, $timestamp);

        try {
            $Result = $this->client->get(
                $this->getUrl('mediacontainer', ['mediaContainerId'], $Payload),
                $this->addOptions($Params, 'query')
            );
        } catch(ClientException $e) {
            die('<pre>' . __METHOD__ . ":\n" . print_r($e, true) . "\n#################################\n\n" . '</pre>');
        }

        return $this->getResultAsArray($Result);
    }

    /**
     * 
     * @param array $Payload 
     * @param null|int $timestamp 
     * @return array 
     * @throws MissingParameterException 
     * @throws DatatypeException 
     * @throws GuzzleException 
     */
    private function getMedia(array $Payload = [], ?int $timestamp = null) : array
    {
        $Params = $this->getParams(['media', 'find'], $Payload, $timestamp);

        try {
            $Result = $this->client->get(
                $this->getUrl('media', ['mediaContainerId', 'mediaId'], $Payload),
                $this->addOptions($Params, 'query')
            );
        } catch(ClientException $e) {
            die('<pre>' . __METHOD__ . ":\n" . print_r($e, true) . "\n#################################\n\n" . '</pre>');
        }

        return $this->getResultAsArray($Result);
    }

    /**
     * 
     * @param array $Payload 
     * @param null|int $timestamp 
     * @return array 
     * @throws MissingParameterException 
     * @throws DatatypeException 
     * @throws GuzzleException 
     * @throws RuntimeException 
     */
    private function getSearch(array $Payload = [], ?int $timestamp = null) : array
    {
        $Params = $this->getParams('search', $Payload, $timestamp);
        
        $Result = $this->client->post(
            $this->getUrl('search'),
            $this->addOptions($Params)
        );
        
        return $this->getResultAsArray($Result);
    }

    /**
     * 
     * @param mixed $type 
     * @param mixed $Payload 
     * @param null|int $timestamp 
     * @return array 
     */
    private function getParams($type, $Payload = null, ?int $timestamp = null) : array
    {
        if(is_string($type)) {
            $type = [$type, $type];
        }

        $Params = [
            'accessSecret' => getenv('AC_SECRET_KEY'),
            'controller' => $type[0],
            'action' => $type[1],
            'ts' => gmdate('U')
        ];

        if($timestamp !== null) {
            $Params['ts'] = $timestamp;
        }

        if(!empty($Payload)) {
            // make sure payload keys are ordered from A-Z!
            ksort($Payload);
            $Params['payload'] = $Payload;
        }

        return $Params;
    }

    private function getUrl(string $endpoint, $UrlParams = [], $Payload = null)
    {
        $Segments = [
            getenv('AC_API_URL'),
            $this->apiVersion,
            $endpoint
        ];

        $URL = array_intersect_key($Payload, array_flip($UrlParams));
        if(!empty($URL)) {
            $Segments[] = implode('/', $URL);
        }

        return implode('/', $Segments);
    }

    /**
     * 
     * @param array $Params 
     * @return array 
     * @throws MissingParameterException 
     * @throws DatatypeException 
     */
    private function addOptions(array $Params, $dataType = 'form_params') : array
    {
        $Sign = (new Signature())->sign($Params);
        
        $Options = [
            'headers' => [
                'x-admiralcloud-rts' => $Sign['ts'],
                'x-admiralcloud-hash' => $Sign['hash'],
            ]
        ];

        if(!empty($Params['payload'])) {
            $Options[$dataType] = $Params['payload'];
        }

        return $Options;
    }

    /**
     * 
     * @param Response $Result 
     * @return array 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function getResultAsArray(\GuzzleHttp\Psr7\Response $Result) : array
    {
        return json_decode($Result->getBody()->getContents(), true);
    }
}
