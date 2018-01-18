<?php

namespace Midnite81\Salesforce\Model;

use App\Services\Auth;
use GuzzleHttp\Client;
use Midnite81\Salesforce\Exceptions\ConnectionNotSetException;

abstract class Model
{
    /**
     * The attributes which get filled on the model
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Returns the url for this object
     *
     * @return string
     */
    abstract public function objectUrl();

    /**
     * Required fields for create
     *
     * @return array
     */
    abstract protected function requiredFields();

    /**
     * The fillable fields for the object
     *
     * @return array
     */
    abstract protected function fillableFields();

    public function __construct($attributes = [])
    {
        $this->fillAttributes($attributes);
    }

    public function find($id)
    {
        $describeUrl = $this->getConnection() . '/' . $id;

        try {
            $client = new \App\Services\Client();
            $response = $client->request($describeUrl, null, Auth::authorisationHeader());
        } catch (\Exception $e) {
            return 'Could not retrieve data: ' . $e->getMessage() . $e->getTraceAsString();

        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Fill Attributes
     *
     * @param $data
     */
    protected function fillAttributes(array $data = [])
    {
        if (! empty($data)) {
            $this->attributes = array_merge($this->fillableFields(), $data);
        } else {
            $this->attributes = $this->fillableFields();
        }

    }


    /**
     * Get Connection String
     *
     * @return string
     * @throws ConnectionNotSetException
     */
    public function getConnection()
    {
        if (! empty($this->objectUrl())) {
            return $this->objectUrl();
        }

        throw new ConnectionNotSetException('The objectUrl has not been set on the class');
    }

    /**
     * Get Object Name
     *
     * @return string
     */
    public function getObjectName()
    {
        return basename($this->objectUrl());
    }

    /**
     * Get primary key
     *
     * @return bool|null
     */
    public function getId()
    {
        return (! empty($this->attributes['id'])) ?? null;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function save()
    {
        $client = new \App\Services\Client();
        try {
            $response = $client->request($this->objectUrl(), $this->attributes, Auth::authorisationHeader());
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }

        dd($response->getBody()->getContents());
    }

    /**
     * ToString
     *
     * @return bool|null
     */
    public function __toString()
    {
        return $this->getId();
    }

    /**
     * Get the definition of the object
     *
     * @return string
     */
    public function describe()
    {
        $describeUrl = config('salesforce.instance') . '/services/data/v20.0/sobjects/' . $this->getObjectName() . '/describe';

        try {
            $client = new \App\Services\Client();
            $response = $client->request($describeUrl, null, Auth::authorisationHeader());
        } catch (\Exception $e) {
            return 'Could not retrieve data: ' . $e->getMessage() . $e->getTraceAsString();

        }

        return $response->getBody()->getContents();
    }


}