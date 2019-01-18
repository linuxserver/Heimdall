<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Credentials;

class Container extends AbstractCredentials implements CredentialsInterface
{
    protected $types = [
        Basic::class,
        SignatureSecret::class,
        Keypair::class
    ];

    protected $credentials;

    public function __construct($credentials)
    {
        if(!is_array($credentials)){
            $credentials = func_get_args();
        }

        foreach($credentials as $credential){
            $this->addCredential($credential);
        }
    }

    protected function addCredential(CredentialsInterface $credential)
    {
        $type = $this->getType($credential);
        if(isset($this->credentials[$type])){
            throw new \RuntimeException('can not use more than one of a single credential type');
        }

        $this->credentials[$type] = $credential;
    }

    protected function getType(CredentialsInterface $credential)
    {
        foreach ($this->types as $type) {
            if($credential instanceof $type){
                return $type;
            }
        }
    }

    public function get($type)
    {
        if(!isset($this->credentials[$type])){
            throw new \RuntimeException('credental not set');
        }

        return $this->credentials[$type];
    }

    public function has($type)
    {
        return isset($this->credentials[$type]);
    }

    public function generateJwt($claims) {
        return $this->credentials[Keypair::class]->generateJwt($claims);
    }

}