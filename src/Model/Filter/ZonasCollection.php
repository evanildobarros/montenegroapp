<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Search\Model\Filter\Callback;
use Search\Model\Filter\FilterCollection;

class ZonasCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->add('nome', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('nome_abreviado', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->value('cidade_id');
        $this->add('cep', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['cep'])) {
                    $cep = str_replace(['-', '.', '/'], '', $args['cep']);

                    $query
                        ->where(function (QueryExpression $expression, Query $query) use ($cep) {
                            $functionBuilder = $query->func();
                            $expression->between(
                                $cep,
                                $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                            );

                            return $expression;
                        });
                }
            },
        ]);
    }
}
