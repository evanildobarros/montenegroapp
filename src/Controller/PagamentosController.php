<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Database\Expression\QueryExpression;
use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Psr\Log\LogLevel;

/**
 * Class PagamentosController
 *
 * @package App\Controller
 * @property \App\Model\Table\PagamentosTable $Pagamentos
 */
class PagamentosController extends AppController
{
    /**
     * @return \Cake\Http\Response
     * @throws \Exception
     */
    public function notificacoes()
    {
        $notificationCode = $this->getRequest()->getData('notificationCode');
        $notificationType = $this->getRequest()->getData('notificationType');

        $this->log(
            "Notificação: Code: {$notificationCode} | Type: {$notificationType}",
            LogLevel::INFO,
            ['scope' => ['payments']],
        );
        switch ($notificationType) {
            case 'transaction':
                if (env('PAGSEGURO_ENV') == 'sandbox') {
                    $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/';
                } else {
                    $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/';
                }

                $url .= $notificationCode;

                $client = new Client();

                $response = $client->get($url, [
                    'email' => env('PAGSEGURO_EMAIL'),
                    'token' => env('PAGSEGURO_TOKEN'),
                ]);

                $xml = simplexml_load_string($response->getStringBody());
                $result = json_decode(json_encode($xml), true);

                $this->log(
                    'Notificação: Iniciado busca do Pagamento ID ' . $result['code'],
                    LogLevel::INFO,
                    ['scope' => ['payments']],
                );
                /** @var \App\Model\Entity\Pagamento $pagamento */
                $pagamento = $this->Pagamentos
                    ->find()
                    ->contain([
                        'Pedidos' => [
                            'Pessoas',
                        ],
                    ])
                    ->where(function (QueryExpression $expression) use ($result) {
                        $expression->eq('Pagamentos.transaction_code', str_replace('-', '', $result['code']));

                        return $expression;
                    })
                    ->firstOrFail();

                $this->log(
                    "Notificação: Pagamento encontrado ID {$pagamento->id}",
                    LogLevel::INFO,
                    ['scope' => ['payments']],
                );

                $conn = $this->Pagamentos->getConnection();
                try {
                    $conn->begin();

                    $novoPagamento = $this->Pagamentos->newEntity([
                        'pedido_id' => $pagamento->pedido_id,
                        'transaction_code' => $pagamento->transaction_code,
                        'comentario' => $pagamento->comentario,
                        'status' => $result['status'],
                        'valor' => $pagamento->valor,
                    ]);

                    $statusAntigo = $pagamento->status;
                    $statusNovo = $result['status'];
                    $this->log(
                        "Notificação: Trocando status pagamento de antigo: {$statusAntigo} | " .
                        "novo: {$statusNovo}",
                        LogLevel::INFO,
                        ['scope' => ['payments']],
                    );

                    $this->Pagamentos->saveOrFail($novoPagamento);
                    $this->log('Notificação: Status do pagamento alterado', LogLevel::INFO, ['scope' => ['payments']]);

                    $conn->commit();

                    return $this->getResponse()
                        ->withStringBody(json_encode([
                            'id' => $pagamento->id,
                            'statusAntigo' => $statusAntigo,
                            'statusNovo' => $statusNovo,
                            'type' => 'transaction',
                        ]))
                        ->withType('application/json');
                } catch (\Exception $e) {
                    $conn->rollback();
                    $this->log($e->getMessage());
                    throw $e;
                }
                break;
            default:
                $this->log(
                    "Tipo não implementado! Tipo: {$notificationType}; Code: {$notificationCode}",
                    LogLevel::INFO,
                    ['scope' => ['payments']],
                );

                throw new BadRequestException(
                    "Tipo não implementado! Tipo: {$notificationType}; Code: {$notificationCode}"
                );
        }
    }
}
