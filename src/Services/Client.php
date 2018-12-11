<?php

namespace Midnite81\Salesforce\Services;

class Client
{
    protected $guzzle;

    public function __construct()
    {
        $this->guzzle = app(\GuzzleHttp\Client::class);
    }

    /**
     * Request
     *
     * @param      $url
     * @param null $data
     * @param null $headers
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function request($url, $data = null, $headers = null)
    {

        if (! empty($headers)) {
            $headers = ['headers' => $headers];
        }

        if (!empty($data)) {
            return $this->post($url, $data, $headers);
        }

        return $this->get($url, $headers);
    }

    /**
     * Get Request
     *
     * @param      $url
     * @param null $headers
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function get($url, $headers = null)
    {
        $request = $this->guzzle->get($url, $headers);

        return $request;
    }

    /**
     * Post Request
     *
     * @param      $url
     * @param null $data
     * @param null $headers
     * @return mixed
     */
    public function post($url, $data = null, $headers = null)
    {
        $data = (!empty($data)) ? ['json' => $data] : [];

        if (! empty($headers)) {
            $data = array_merge($data, $headers);
        }

        $request = $this->guzzle->post($url, $data);

        return $request;
    }

    /**
     * Post Request
     *
     * @param      $url
     * @param null $data
     * @param null $headers
     * @return mixed
     */
    public function patch($url, $data = null, $headers = null)
    {
        if (! empty($headers)) {
            $headers = ['headers' => $headers];
        }

        $data = (!empty($data)) ? ['json' => $data] : [];

        if (!empty($headers)) {
            $data = array_merge($data, $headers);
        }

        $request = $this->guzzle->patch($url, $data);

        return $request;
    }
}