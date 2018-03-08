<?php

namespace Midnite81\Salesforce;

use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Midnite81\Salesforce\Services\Json;

class Salesforce
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct()
    {
        $client = app(Client::class);
        $filesystem = app(Illuminate\Filesystem\Filesystem::class);
    }

    /**
     * Get an authorisation token
     */
    public function authorise()
    {
        $clientId = sf_config('consumer_key');
        $clientSecret = sf_config('consumer_secret');
        $username = sf_config('username');
        $password = base64_decode(sf_config('password'));

        $params = $this->getCredentialParams($clientId, $clientSecret, $username, $password);

        $request = $this->requestData($this->get('/services/oauth2/token'), $params);

        $this->save($request);
    }

    /**
     * @param $request
     */
    protected function save($request)
    {
        $contents = $request->getBody()->getContents();

        $this->filesystem->put(storage_path('output.json'), Json::prettyJson($contents));
    }

    /**
     * Request Data
     *
     * @param       $url
     * @param array $data
     * @return string
     */
    protected function requestData($url, $data = [])
    {
        if (!empty($data)) {
            $request = $this->guzzle->post($url, [
                'form_params' => $data
            ]);
        } else {
            $request = $this->guzzle->get($url);
        }

        $contents = $request->getBody()->getContents();

        return $contents;
    }

    /**
     * Generate the params for auth request
     *
     * @param $clientId
     * @param $clientSecret
     * @param $username
     * @param $password
     * @return array
     */
    protected function getCredentialParams($clientId, $clientSecret, $username, $password)
    {
        return [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * Construct the url
     *
     * @param $value
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function get($value)
    {
        return sf_config('sf_base_url') . $value;
    }

}