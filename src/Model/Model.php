<?php

namespace Midnite81\Salesforce\Model;

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
     * Get primary key
     *
     * @return bool|null
     */
    public function getId()
    {
        return (! empty($this->attributes['id'])) ?? null;
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


}