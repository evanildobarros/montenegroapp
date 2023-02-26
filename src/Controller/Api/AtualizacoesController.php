<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Hashids\Hashids;
use App\Model\Table\PedidosTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenDate;
use Psr\Log\LogLevel;

/**
 * Class AtualizacoesController
 *
 * @property \App\Model\Table\AtualizacoesTable $Atualizacoes
 */
class AtualizacoesController extends AppController
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

        $this->Authentication->allowUnauthenticated([
            'rastrear',
        ]);
    }

    /**
     * Rastrear method
     *
     * @return void
     * @throws \Exception
     */
    public function rastrear(): void
    {
        $this->getRequest()->allowMethod('post');
        $data = $this->getRequest()->getData();

        try {
            // Parametros
            $documento = (string)$data['documento'];
            $codigo = (string)$data['codigo'];

            /** @var \App\Model\Entity\Pedido $pedido */
            $pedido = $this->Atualizacoes->Pedidos
                ->find()
                ->select([
                    'Pedidos.id',
                    'Pedidos.created',
                    'Pedidos.status',
                    'Pessoas.id',
                    'Pessoas.nome',
                ])
                ->contain([
                    'Pessoas',
                ])
                ->where(function (QueryExpression $expression) use ($documento, $codigo) {
                    $documento = str_replace(['.', '-', '/'], '', $documento);
                    $orDocumento = $expression->or(function (QueryExpression $orExpression) use ($documento) {
                        return $orExpression
                            ->eq('Pessoas.cpf', $documento)
                            ->eq('Pessoas.cnpj', $documento);
                    });

                    if (ctype_digit($codigo)) { //contém somente números?
                        $expression->eq('Pedidos.id', $codigo);
                    } else {
                        $codigoDecode = Hashids::getInstance()->decode(strtoupper($codigo));
                        $expression->eq('Pedidos.id', $codigoDecode[0]);
                    }

                    $expression
                        ->notIn('Pedidos.status', [PedidosTable::PENDENTE, PedidosTable::CANCELADO])
                        ->add($orDocumento);

                    return $expression;
                })
                ->firstOrFail();

            // Pedidos só poderão ser rastreados após um mês de FINALIZADO ou CANCELADO
            $dataBase = new FrozenDate();
            $dataAtual = new FrozenDate();

            if (in_array($pedido->status, [PedidosTable::CANCELADO, PedidosTable::FINALIZADO])) {
                $dataBase = $pedido->created->addMonth(2);
            }

            if ($dataBase->format('Y-m-d') >= $dataAtual->format('Y-m-d')) {
                $atualizacoes = $this->Atualizacoes
                    ->find()
                    ->where(function (QueryExpression $expression) use ($pedido) {
                        $expression
                            ->eq('Atualizacoes.pedido_id', $pedido->id);

                        return $expression;
                    })
                    ->orderAsc('Atualizacoes.data');

                $pedido->codigo_rastreio = Hashids::getInstance()->encode($pedido->id);
                $pedido->atualizacoes = $atualizacoes;
            } else {
                throw new NotFoundException('Nenhum pedido encontrado');
            }
        } catch (\Exception $e) {
            $this->log(
                'API - Erro ao rastrear pedido pelo parâmetro informado: ' . json_encode($data),
                LogLevel::ERROR,
                ['scope' => ['rastreios']],
            );
            $this->log($e->getMessage(), LogLevel::ERROR, ['scope' => ['rastreios']]);

            throw $e;
        }

        $result = [
            'success' => true,
            'data' => [
                'pedido' => $pedido,
            ],
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
}
