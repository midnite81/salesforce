<?php

if ( ! function_exists('sf_config')) {

    /**
     * Get the environment specific config value
     */
    function sf_config($key)
    {
        $config = config('midnite-salesforce');
        $env = config('midnite-salesforce.environment');

        if (empty ($env)) {
            return null;
        }

        if (array_key_exists($env, $config['environments'])) {
            return $config['environments'][$env][$key];
        }

        return null;
    }

}