<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\Date;
use Cake\ORM\Query;
use Search\Model\Filter\Callback;
use Search\Model\Filter\FilterCollection;

class AtualizacoesCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->add('titulo', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('descricao', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('data', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['data'])) {
                    $data = new Date(str_replace('/', '-', $args['data']));

                    $query
                        ->where(function (QueryExpression $expression) use ($data) {
                            $expression
                                ->eq('DATE_FORMAT(Atualizacoes.data,\'%d/%m/%Y\')', $data);

                            return $expression;
                        });
                }
            },
        ]);
    }
}
