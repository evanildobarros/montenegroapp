<?php
declare(strict_types=1);

namespace App\Pagamentos;

use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use PagSeguro\Domains\AccountCredentials;
use PagSeguro\Services\Session;

class Cartao
{
    private $numero;
    private $bandeira;
    private $mes;
    private $ano;
    private $cvv;

    /**
     * @param string $numero Numero do cartão
     * @param string $bandeira Bandeira
     * @param string $mes Mes de vencimento
     * @param string $ano Ano do vencimento
     * @param string $cvv Código de verificação
     */
    public function __construct(string $numero, string $bandeira, string $mes, string $ano, string $cvv)
    {
        $this->numero = $numero;
        $this->bandeira = $bandeira;
        $this->mes = $mes;
        $this->ano = $ano;
        $this->cvv = $cvv;
    }

    /**
     * @return string
     */
    public function getNumero(): string
    {
        return $this->numero;
    }

    /**
     * @param string $numero Numero do cartao
     * @return \App\Pagamentos\Cartao Return
     */
    public function setNumero(string $numero): Cartao
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return string
     */
    public function getBandeira(): string
    {
        return $this->bandeira;
    }

    /**
     * @param string $bandeira Bandeira do cartão
     * @return \App\Pagamentos\Cartao Return
     */
    public function setBandeira(string $bandeira): Cartao
    {
        $this->bandeira = $bandeira;

        return $this;
    }

    /**
     * @return string
     */
    public function getMes(): string
    {
        return $this->mes;
    }

    /**
     * @param string $mes Mes
     * @return \App\Pagamentos\Cartao Return
     */
    public function setMes(string $mes): Cartao
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * @return string
     */
    public function getAno(): string
    {
        return $this->ano;
    }

    /**
     * @param string $ano Ano
     * @return \App\Pagamentos\Cartao Return
     */
    public function setAno(string $ano): Cartao
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvv(): string
    {
        return $this->cvv;
    }

    /**
     * @param string $cvv CVV
     * @return \App\Pagamentos\Cartao Return
     */
    public function setCvv(string $cvv): Cartao
    {
        $this->cvv = $cvv;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function token(): string
    {
        $pagSeguroEmail = env('PAGSEGURO_EMAIL');
        $pagSeguroToken = env('PAGSEGURO_TOKEN');

        $credential = new AccountCredentials(env('PAGSEGURO_EMAIL'), env('PAGSEGURO_TOKEN'));
        /** @var \PagSeguro\Parsers\Session\Response $session */
        $session = Session::create($credential);

        $query = http_build_query([
            'email' => $credential->getEmail(),
            'token' => $credential->getToken(),
        ]);

        $client = new Client();

        $cardResponse = $client->post(
            "https://df.uol.com.br/v2/cards?{$query}",
            [
                'sessionId' => $session->getResult(),
                'cardNumber' => $this->numero,
                'cardBrand' => $this->bandeira,
                'cardCvv' => $this->cvv,
                'cardExpirationMonth' => $this->mes,
                'cardExpirationYear' => $this->ano,
            ]
        );
        $xmlString = $cardResponse->getStringBody();

        $response = json_decode(json_encode(simplexml_load_string($xmlString)), true);

        if (isset($response['error'])) {
            throw new BadRequestException($response['error']['message']);
        }

        return $response['token'];
    }

    public function senderHash(): string
    {
        $pagSeguroEmail = env('PAGSEGURO_EMAIL');
        $pagSeguroToken = env('PAGSEGURO_TOKEN');

        $credential = new AccountCredentials(env('PAGSEGURO_EMAIL'), env('PAGSEGURO_TOKEN'));
        /** @var \PagSeguro\Parsers\Session\Response $session */
        $session = Session::create($credential);

        $query = http_build_query([
            'email' => $credential->getEmail(),
            'token' => $credential->getToken(),
        ]);

        $client = new Client();

        $response = $client->get(
            "https://pagseguro.uol.com.br/checkout/direct-payment/i-ck.html",
            [
                'sessionId' => $session->getResult(),
            ]
        );

        $senderHash = [];

        preg_match('/\w{64}/', $response->getBody()->getContents(), $senderHash);

        return $senderHash[0];
    }
}
