<?php
namespace Nexmo\Message;

class Query
{
    protected $params = [];

    public function __construct(\DateTime $date, $to)
    {
        $this->params['date'] = $date->format('Y-m-d');
        $this->params['to']   = $to;
    }

    public function getParams()
    {
        return $this->params;
    }
}