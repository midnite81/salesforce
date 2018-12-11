<?php
namespace Midnite81\Salesforce\Services;

class Auth
{
    /**
     * Return authorisation header
     *
     * @return array
     * @throws \Exception
     */
    static public function authorisationHeader()
    {
        if (file_exists(storage_path('output.json'))) {
            $credentials = json_decode(file_get_contents(storage_path('output.json')));
            return ['Authorization' => 'Bearer ' . $credentials->access_token];
        }
        return ['Authorization' => 'Bearer ' . ''];
    }
}