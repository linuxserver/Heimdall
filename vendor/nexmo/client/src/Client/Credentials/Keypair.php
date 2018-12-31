<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Credentials;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Nexmo\Application\Application;

class Keypair extends AbstractCredentials  implements CredentialsInterface
{

    protected $key;

    protected $signer;

    public function __construct($privateKey, $application = null)
    {
        $this->credentials['key'] = $privateKey;
        if($application){
            if($application instanceof Application){
                $application = $application->getId();
            }

            $this->credentials['application'] = $application;
        }

        $this->key = new Key($privateKey);
        $this->signer = new Sha256();
    }

    public function generateJwt(array $claims = [])
    {
        $exp = time() + 60;
        $iat = time();
        $jti = base64_encode(mt_rand());

        if(isset($claims['exp'])){
            $exp = $claims['exp'];
            unset($claims['exp']);
        }

        if(isset($claims['iat'])){
            $iat = $claims['iat'];
            unset($claims['iat']);
        }

        if(isset($claims['jti'])){
            $jti = $claims['jti'];
            unset($claims['jti']);
        }

        $builder = new Builder();
        $builder->setIssuedAt($iat)
                ->setExpiration($exp)
                ->setId($jti);


        if(isset($claims['nbf'])){
            $builder->setNotBefore($claims['nbf']);
            unset($claims['nbf']);
        }

        if(isset($this->credentials['application'])){
            $builder->set('application_id', $this->credentials['application']);
        }

        if(!empty($claims)){
            foreach($claims as $claim => $value){
                $builder->set($claim, $value);
            }
        }

        return $builder->sign($this->signer, $this->key)->getToken();
    }
}