<?php
declare(strict_types=1);

namespace App\Queue\Task;

use App\Model\Table\PagamentosTable;
use App\Model\Table\PessoasTable;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\Log\LogTrait;
use PagSeguro\Domains\AccountCredentials;
use PagSeguro\Domains\Requests\DirectPayment\CreditCard;
use Psr\Log\LogLevel;
use Queue\Queue\Task;

/**
 * CobrarPagamento task.
 *
 * @property \App\Model\Table\PagamentosTable $Pagamentos
 * @property \App\Model\Table\ConfigsTable $Configs
 */
class CobrarPagamentoTask extends Task
{
    use LogTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     * @throws \Exception
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Pagamentos');
        $this->loadModel('Configs');

        $pagamento = $this->Pagamentos->get($data['pagamento_id'], [
            'contain' => [
                'Pedidos' => [
                    'Pessoas' => [
                        'Enderecos' => [
                            'Cidades' => [
                                'Estados',
                            ],
                        ],
                    ],
                    'Objetos' => [
                        'EnderecoEntregas' => [
                            'Cidades' => [
                                'Estados',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        try {
            \PagSeguro\Configuration\Configure::setEnvironment(env('PAGSEGURO_ENV'));

            $creditCard = new CreditCard();

            $creditCard->setReceiverEmail(env('PAGSEGURO_EMAIL'));
            $creditCard->setReference("PAGAMENTO_ {$pagamento->id}");
            $creditCard->setCurrency('BRL');

            // Adicione os itens para esta solicitação de pagamento
            $creditCard->addItems()->withParameters(
                $pagamento->id,
                "Pagamento do pedido {$pagamento->pedido->id} da Montenegro Express",
                1,
                $pagamento->valor,
            );

            // Defina as informações do seu cliente
            $creditCard->setSender()->setName($pagamento->pedido->pessoa->nome);
            $creditCard->setSender()->setEmail($pagamento->pedido->pessoa->email);

            $contato = '';
            if (!empty($pagamento->pedido->pessoa->celular)) {
                $contato = $pagamento->pedido->pessoa->celular;
            } elseif (!empty($pagamento->pedido->pessoa->telefone)) {
                $contato = $pagamento->pedido->pessoa->telefone;
            }

            if (!empty($contato)) {
                $creditCard->setSender()->setPhone()->withParameters(
                    substr($contato, 1, 2),
                    preg_replace('/[^0-9]/', '', substr($contato, 5))
                );
            }

            switch ($pagamento->pedido->pessoa->tipo) {
                case PessoasTable::JURIDICA:
                    $tipo = 'CNPJ';
                    $documento = $pagamento->pedido->pessoa->cnpj;
                    break;
                case PessoasTable::FISICA:
                    $tipo = 'CPF';
                    $documento = $pagamento->pedido->pessoa->cpf;
                    break;
                default:
                    throw new BadRequestException(
                        "Tipo de pessoa não implementado! Tipo: {$pagamento->pedido->pessoa->tipo};"
                    );
            }

            $creditCard->setSender()->setDocument()->withParameters(
                $tipo,
                preg_replace('/[^0-9]/', '', $documento)
            );
            $creditCard->setSender()->setIp($data['ip']);
            $creditCard->setSender()->setHash($data['senderHash']);

            // Defina as informações de envio para esta solicitação de pagamento
            $creditCard->setShipping()->setAddress()->withParameters(
                $pagamento->pedido->objeto->endereco_entrega->logradouro,
                $pagamento->pedido->objeto->endereco_entrega->numero,
                $pagamento->pedido->objeto->endereco_entrega->bairro,
                preg_replace('/[^0-9]/', '', $pagamento->pedido->objeto->endereco_entrega->cep),
                $pagamento->pedido->objeto->endereco_entrega->cidade->nome,
                $pagamento->pedido->objeto->endereco_entrega->cidade->estado->sigla,
                'BRA',
            );

            // Definir informações de faturamento para cartão de crédito
            $creditCard->setBilling()->setAddress()->withParameters(
                $pagamento->pedido->pessoa->endereco->logradouro,
                $pagamento->pedido->pessoa->endereco->numero,
                $pagamento->pedido->pessoa->endereco->bairro,
                preg_replace('/[^0-9]/', '', $pagamento->pedido->pessoa->endereco->cep),
                $pagamento->pedido->pessoa->endereco->cidade->nome,
                $pagamento->pedido->pessoa->endereco->cidade->estado->sigla,
                'BRA',
            );

            // Definir token de cartão de crédito
            $token = $data['token'];
            $creditCard->setToken($token);

            // Defina a quantidade e o valor da parcela
            $creditCard->setInstallment()->withParameters(1, $pagamento->valor);

            // Defina as informações do titular do cartão de crédito
            $dataNascimento = new FrozenDate(str_replace('/', '-', $data['cardDataNascimento']));
            $creditCard->setHolder()->setBirthdate($dataNascimento->format('d/m/Y'));

            // O nome deve ser igual o que está no cartão de crédito
            $creditCard->setHolder()->setName($data['cardNome']);

            $creditCard->setHolder()->setPhone()->withParameters(
                substr($contato, 1, 2),
                preg_replace('/[^0-9]/', '', substr($contato, 5))
            );

            $creditCard->setHolder()->setDocument()->withParameters(
                'CPF',
                preg_replace('/[^0-9]/', '', $data['cardCpf'])
            );

            // Defina o modo de pagamento para esta solicitação de pagamento
            $creditCard->setMode('DEFAULT');

            // Obtenha as credenciais e registre o pagamento com cartão de crédito
            /** @var \PagSeguro\Parsers\Transaction\CreditCard\Response $result */
            $result = $creditCard->register(
                new AccountCredentials(env('PAGSEGURO_EMAIL'), env('PAGSEGURO_TOKEN'))
            );

            if ($result->getStatus()) {
                $pagamento->status = $result->getStatus();
                $pagamento->transaction_code = str_replace('-', '', $result->getCode());

                $this->Pagamentos->saveOrFail($pagamento);
            }
        } catch (\Exception $e) {
            $this->log(
                'Erro ao processar pagamento ENTITY: ' . json_encode($pagamento),
                LogLevel::ERROR,
                ['scope' => ['payments']],
            );
            $this->log(
                'Erro ao processar pagamento MESSAGE: ' . $e->getMessage(),
                LogLevel::ERROR,
                ['scope' => ['payments']],
            );

            $pagamento->status = PagamentosTable::CANCELADA;
            $pagamento->comentario = 'Erro ao processar o pagamento: ' . $e->getMessage();
            $this->Pagamentos->saveOrFail($pagamento);

            throw $e;
        }
    }
}
