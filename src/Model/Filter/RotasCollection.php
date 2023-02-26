<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\Date;
use Cake\ORM\Query;
use Search\Model\Filter\Callback;
use Search\Model\Filter\FilterCollection;

class RotasCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->value('status');
        $this->value('entregador_id');
        $this->add('data_saida', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['data_saida'])) {
                    $data = new Date(str_replace('/', '-', $args['data_saida']));

                    $query
                        ->where(function (QueryExpression $expression) use ($data) {
                            $expression
                                ->eq('DATE_FORMAT(Rotas.data_saida,\'%d/%m/%Y\')', $data);

                            return $expression;
                        });
                }
            },
        ]);
    }
}
