<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\Date;
use Cake\ORM\Query;
use Search\Model\Filter\Callback;
use Search\Model\Filter\FilterCollection;

class PedidosCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->value('status');
        $this->value('modalidade_distribuicao');
        $this->value('cliente_id');
        $this->value('filial_id');
        $this->value('cidade_id', [
            'fields' => ['Filiais.cidade_id'],
        ]);
        $this->value('entrega_modalidade_id');
        $this->value('entrega_meio_id');
        $this->add('prazo_envio', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['prazo_envio'])) {
                    $data = new Date(str_replace('/', '-', $args['prazo_envio']));

                    $query
                        ->where(function (QueryExpression $expression) use ($data) {
                            $expression
                                ->eq('Pedidos.prazo_envio', $data);

                            return $expression;
                        });
                }
            },
        ]);
        $this->add('previsao_entrega', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['previsao_entrega'])) {
                    $data = new Date(str_replace('/', '-', $args['previsao_entrega']));

                    $query
                        ->where(function (QueryExpression $expression) use ($data) {
                            $expression
                                ->eq('Pedidos.previsao_entrega', $data);

                            return $expression;
                        });
                }
            },
        ]);
        $this->add('data_entrega', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['data_entrega'])) {
                    $data = new Date(str_replace('/', '-', $args['data_entrega']));

                    $query
                        ->where(function (QueryExpression $expression) use ($data) {
                            $expression
                                ->eq('DATE_FORMAT(Pedidos.data_entrega,\'%d/%m/%Y\')', $data);

                            return $expression;
                        });
                }
            },
        ]);
        $this->add('objeto_recebido', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                $query
                    ->where(function (QueryExpression $expression) use ($args) {
                        if ($args['objeto_recebido']) {
                            $expression->isNotNull('Pedidos.data_chegada');
                        } else {
                            $expression->isNull('Pedidos.data_chegada');
                        }

                        return $expression;
                    });
            },
        ]);
        $this->add('objeto_entregue', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                $query
                    ->where(function (QueryExpression $expression) use ($args) {
                        if ($args['objeto_entregue']) {
                            $expression->isNotNull('Pedidos.data_entrega');
                        } else {
                            $expression->isNull('Pedidos.data_entrega');
                        }

                        return $expression;
                    });
            },
        ]);
        $this->add('meio_coleta', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('meio_entrega', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('modalidade_distribuicao', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->value('rota_id', [
            'fields' => ['RotaPedidos.rota_id'],
        ]);
        $this->value('parada_tipo', [
            'fields' => ['RotaPedidos.tipo'],
        ]);
    }
}
