<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Entity\RotaPedido;
use App\Model\Table\RotaPedidosTable;
use App\Model\Table\RotasTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\ORM\Exception\PersistenceFailedException;
use Crud\Error\Exception\ValidationException;

/**
 * Class RotaPedidosController
 *
 * @property \App\Model\Table\RotasTable $Rotas
 */
class RotasController extends AppController
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
     * Index method
     * Retorna a rota do dia
     *
     * @return void
     */
    public function index()
    {
        $this->getRequest()->allowMethod('get');
        $data = $this->getRequest()->getQueryParams();

        // Busca Rota do dia
        $queryRota = $this->Rotas
            ->find()
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Rotas.data_saida', new FrozenDate());

                return $expression;
            });

        $queryRota = $this->Authorization->applyScope($queryRota);
        $rota = $queryRota->first();

        $paradas = [];
        $paradas_concluidas = 0;
        $paradas_restantes = 0;

        if (!empty($rota)) {
            // Busca as paradas que foram entregues
            $paradas_concluidas = $this->Rotas->RotaPedidos
                ->find()
                ->where(function (QueryExpression $expression) use ($rota) {
                    $expression
                        ->eq('RotaPedidos.entregue', true)
                        ->eq('RotaPedidos.rota_id', $rota->id);

                    return $expression;
                })
                ->count();

            // Busca as paradas que não foram entregues
            $paradas_restantes = $this->Rotas->RotaPedidos
                ->find()
                ->where(function (QueryExpression $expression) use ($rota) {
                    $expression
                        ->eq('RotaPedidos.entregue', false)
                        ->eq('RotaPedidos.rota_id', $rota->id);

                    return $expression;
                })
                ->count();

            // Busca as paradas da rota do dia
            $this->Rotas->RotaPedidos
                ->find()
                ->contain([
                    'Pedidos' => [
                        'Objetos' => [
                            'EnderecoEntregas' => [
                                'Cidades' => [
                                    'Estados',
                                ],
                            ],
                            'EnderecoColetas' => [
                                'Cidades' => [
                                    'joinType' => 'LEFT',
                                    'Estados' => [
                                        'joinType' => 'LEFT',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'Tentativas',
                ])
                ->where(function (QueryExpression $expression) use ($rota, $data, &$paradas) {
                    $expression->eq('RotaPedidos.rota_id', $rota->id);

                    if (isset($data['pedido_id'])) {
                        $expression->eq('RotaPedidos.pedido_id', $data['pedido_id']);
                    }
                    if (isset($data['entregue'])) {
                        $expression->eq('RotaPedidos.entregue', $data['entregue'], 'boolean');
                    }

                    return $expression;
                })
                ->orderAsc('RotaPedidos.ordem')
                ->each(function (RotaPedido $rotaPedido) use (&$paradas) {
                    $parada = [
                        'ordem' => $rotaPedido->ordem,
                        'rota_pedido_id' => $rotaPedido->id,
                        'pedido_id' => $rotaPedido->pedido_id,
                        'tipo' => $rotaPedido->tipo,
                        'tipo_formatado' => $rotaPedido->tipo_formatado,
                        'entregue' => $rotaPedido->entregue,
                        'tentativa' => !empty($rotaPedido->tentativas),
                    ];

                    if ($rotaPedido->tipo === RotaPedidosTable::COLETA) {
                        $parada['endereco_formatado'] =
                            $rotaPedido->pedido->objeto->endereco_coleta->endereco_formatado;
                    } else {
                        $parada['endereco_formatado'] =
                            $rotaPedido->pedido->objeto->endereco_entrega->endereco_formatado;
                    }

                    $paradas[] = $parada;
                });
        }

        $success = true;
        $results = [
            'rota' => $rota,
            'paradas_concluidas' => $paradas_concluidas,
            'paradas_restantes' => $paradas_restantes,
            'paradas' => $paradas,
        ];

        $this->set(compact('success', 'results'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'results']);
    }

    /**
     * Iniciar method
     * Muda status da rota para EM ROTA
     *
     * @param int $rota_id Id da rota
     * @return void
     */
    public function iniciar($rota_id)
    {
        $this->getRequest()->allowMethod(['post']);

        /** @var \App\Model\Entity\Rota $rota */
        $queryRota = $this->Rotas
            ->find()
            ->where(function (QueryExpression $expression) use ($rota_id) {
                $expression
                    ->eq('Rotas.id', $rota_id);

                return $expression;
            });

        $queryRota = $this->Authorization->applyScope($queryRota);
        $rota = $queryRota->firstOrFail();

        $conn = $this->Rotas->getConnection();
        try {
            $conn->begin();

            if (in_array($rota->status, [RotasTable::AGUARDANDO_INICIO, RotasTable::ATRASADA])) {
                $rota->status = RotasTable::EM_ROTA;
                $this->Rotas->saveOrFail($rota);
            } else {
                throw new BadRequestException('Atenção, está rota já está iniciada ou finalizada');
            }

            $conn->commit();
        } catch (PersistenceFailedException $e) {
            $conn->rollback();
            $this->log('Erro ao iniciar rota: ' . $e->getMessage());
            throw new ValidationException($e->getEntity());
        }

        $success = true;

        $this->set(compact('success', 'rota'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'rota']);
    }

    /**
     * Finalizar method
     * Muda status da rota para FINALIZADO
     *
     * @param int $rota_id Id da rota
     * @return void
     */
    public function finalizar($rota_id)
    {
        $this->getRequest()->allowMethod(['post']);

        /** @var \App\Model\Entity\Rota $rota */
        $queryRota = $this->Rotas
            ->find()
            ->where(function (QueryExpression $expression) use ($rota_id) {
                $expression
                    ->eq('Rotas.id', $rota_id);

                return $expression;
            });

        $queryRota = $this->Authorization->applyScope($queryRota);
        $rota = $queryRota->firstOrFail();

        $conn = $this->Rotas->getConnection();
        try {
            $conn->begin();

            if ($rota->status === RotasTable::EM_ROTA) {
                $rota->status = RotasTable::FINALIZADA;
                $this->Rotas->saveOrFail($rota);
            } else {
                throw new BadRequestException('Atenção, está rota não pode ser finalizada pois não ' .
                    'está em andamento');
            }

            $conn->commit();
        } catch (PersistenceFailedException $e) {
            $conn->rollback();
            $this->log('Erro ao finalizar rota: ' . $e->getMessage());
            throw new ValidationException($e->getEntity());
        }

        $success = true;

        $this->set(compact('success', 'rota'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'rota']);
    }
}
