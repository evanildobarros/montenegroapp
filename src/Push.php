<?php
declare(strict_types=1);

namespace App;

class Push
{
    /**
     * @var \App\AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param \App\AdapterInterface $adapter The adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get the Adapter.
     *
     * @return \App\AdapterInterface adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Send a downstream message to one or more devices.
     *
     * @return bool
     */
    public function send()
    {
        return $this->getAdapter()->send();
    }

    /**
     * Return the response of the push.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function response()
    {
        return $this->getAdapter()->response();
    }
}
