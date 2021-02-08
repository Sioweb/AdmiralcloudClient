<?php

namespace Sioweb\AcSignature\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Sioweb\AcSignature\Classes\MissingParameterException;
use Sioweb\AcSignature\Classes\DatatypeException;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Sioweb\AcSignature\Classes\Signature;
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

    public function setRootDir(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    private function getRootDir()
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
     * @param integer|null $timestamp 
     * @return array 
     * @throws MissingParameterException 
     * @throws DatatypeException 
     * @throws GuzzleException 
     * @throws RuntimeException 
     */
    private function getMediacontainerSearch(array $Payload = [], $timestamp = null)
    {
        $Params = [
            'accessSecret' => getenv('AC_SECRET_KEY'),
            'controller' => 'search',
            'action' => 'search',
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

        $Sign = (new Signature())->sign($Params);
        try {
            $Result = $this->client->post(
                getenv('AC_API_URL') . '/' . $this->apiVersion . '/search/',
                [
                    'headers' => [
                        'x-admiralcloud-rts' => $Sign['ts'],
                        'x-admiralcloud-hash' => $Sign['hash'],
                    ],
                    'form_params' => $Params['payload']
                ]
            );

            $Result = json_decode($Result->getBody()->getContents(), true);
        } catch(ClientException $e) {
            echo $e->getMessage();
        }
        
        return $Result;
    }
}