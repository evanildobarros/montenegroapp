<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\InternalErrorException;
use Firebase\JWT\JWT;

/**
 * Class JwksController
 *
 * @package App\Controller
 */
class JwksController extends AppController
{
    /**
     * Chaves para o JWT
     *
     * @return void
     */
    public function index(): void
    {
        if (!file_exists(CONFIG . 'jwt.pem')) {
            throw new InternalErrorException('Chave pública do JWT não encontrada!');
        }
        $pubKey = file_get_contents(CONFIG . 'jwt.pem');
        $res = openssl_pkey_get_public($pubKey);
        $detail = openssl_pkey_get_details($res);
        $key = [
            'kty' => 'RSA',
            'alg' => 'RS512',
            'use' => 'sig',
            'e' => JWT::urlsafeB64Encode($detail['rsa']['e']),
            'n' => JWT::urlsafeB64Encode($detail['rsa']['n']),
        ];
        $keys['keys'][] = $key;

        $this->set(compact('keys'));
        $this->set('_serialize', 'keys');
    }
}
