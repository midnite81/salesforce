<?php

namespace Midnite81\Salesforce\Client;

class Client
{
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }


}