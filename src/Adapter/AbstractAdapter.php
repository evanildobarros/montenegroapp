<?php
declare(strict_types=1);

namespace App\Adapter;

use App\AdapterInterface;
use Cake\Core\InstanceConfigTrait;

abstract class AbstractAdapter implements AdapterInterface
{
    use InstanceConfigTrait;

    /**
     * Response of the request
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * AbstractAdapter constructor.
     *
     * @param string|array $config The Adapter configuration
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->setConfig($config);

        if ($this->getConfig('api.key') === null) {
            throw new \InvalidArgumentException('No API key set.');
        }
    }

    /**
     * @inheritDoc
     */
    abstract public function send();

    /**
     * @inheritDoc
     */
    public function response()
    {
        return $this->response;
    }
}
