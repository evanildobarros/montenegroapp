<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\Date;
use Cake\ORM\Query;
use JansenFelipe\Utils\Utils;
use Search\Model\Filter\Callback;
use Search\Model\Filter\FilterCollection;

class PessoasCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->value('tipo');
        $this->value('email');
        $this->value('celular');
        $this->value('cidade_id', [
            'fields' => ['Enderecos.cidade_id'],
        ]);
        $this->value('status');
        $this->value('quantidade_entregas');
        $this->add('nome', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('data_nascimento', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['data_nascimento'])) {
                    $data = new Date(str_replace('/', '-', $args['data_nascimento']));

                    $query
                        ->where(function (QueryExpression $expression) use ($data) {
                            $expression
                                ->eq('Pessoas.data_nascimento', $data);

                            return $expression;
                        });
                }
            },
        ]);
        $this->add('cpf', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['cpf'])) {
                    $cpf = Utils::unmask($args['cpf']);

                    $query
                        ->where(function (QueryExpression $expression) use ($cpf) {
                            $expression
                                ->eq('Pessoas.cpf', $cpf);

                            return $expression;
                        });
                }
            },
        ]);
        $this->add('cnpj', 'Search.Callback', [
            'callback' => function (Query $query, array $args, Callback $filter) {
                if (!empty($args['cnpj'])) {
                    $cnpj = Utils::unmask($args['cnpj']);

                    $query
                        ->where(function (QueryExpression $expression) use ($cnpj) {
                            $expression
                                ->eq('Pessoas.cnpj', $cnpj);

                            return $expression;
                        });
                }
            },
        ]);
    }
}
