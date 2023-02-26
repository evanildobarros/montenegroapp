<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Crud\Error\Exception\ValidationException;

/**
 * Class PessoasController
 *
 * @property \App\Model\Table\TentativasTable $Tentativas
 */
class TentativasController extends AppController
{
    /**
     * Initialize controller
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Add method
     *
     * @param int $rota_pedido_id Id da rota_pedido
     * @return void
     */
    public function add($rota_pedido_id)
    {
        $rotaPedido = $this->Tentativas->RotaPedidos->get($rota_pedido_id, [
            'contain' => [
                'Pedidos',
                'Tentativas',
            ],
        ]);

        if ($rotaPedido->entregue) {
            throw new BadRequestException('Atenção! Este produto já foi entregue e/ou coletado');
        }

        $quantidadeTentativas = $this->Configs->parametro('quantidade_tentativas');
        $tentativasRealizadas = count($rotaPedido->tentativas) + 1;
        if ($tentativasRealizadas > $quantidadeTentativas) {
            throw new BadRequestException('Atenção! Este pedido não aceita mais tentativas de entrega');
        }

        $tentativa = $this->Tentativas->newEmptyEntity();
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Tentativas->getConnection();

            try {
                $conn->begin();
                $data = $this->getRequest()->getData();
                $data['data'] = new FrozenTime();
                $data['rota_pedido_id'] = $rotaPedido->id;

                $tentativa = $this->Tentativas->patchEntity($tentativa, $data);
                $this->Tentativas->saveOrFail($tentativa);

                $atualizacao = [
                    'pedido_id' => $rotaPedido->pedido_id,
                    'titulo' => "Tentativa de {$rotaPedido->tipo_formatado}",
                    'descricao' => "Tentativa de entrega no dia {$tentativa->data->format('d/m/Y')}; " .
                        "/n Motivo: {$tentativa->nome_motivo}; /n Observações: {$tentativa->observacoes}",
                    'data' => new FrozenTime(),
                ];
                $this->Tentativas->RotaPedidos->Pedidos->Atualizacoes->add($atualizacao);

                $conn->commit();
            } catch (PersistenceFailedException $e) {
                $conn->rollback();
                $this->log('Erro em finalizar tentativa: ' . $e->getMessage());
                throw new ValidationException($e->getEntity());
            }
        }

        $success = true;
        $results = [
            'quantidade_tentativas' => (int)$quantidadeTentativas,
            'tentativas_realizadas' => $tentativasRealizadas,
        ];
        $this->set(compact('success', 'results'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'results']);
    }
}
