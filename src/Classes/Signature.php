<?php

namespace Sioweb\AcSignature\Classes;

class Signature
{
    public function sign($params)
    {
        $requiredParams = [
            'accessSecret',
            'controller',
            'action',
        ];

        foreach($requiredParams as $Parameter) {
            if(empty($params[$Parameter])) {
                throw new MissingParameterException($Parameter);
            }
        }
        
        if(!empty($params['payload']) && gettype($params['payload']) !== 'array') {
            throw new DatatypeException('Payload', 'array', gettype($params['payload']));
        }

        $valueToHash = [
            strtolower($params['controller']),
            strtolower($params['action']),
            strtolower($params['ts'])
        ];

        if(!empty($params['payload'])) {
            $valueToHash[] = json_encode($params['payload']);
        }

        $valueToHash = implode("\n", $valueToHash);

        $hash = hash_hmac('sha256', $valueToHash, $params['accessSecret']);

        return [
            'hash' => $hash,
            'ts' => $params['ts']
        ];
    }
}