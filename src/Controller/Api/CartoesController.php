<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use PagSeguro\Domains\AccountCredentials;
use PagSeguro\Services\Session;

/**
 * Cartoes Controller
 */
class CartoesController extends AppController
{
    /**
     * getBin method
     *
     * Busca a bandeira do cartao com base nos 6 primeiros digitos
     *
     * @return void
     * @throws \Exception
     */
    public function getBin()
    {
        $bin = substr($this->getRequest()->getData('numero'), 0, 6);

        if (strlen($bin) != 6 || !is_numeric($bin)) {
            throw new BadRequestException('BIN inválido!');
        }
        $credential = new AccountCredentials(env('PAGSEGURO_EMAIL'), env('PAGSEGURO_TOKEN'));
        /** @var \PagSeguro\Parsers\Session\Response $session */
        $session = Session::create($credential);

        $client = new Client();

        $bandeiraResponse = $client->get('https://df.uol.com.br/df-fe/mvc/creditcard/v1/getBin', [
            'tk' => $session->getResult(),
            'creditCard' => $bin,
        ]);

        $binInfo = json_decode($bandeiraResponse->getStringBody(), true);

        if (!isset($binInfo['bin']['brand']['name'])) {
            $this->log('Erro BIN Cartão: ' . serialize($binInfo));
            if (isset($binInfo['bin']['reasonMessage'])) {
                throw new BadRequestException('BIN do cartão desconhecido!');
            }
            if (isset($binInfo['safeCheckoutResponse']['reasonMessage'])) {
                throw new BadRequestException($binInfo['safeCheckoutResponse']['reasonMessage']);
            }
            throw new BadRequestException('Erro desconhecido!');
        }

        $success = true;
        $result = $binInfo['bin']['brand']['name'];

        $this->set(compact('success', 'result'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'result']);
    }
}
