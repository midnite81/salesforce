<?php

namespace Midnite81\Salesforce\Commands;

use GuzzleHttp\Client;
use Midnite81\Salesforce\Services\Json;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Exception;
use SforceBaseClient;
use SforceEnterpriseClient;

class GetToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salesforce:get-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authorises credentials and creates a token';

    protected $client;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Client
     */
    protected $guzzle;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @internal param Client $client
     * @param Request            $request
     * @param Client $guzzle
     * @param Filesystem         $filesystem
     */
    public function __construct(Request $request, Client $guzzle, Filesystem $filesystem)
    {
        parent::__construct();

        $this->request = $request;
        $this->guzzle = $guzzle;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $clientId = sf_config('consumer_key');
        $clientSecret = sf_config('consumer_secret');
        $username = sf_config('username');
        $password = base64_decode(sf_config('password'));

        $params = $this->getCredentialParams($clientId, $clientSecret, $username, $password);

        try {
            $request = $this->requestData($this->get('/services/oauth2/token'), $params);
        } catch (\Exception $e) {
            $this->info('ClientId ' . $clientId);
            $this->info('ClientSecret ' . $clientSecret);
            $this->info('Username ' . $username);
            $this->info('Password ' . $password . "\r\n\r\n");

            $this->warn('Request returned an error');
            $this->warn('Message: ' . $e->getMessage());
            $this->warn('Trace' . $e->getTraceAsString());
            return false;
        }

        $this->info('Request successful');

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

        $this->filesystem->put(storage_path('output.json'), Json::prettyJson($contents));


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