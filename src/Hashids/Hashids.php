<?php
declare(strict_types=1);

namespace App\Hashids;

use Cake\Core\Configure;

class Hashids
{
    private static $instance = null;
    private $hashids;

    //\App\Hashids\Hashids::getInstance()->decode();

    /**
     * @param int $minLength Tamanho mínimo
     * @param string $alphabet Alfabeto
     */
    public function __construct(
        int $minLength = 10,
        string $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    ) {
        $this->hashids = new \Hashids\Hashids(Configure::read('Security.salt'), $minLength, $alphabet);
    }

    /**
     * getInstance method
     * Cria instância
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Encode parameters to generate a hash.
     *
     * @param mixed $numbers Number parameter to generate a hash
     * @return string Return
     */
    public function encode(...$numbers): string
    {
        return $this->hashids->encode($numbers);
    }

    /**
     * Decode a hash to the original parameter values.
     *
     * @param string $hash hash
     * @return array Return
     */
    public function decode(string $hash): array
    {
        return $this->hashids->decode($hash);
    }
}
