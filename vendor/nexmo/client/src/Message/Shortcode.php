<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Message;

use Nexmo\Client\Exception\Exception;
use Nexmo\Message\Shortcode\Alert;
use Nexmo\Message\Shortcode\Marketing;
use Nexmo\Message\Shortcode\TwoFactor;

abstract class Shortcode
{

    protected $to;
    protected $custom;
    protected $options;

    public function __construct($to, array $custom = [], array $options = []) {
        $this->to = $to;
        $this->custom = $custom;
        $this->options = $options;
    }

    public function setCustom($custom) {
        $this->custom = $custom;
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function getType() {
        return $this->type;
    }

    public function getRequestData() {
        // Options, then custom, then to. This is the priority
        // we want so that people can't overwrite to with a custom param
        return $this->options + $this->custom + [
            'to' => $this->to
        ];
    }

    public static function createMessageFromArray($data){
        if (!isset($data['type'])) {
            throw new Exception('No type provided when creating a shortcode message');
        }

        if (!isset($data['to'])) {
            throw new Exception('No to provided when creating a shortcode message');
        }

        $data['type'] = strtolower($data['type']);

        if ($data['type'] === '2fa') {
            $m = new TwoFactor($data['to']);
        } else if ($data['type'] === 'marketing') {
            $m = new Marketing($data['to']);
        } else if ($data['type'] === 'alert') {
            $m = new Alert($data['to']);
        }

        if (isset($data['custom'])) {
            $m->setCustom($data['custom']);
        }

        if (isset($data['options'])) {
            $m->setOptions($data['options']);
        }

        return $m;
    }
}