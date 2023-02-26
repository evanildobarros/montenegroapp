<?php
declare(strict_types=1);

namespace App;

interface AdapterInterface
{
    /**
     * Send a request
     *
     * @return bool
     */
    public function send();

    /**
     * Return the response of the request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function response();
}
