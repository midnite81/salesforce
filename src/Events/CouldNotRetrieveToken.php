<?php

namespace Midnite81\Salesforce\Events;

use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;


class CouldNotRetrieveToken 
{
    use SerializesModels;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * Create a new event instance.
     *
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function getException()
    {
        return $this->exception;
    }
}