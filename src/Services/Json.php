<?php

namespace Midnite81\Salesforce\Services;

class Json
{
    /**
     * Return pretty json
     *
     * @param $contents
     * @return string
     */
    public static function prettyJson($contents)
    {
        return json_encode(json_decode($contents), JSON_PRETTY_PRINT);
    }
}