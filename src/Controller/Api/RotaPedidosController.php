<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Table\PedidosTable;
use App\Model\Table\RotaPedidosTable;
use App\Model\Table\RotasTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Crud\Error\Exception\ValidationException;

/**
 * Class RotaPedidosController
 *
 * @property \App\Model\Table\RotaPedidosTable $RotaPedidos
 */
class RotaPedidosController extends AppController
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
     * Parada method
     * View da table rota_pedidos
     *
     * @param int $id Id rota_pedidos
     * @return void
     */
    public function view($id)
    {
        $this->getRequest()->allowMethod('get');

        /** @var \App\Model\Entity\RotaPedido $parada */
        $parada = $this->RotaPedidos
            ->find()
            ->select([
                'RotaPedidos.id',
                'RotaPedidos.parent_id',
                'RotaPedidos.tipo',
                'RotaPedidos.entregue',
                'Rotas.id',
                'Rotas.status',
                'Rotas.entregador_id',
                'Pedidos.id',
                'Pedidos.instrucoes',
                'Objetos.id',
                'Objetos.observacoes',
                'Objetos.endereco_entrega_id',
                'Objetos.endereco_coleta_id',
                'Objetos.nome_destinatario',
                'Objetos.telefone_destinatario',
                'Objetos.celular_destinatario',
                'Pessoas.id',
                'Pessoas.nome',
            ])
            ->contain([
                'Rotas',
                'Pedidos' => [
                    'Objetos',
                    'Pessoas',
                ],
                'Tentativas' => function (Query $query) {
                    return $query->orderDesc('data');
                },
            ])
            ->where(function (QueryExpression $expression) use ($id) {
                $expression
                    ->eq('RotaPedidos.id', $id);

                return $expression;
            })
            ->firstOrFail();

        $this->Authorization->authorize($parada);

        if ($parada->tipo === RotaPedidosTable::COLETA) {
            $endereco_id = $parada->pedido->objeto->endereco_coleta_id;
        } else {
            $endereco_id = $parada->pedido->objeto->endereco_entrega_id;
        }

        $endereco = $this->RotaPedidos->Rotas->Pessoas->Enderecos->get($endereco_id, [
            'contain' => [
                'Cidades' => [
                    'Estados',
                ],
            ],
        ]);

        $parada->endereco = $endereco;

        $quantidadeTentativas = (int)$this->Configs->parametro('quantidade_tentativas');
        $tentativasRealizadas = count($parada->tentativas) + 1;

        $parada->quantidade_tentativas = $quantidadeTentativas;
        $parada->tentativas_realizadas = $tentativasRealizadas;

        $result = [
            'success' => true,
            'parada' => $parada,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Entregar method
     * Marca o objeto como entregue
     *
     * @param int $id Id rota_pedidos
     * @return void
     */
    public function entregar($id)
    {
        $this->getRequest()->allowMethod('post');

        /** @var \App\Model\Entity\RotaPedido $rotaPedido */
        $rotaPedido = $this->RotaPedidos
            ->find()
            ->contain([
                'ParentRotaPedidos',
                'Pedidos',
                'Rotas',
            ])
            ->where(function (QueryExpression $expression) use ($id) {
                $expression
                    ->eq('RotaPedidos.id', $id);

                return $expression;
            })
            ->firstOrFail();

        $this->Authorization->authorize($rotaPedido);

        if ($rotaPedido->entregue) {
            throw new BadRequestException('Atenção! Este objeto já foi entregue e/ou coletado');
        }

        if (isset($rotaPedido->parent_rota_pedido) && (!$rotaPedido->parent_rota_pedido->entregue)) {
            throw new BadRequestException('Atenção! Este objeto precisa ser coletado primeiro.');
        }

        $conn = $this->RotaPedidos->getConnection();

        try {
            $conn->begin();
            $data = $this->getRequest()->getData();

            $rotaPedidoAtualizada = [
                'entregue' => true,
                'pedido' => [
                    'nome_recebedor' => $data['pedido']['nome_recebedor'],
                    'documento_recebedor' => $data['pedido']['documento_recebedor'],
                ],
            ];

            if ($rotaPedido->tipo === RotaPedidosTable::COLETA) {
                $rotaPedidoAtualizada['pedido']['data_chegada'] = new FrozenTime();
                $rotaPedidoAtualizada['pedido']['status'] = PedidosTable::PROCESSO_ENTREGA;
            } else {
                $rotaPedidoAtualizada['pedido']['data_entrega'] = new FrozenTime();
                $rotaPedidoAtualizada['pedido']['status'] = PedidosTable::FINALIZADO;
            }

            if (isset($data['pedido']['comprovante'])) {
                $rotaPedidoAtualizada['pedido']['comprovante'] = $data['pedido']['comprovante'];
            }

            $rotaPedido = $this->RotaPedidos->patchEntity($rotaPedido, $rotaPedidoAtualizada);
            $this->RotaPedidos->saveOrFail($rotaPedido);

            if (in_array($rotaPedido->rota->status, [RotasTable::ATRASADA, RotasTable::AGUARDANDO_INICIO])) {
                $rota = $rotaPedido->rota;
                $rota->status = RotasTable::EM_ROTA;
                $this->RotaPedidos->Rotas->saveOrFail($rota);
                $rotaPedido->rota = $rota;
            }

            if ($rotaPedido->rota->status === RotasTable::EM_ROTA) {
                $temRotasFazer = $this->RotaPedidos
                    ->find()
                    ->leftJoinWith('Tentativas')
                    ->where(function (QueryExpression $expression) use ($rotaPedido) {
                        $expression
                            ->eq('RotaPedidos.entregue', false)
                            ->eq('RotaPedidos.rota_id', $rotaPedido->rota_id);

                        return $expression;
                    })
                    ->limit(1)
                    ->count();

                if ($temRotasFazer === 0) {
                    $rota = $rotaPedido->rota;
                    $rota->status = RotasTable::FINALIZADA;
                    $this->RotaPedidos->Rotas->saveOrFail($rota);
                }
            }

            /** @var \App\Model\Entity\Pedido $pedido */
            $pedido = $this->RotaPedidos->Pedidos->get($rotaPedido->pedido_id, [
                'contain' => [
                    'Objetos' => [
                        'EnderecoEntregas' => [
                            'Cidades' => [
                                'Estados',
                            ],
                        ],
                        'EnderecoColetas' => [
                            'joinType' => 'LEFT',
                            'Cidades' => [
                                'joinType' => 'LEFT',
                                'Estados' => [
                                    'joinType' => 'LEFT',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($rotaPedido->tipo === RotaPedidosTable::ENTREGA) {
                $titulo = 'Objeto entregue';
                $descricao = 'Objeto entregue em: ' . $pedido->objeto->endereco_entrega->endereco_formatado;

                $pedido->data_entrega = new FrozenTime();
                $pedido->status = PedidosTable::FINALIZADO;
            } else {
                $titulo = 'Objeto coletado';
                $descricao = 'Objeto coletado em: ' . $pedido->objeto->endereco_coleta->endereco_formatado;

                $pedido->data_chegada = new FrozenTime();
                $pedido->status = PedidosTable::PROCESSO_ENTREGA;
            }
            // Salvar pedidos
            $this->RotaPedidos->Pedidos->saveOrFail($pedido);

            // ADICIONAR ATUALIZAÇÃO DO PEDIDO
            $atualizacao = [
                'pedido_id' => $pedido->id,
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data' => new FrozenTime(),
            ];

            $this->RotaPedidos->Pedidos->Atualizacoes->add($atualizacao);

            $conn->commit();
        } catch (PersistenceFailedException $e) {
            $conn->rollback();
            $this->log("Erro em finalizar a {$rotaPedido->tipo}: " . $e->getMessage());
            throw new ValidationException($e->getEntity());
        }

        $results = [
            'success' => true,
            'data' => $rotaPedido,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', ['results']);
    }
}
