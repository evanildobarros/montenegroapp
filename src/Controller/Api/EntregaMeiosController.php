<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Entity\TabelaPreco;
use App\Model\Table\ObjetosTable;
use App\Model\Table\PedidosTable;
use App\Model\Table\TabelaPrecosTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;

/**
 * Class EntregaMeiosController
 *
 * @property \App\Model\Table\EntregaMeiosTable $EntregaMeios
 */
class EntregaMeiosController extends AppController
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
     * Meios
     * Retorna os meios de entrega/coleta disponíveis conforme a tabela de preços
     *
     * @param string $modalidade_distribuicao Modalidade de distribuição
     * @return void
     */
    public function meios($modalidade_distribuicao)
    {
        $this->getRequest()->allowMethod('get');

        $meios = $this->EntregaMeios
            ->find('list')
            ->join([
                'TabelaPrecos' => [
                    'table' => 'tabela_precos',
                    'type' => 'INNER',
                    'conditions' => 'TabelaPrecos.entrega_meio_id = EntregaMeios.id',
                ],
                'TabelaPrecosZonas' => [
                    'table' => 'tabela_precos_zonas',
                    'type' => 'INNER',
                    'conditions' => 'TabelaPrecosZonas.tabela_preco_id = TabelaPrecos.id',
                ],
                'Zonas' => [
                    'table' => 'zonas',
                    'type' => 'INNER',
                    'conditions' => 'Zonas.id = TabelaPrecosZonas.zona_id',
                ],
                'Faixas' => [
                    'table' => 'faixas',
                    'type' => 'INNER',
                    'conditions' => [
                        'Faixas.zona_id = Zonas.id',
                    ],
                ],
                'Pesos' => [
                    'table' => 'pesos',
                    'type' => 'INNER',
                    'conditions' => 'Pesos.tabela_preco_id = TabelaPrecos.id',
                ],
            ])
            ->where(function (QueryExpression $expression) use ($modalidade_distribuicao) {
                $expression
                    ->eq('TabelaPrecos.modalidade_distribuicao', $modalidade_distribuicao)
                    ->eq('EntregaMeios.status', true);

                return $expression;
            })
            ->group(['EntregaMeios.id', 'EntregaMeios.nome'])
            ->toArray();

        $result = [
            'success' => true,
            'data' => $meios,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Disponiveis
     * Retorna os meios de entrega/coleta disponíveis conforme as condições informadas
     *
     * @return void
     */
    public function disponiveis()
    {
        $this->getRequest()->allowMethod('get');

        $pessoa = $this->Authentication->getIdentity();
        $data = $this->getRequest()->getQueryParams();

        $modalidade_distribuicao = $data['modalidade_distribuicao'];
        if (!in_array($modalidade_distribuicao, [TabelaPrecosTable::COLETA, TabelaPrecosTable::ENTREGA])) {
            throw new BadRequestException('Modalidade de distribuição inválida');
        }

        /*
         * No dia 20/07/2022:
         * Montenegro solicitou, através do whats, a troca de busca por cidade para bairro (faixas de cep).
         */
        $cep_entrega = str_replace(['-', '.'], '', ($data['cep_entrega'] ?? null));
        $cep_coleta = str_replace(['-', '.'], '', ($data['cep_coleta'] ?? null));

        $unidade_medida_peso = $data['unidade_medida_peso'];
        $peso = $data['peso'];

        $unidade_medida_comprimento = $data['unidade_medida_comprimento'];
        $altura = $data['altura'];
        $largura = $data['largura'];
        $profundidade = $data['profundidade'];

        if ($unidade_medida_peso === ObjetosTable::QUILO) {
            $peso = $peso * 1000;
        }
        if ($unidade_medida_comprimento === ObjetosTable::METRO) {
            $altura = $altura * 100;
            $largura = $largura * 100;
            $profundidade = $profundidade * 100;
        }

        $resultMeiosColeta = [];
        $resultMeiosEntrega = [];

        /*
         * Busca os meios de coleta disponíveis conforme os parametros
         */
        if ($modalidade_distribuicao === TabelaPrecosTable::COLETA) {
            /** @var \App\Model\Entity\Faixa $faixa */
            $faixa = $this->EntregaMeios->TabelaPrecos->Zonas->Faixas
                ->find()
                ->where(function (QueryExpression $expression, Query $query) use ($cep_coleta) {
                    $functionBuilder = $query->func();
                    $expression->between(
                        $cep_coleta,
                        $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                        $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                    );

                    return $expression;
                })
                ->first();

            if (!empty($faixa)) {
                $this->EntregaMeios->TabelaPrecos
                    ->find()
                    ->contain([
                        'EntregaMeios',
                    ])
                    ->join([
                        'table' => 'tabela_precos_zonas',
                        'alias' => 'TabelaPrecosZonas',
                        'type' => 'INNER',
                        'conditions' => [
                            'TabelaPrecosZonas.zona_id' => $faixa->zona_id,
                            'TabelaPrecosZonas.tabela_preco_id = TabelaPrecos.id',
                        ],
                    ])
                    ->where(function (QueryExpression $expression) use ($altura, $largura, $profundidade) {
                        $orAltura = $expression->or(function (QueryExpression $orExpression) use ($altura) {
                            return $orExpression
                                ->gte('EntregaMeios.altura_maxima', $altura)
                                ->isNull('EntregaMeios.altura_maxima');
                        });
                        $orLargura = $expression->or(function (QueryExpression $orExpression) use ($largura) {
                            return $orExpression
                                ->gte('EntregaMeios.largura_maxima', $largura)
                                ->isNull('EntregaMeios.largura_maxima');
                        });
                        $orProfundidade = $expression->or(function (QueryExpression $orExpression) use ($profundidade) {
                            return $orExpression
                                ->gte('EntregaMeios.profundidade_maxima', $profundidade)
                                ->isNull('EntregaMeios.profundidade_maxima');
                        });

                        $expression
                            ->add([$orAltura, $orLargura, $orProfundidade])
                            ->eq('TabelaPrecos.modalidade_distribuicao', TabelaPrecosTable::COLETA);

                        return $expression;
                    })
                    ->each(function (TabelaPreco $tabelaPreco) use (
                        $peso,
                        $cep_coleta,
                        &$resultMeiosColeta,
                        $pessoa
                    ) {
                        /** @var \App\Model\Entity\Taxa $taxaAdicional */
                        $taxaAdicional = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                            ->find()
                            ->contain('Pesos')
                            ->join([
                                'table' => 'faixas',
                                'alias' => 'Faixas',
                                'type' => 'INNER',
                                'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                            ])
                            ->where(function (QueryExpression $expression, Query $query) use ($peso, $cep_coleta, $tabelaPreco) {
                                $expression
                                    ->eq('Pesos.quilo_adicional', true)
                                    ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                $functionBuilder = $query->func();
                                $expression->between(
                                    $cep_coleta,
                                    $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                    $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                );

                                return $expression;
                            })
                            ->first();

                        if (empty($taxaAdicional)) {
                            /** @var \App\Model\Entity\Taxa $taxaBasica */
                            $taxaBasica = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                                ->find()
                                ->contain('Pesos')
                                ->join([
                                    'table' => 'faixas',
                                    'alias' => 'Faixas',
                                    'type' => 'INNER',
                                    'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                                ])
                                ->where(function (QueryExpression $expression, Query $query) use (
                                    $peso,
                                    $cep_coleta,
                                    $tabelaPreco
                                ) {
                                    $expression
                                        ->lte('Pesos.peso_minimo', $peso)
                                        ->gte('Pesos.peso_maximo', $peso)
                                        ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                    $functionBuilder = $query->func();
                                    $expression->between(
                                        $cep_coleta,
                                        $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                        $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                    );

                                    return $expression;
                                })
                                ->first();
                        } else {
                            // Verifica se o PESO atende ao mínimo da tabela de preços
                            $taxaMinima = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                                ->find()
                                ->contain('Pesos')
                                ->join([
                                    'table' => 'faixas',
                                    'alias' => 'Faixas',
                                    'type' => 'INNER',
                                    'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                                ])
                                ->where(function (QueryExpression $expression, Query $query) use (
                                    $peso,
                                    $cep_coleta,
                                    $tabelaPreco
                                ) {
                                    $expression
                                        ->lte('Pesos.peso_minimo', $peso)
                                        ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                    $functionBuilder = $query->func();
                                    $expression->between(
                                        $cep_coleta,
                                        $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                        $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                    );

                                    return $expression;
                                })
                                ->count();

                            if ($taxaMinima > 0) {
                                // Busca o valor da taxa que possui a maior faixa de peso
                                $taxaBasica = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                                    ->find()
                                    ->contain('Pesos')
                                    ->join([
                                        'table' => 'faixas',
                                        'alias' => 'Faixas',
                                        'type' => 'INNER',
                                        'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                                    ])
                                    ->where(function (QueryExpression $expression, Query $query) use (
                                        $peso,
                                        $cep_coleta,
                                        $tabelaPreco
                                    ) {
                                        $expression
                                            ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                        $functionBuilder = $query->func();
                                        $expression->between(
                                            $cep_coleta,
                                            $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                            $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                        );

                                        return $expression;
                                    })
                                    ->order([
                                        'ISNULL(Pesos.peso_maximo)',
                                        'Pesos.peso_maximo DESC',
                                    ])
                                    ->first();
                            }
                        }

                        if (!empty($taxaBasica)) {
                            $valorAdicional = 0;
                            $tempoEstimadoAdicional = 0;

                            // Cálcula valor adicional
                            if (!empty($taxaAdicional)) {
                                // divide por 1000 para converter gramas para kg
                                $diff = ($peso / 1000) - ($taxaBasica->peso->peso_maximo / 1000);

                                for ($i = 1; $i <= $diff; $i++) {
                                    $valorAdicional += $taxaAdicional->valor;
                                    $tempoEstimadoAdicional += $taxaAdicional->tempo_estimado;
                                }
                            }

                            // Se o cliente tiver valor fixo definido, usar este valor
                            if (!empty($pessoa->valor_fixo_pedidos)) {
                                $valor = $pessoa->valor_fixo_pedidos;
                                $valorAdicional = 0;
                            } else {
                                $valor = $taxaBasica->valor;
                            }

                            $tempoEstimado = $taxaBasica->tempo_estimado + $tempoEstimadoAdicional;
                            $data = new FrozenDate();
                            $previsaoColeta = $data->addDays($tempoEstimado);

                            $resultMeiosColeta[] = [
                                'id' => $tabelaPreco->entrega_meio_id,
                                'nome' => $tabelaPreco->entrega_meio->nome,
                                'tempo_estimado' => $tempoEstimado,
                                'valor' => round($valor + $valorAdicional, 2),
                                'previsao_coleta' => $previsaoColeta,
                            ];
                        }
                    });
            }
        }


        //-----------------------------------------------------------------------------------------------
        /*
         * Buscar os meios de entrega
         */

        /** @var \App\Model\Entity\Faixa $faixaEntrega */
        $faixaEntrega = $this->EntregaMeios->TabelaPrecos->Zonas->Faixas
            ->find()
            ->where(function (QueryExpression $expression, Query $query) use ($cep_entrega) {
                $functionBuilder = $query->func();
                $expression->between(
                    $cep_entrega,
                    $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                    $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                );

                return $expression;
            })
            ->first();

        if (!empty($faixaEntrega)) {
            $this->EntregaMeios->TabelaPrecos
                ->find()
                ->contain('EntregaMeios')
                ->join([
                    'table' => 'tabela_precos_zonas',
                    'alias' => 'TabelaPrecosZonas',
                    'type' => 'INNER',
                    'conditions' => [
                        'TabelaPrecosZonas.zona_id' => $faixaEntrega->zona_id,
                        'TabelaPrecosZonas.tabela_preco_id = TabelaPrecos.id',
                    ],
                ])
                ->where(function (QueryExpression $expression) use ($altura, $largura, $profundidade) {
                    $orAltura = $expression->or(function (QueryExpression $orExpression) use ($altura) {
                        return $orExpression
                            ->gte('EntregaMeios.altura_maxima', $altura)
                            ->isNull('EntregaMeios.altura_maxima');
                    });
                    $orLargura = $expression->or(function (QueryExpression $orExpression) use ($largura) {
                        return $orExpression
                            ->gte('EntregaMeios.largura_maxima', $largura)
                            ->isNull('EntregaMeios.largura_maxima');
                    });
                    $orProfundidade = $expression->or(function (QueryExpression $orExpression) use ($profundidade) {
                        return $orExpression
                            ->gte('EntregaMeios.profundidade_maxima', $profundidade)
                            ->isNull('EntregaMeios.profundidade_maxima');
                    });

                    $expression
                        ->add([$orAltura, $orLargura, $orProfundidade])
                        ->eq('TabelaPrecos.modalidade_distribuicao', TabelaPrecosTable::ENTREGA);

                    return $expression;
                })

                ->each(function (TabelaPreco $tabelaPreco) use ($peso, $cep_entrega, &$resultMeiosEntrega, $pessoa) {
                    /** @var \App\Model\Entity\Taxa $taxaAdicional */
                    $taxaAdicional = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                        ->find()
                        ->contain('Pesos')
                        ->join([
                            'table' => 'faixas',
                            'alias' => 'Faixas',
                            'type' => 'INNER',
                            'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                        ])
                        ->where(function (QueryExpression $expression, Query $query) use ($peso, $cep_entrega, $tabelaPreco) {
                            $expression
                                ->eq('Pesos.quilo_adicional', true)
                                ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                            $functionBuilder = $query->func();
                            $expression->between(
                                $cep_entrega,
                                $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                            );

                            return $expression;
                        })
                        ->first();

                    if (empty($taxaAdicional)) {
                        /** @var \App\Model\Entity\Taxa $taxaBasica */
                        $taxaBasica = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                            ->find()
                            ->contain('Pesos')
                            ->join([
                                'table' => 'faixas',
                                'alias' => 'Faixas',
                                'type' => 'INNER',
                                'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                            ])
                            ->where(function (QueryExpression $expression, Query $query) use ($peso, $cep_entrega, $tabelaPreco) {
                                $expression
                                    ->lte('Pesos.peso_minimo', $peso)
                                    ->gte('Pesos.peso_maximo', $peso)
                                    ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                $functionBuilder = $query->func();
                                $expression->between(
                                    $cep_entrega,
                                    $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                    $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                );

                                return $expression;
                            })
                            ->first();
                    } else {
                        // Verifica se o PESO atende ao mínimo da tabela de preços
                        $taxaMinima = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                            ->find()
                            ->contain('Pesos')
                            ->join([
                                'table' => 'faixas',
                                'alias' => 'Faixas',
                                'type' => 'INNER',
                                'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                            ])
                            ->where(function (QueryExpression $expression, Query $query) use (
                                $peso,
                                $cep_entrega,
                                $tabelaPreco
                            ) {
                                $expression
                                    ->lte('Pesos.peso_minimo', $peso)
                                    ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                $functionBuilder = $query->func();
                                $expression->between(
                                    $cep_entrega,
                                    $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                    $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                );

                                return $expression;
                            })
                            ->count();

                        if ($taxaMinima > 0) {
                            // Busca o valor da taxa que possui a maior faixa de peso
                            $taxaBasica = $this->EntregaMeios->TabelaPrecos->Pesos->Taxas
                                ->find()
                                ->contain('Pesos')
                                ->join([
                                    'table' => 'faixas',
                                    'alias' => 'Faixas',
                                    'type' => 'INNER',
                                    'conditions' => 'Faixas.zona_id = Taxas.zona_id',
                                ])
                                ->where(function (QueryExpression $expression, Query $query) use (
                                    $peso,
                                    $cep_entrega,
                                    $tabelaPreco
                                ) {
                                    $expression
                                        ->eq('Pesos.tabela_preco_id', $tabelaPreco->id);

                                    $functionBuilder = $query->func();
                                    $expression->between(
                                        $cep_entrega,
                                        $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                                        $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                                    );

                                    return $expression;
                                })
                                ->order([
                                    'ISNULL(Pesos.peso_maximo)',
                                    'Pesos.peso_maximo DESC',
                                ])
                                ->first();
                        }
                    }

                    if (!empty($taxaBasica)) {
                        $valorAdicional = 0;
                        $tempoEstimadoAdicional = 0;

                        // Cálcula valor e tempo adicional
                        if (!empty($taxaAdicional)) {
                            // divide por 1000 para converter gramas para kg
                            $diff = ($peso / 1000) - ($taxaBasica->peso->peso_maximo / 1000);

                            /*
                             * A cada quilo adicional, o valor e o tempo são acrescidos
                             */
                            for ($i = 1; $i <= $diff; $i++) {
                                $valorAdicional += $taxaAdicional->valor;
                                $tempoEstimadoAdicional += $taxaAdicional->tempo_estimado;
                            }
                        }

                        // Se o cliente tiver valor fixo definido, usar este valor
                        if (!empty($pessoa->valor_fixo_pedidos)) {
                            $valor = $pessoa->valor_fixo_pedidos;
                            $valorAdicional = 0;
                        } else {
                            $valor = $taxaBasica->valor;
                        }

                        $tempoEstimado = $taxaBasica->tempo_estimado + $tempoEstimadoAdicional;
                        $data = new FrozenDate();
                        $previsaoEntrega = $data->addDays($tempoEstimado);

                        $resultMeiosEntrega[] = [
                            'id' => $tabelaPreco->entrega_meio_id,
                            'nome' => $tabelaPreco->entrega_meio->nome,
                            'tempo_estimado' => $tempoEstimado,
                            'valor' => round($valor + $valorAdicional, 2),
                            'previsao_entrega' => $previsaoEntrega,
                        ];
                    }
                });
        }

        $result = [
            'success' => true,
            'data' => [
                'meios_coleta' => $resultMeiosColeta,
                'meios_entrega' => $resultMeiosEntrega,
            ],
        ];

        if ($modalidade_distribuicao === PedidosTable::ENTREGA) {
            $dias = (int)$this->Configs->parametro('prazo_envio');
            $dataBase = new FrozenDate();
            $prazoEnvio = $dataBase->addDays($dias);

            $result['data']['prazo_envio'] = $prazoEnvio;
        }

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * limites
     * Retorna os limites de dimensões e peso do meio informado
     *
     * @param int $entrega_meio_id ID Meio de entrega
     * @param int $modalidade_distribuicao modalidade de distribuição
     * @return void
     */
    public function limites($entrega_meio_id, $modalidade_distribuicao)
    {
        $this->getRequest()->allowMethod('get');
        $meioEntrega = $this->EntregaMeios->get($entrega_meio_id);

        /** @var \App\Model\Entity\Peso $peso */
        $peso = $this->EntregaMeios->TabelaPrecos->Pesos
            ->find()
            ->select([
                'peso_minimo' => 'MIN(Pesos.peso_minimo)',
                'peso_maximo' => 'MAX(Pesos.peso_maximo)',
            ])
            ->contain([
                'TabelaPrecos',
            ])
            ->where(function (QueryExpression $expression) use ($meioEntrega, $modalidade_distribuicao) {
                $expression
                    ->eq('TabelaPrecos.entrega_meio_id', $meioEntrega->id)
                    ->eq('TabelaPrecos.modalidade_distribuicao', $modalidade_distribuicao);

                return $expression;
            })
            ->first();

        /** @var \App\Model\Entity\Peso $pesoQuiloAdicional */
        $pesoQuiloAdicional = $this->EntregaMeios->TabelaPrecos->Pesos
            ->find()
            ->contain([
                'TabelaPrecos',
            ])
            ->where(function (QueryExpression $expression) use ($meioEntrega, $modalidade_distribuicao) {
                $expression
                    ->eq('TabelaPrecos.entrega_meio_id', $meioEntrega->id)
                    ->eq('TabelaPrecos.modalidade_distribuicao', $modalidade_distribuicao)
                    ->eq('Pesos.quilo_adicional', true);

                return $expression;
            })
            ->first();

        if (!empty($pesoQuiloAdicional)) {
            $peso->peso_maximo = null;
        }

        $result = [
            'success' => true,
            'data' => $meioEntrega,
            'pesos' => $peso,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
}
