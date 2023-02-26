<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\EntregaMeio;
use App\Model\Entity\TabelaPreco;
use App\Model\Table\ObjetosTable;
use App\Model\Table\TabelaPrecosTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;

/**
 * EntregaMeios Controller
 *
 * @property \App\Model\Table\EntregaMeiosTable $EntregaMeios
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\EntregaMeio[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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

        $this->loadComponent('Search.Search', [
            'actions' => [
                'index',
            ],
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->EntregaMeios
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ]);
        $entregaMeios = $this->paginate($query);

        $isSearch = $this->EntregaMeios->isSearch();
        $this->set(compact('entregaMeios', 'isSearch'));
    }

    /**
     * View method
     *
     * @param string|null $id Entrega Meio id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $entregaMeio = $this->EntregaMeios->get($id, [
            'contain' => ['Pedidos'],
        ]);

        $this->set(compact('entregaMeio'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $entregaMeio = $this->EntregaMeios->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->EntregaMeios->getConnection();
            try {
                $conn->begin();
                $entregaMeio = $this->EntregaMeios->patchEntity($entregaMeio, $this->getRequest()->getData());
                $this->EntregaMeios->saveOrFail($entregaMeio);

                $conn->commit();
                $this->Flash->success(__('O entrega meio foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O entrega meio não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $this->set(compact('entregaMeio'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Entrega Meio id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $entregaMeio = $this->EntregaMeios->get($id, [
            'contain' => [],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->EntregaMeios->getConnection();
            try {
                $conn->begin();
                $entregaMeio = $this->EntregaMeios->patchEntity($entregaMeio, $this->getRequest()->getData());
                $this->EntregaMeios->saveOrFail($entregaMeio);

                $conn->commit();
                $this->Flash->success(__('O entrega meio foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O entrega meio não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $this->set(compact('entregaMeio'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Entrega Meio id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->EntregaMeios->getConnection();
        try {
            $conn->begin();
            $entregaMeio = $this->EntregaMeios->get($id);
            $this->EntregaMeios->deleteOrFail($entregaMeio);
            $conn->commit();
            $this->Flash->success(__('O entrega meio foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O entrega meio não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Entrega Meio id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->EntregaMeios->getConnection();
        try {
            $conn->begin();
            $this->EntregaMeios
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('EntregaMeios.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (EntregaMeio $entregaMeio) {
                    $this->EntregaMeios->deleteOrFail($entregaMeio);
                });
            $conn->commit();
            $this->Flash->success(__('O entrega meio foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O entrega meio não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Toggle method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function toggle()
    {
        if ($this->getRequest()->is('ajax')) {
            $id = $this->getRequest()->getData('id');
            $campo = $this->getRequest()->getData('field');

            $entregaMeio = $this->EntregaMeios
                ->find()
                ->select([
                    'id',
                    $campo,
                ])
                ->where(function (QueryExpression $expression) use ($id, $campo) {
                    $expression->eq('id', $id);

                    return $expression;
                })
                ->firstOrFail();

            $entregaMeio = $this->EntregaMeios->patchEntity($entregaMeio, [
                $campo => !$entregaMeio->get($campo),
            ]);

            $this->EntregaMeios->saveOrFail($entregaMeio);

            $this->set(compact('entregaMeio'));
            $this->set('_serialize', ['entregaMeio']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }

    /**
     * Disponiveis
     * Retorna os meios de entrega/coleta disponíveis conforme as condições informadas
     *
     * @return void
     */
    public function disponiveis()
    {
        $this->getRequest()->allowMethod('ajax');
        $data = $this->getRequest()->getQueryParams();

        $cliente = $this->EntregaMeios->Pedidos->Pessoas->get($data['cliente_id']);
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
        $peso = (float)$data['peso'];

        $unidade_medida_comprimento = $data['unidade_medida_comprimento'];
        $altura = (float)$data['altura'];
        $largura = (float)$data['largura'];
        $profundidade = (float)$data['profundidade'];

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

        if ($modalidade_distribuicao === TabelaPrecosTable::COLETA) {
            $this->EntregaMeios->TabelaPrecos
                ->find()
                ->contain('EntregaMeios')
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
                    $cliente
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

                        // Cálcula valor e tempo adicional
                        if (!empty($taxaAdicional)) {
                            // divide por 1000 para converter gramas para kg
                            $diff = ($peso / 1000) - ($taxaBasica->peso->peso_maximo / 1000);

                            for ($i = 1; $i <= $diff; $i++) {
                                $valorAdicional += $taxaAdicional->valor;
                                $tempoEstimadoAdicional += $taxaAdicional->tempo_estimado;
                            }
                        }

                        // Se o cliente tiver valor fixo definido, usar este valor
                        if (!empty($cliente->valor_fixo_pedidos)) {
                            $valor = $cliente->valor_fixo_pedidos;
                            $valorAdicional = 0;
                        } else {
                            $valor = $taxaBasica->valor;
                        }

                        $data = new FrozenDate();
                        $tempoEstimado = $taxaBasica->tempo_estimado + $tempoEstimadoAdicional;
                        $previsaoColeta = $data->addDays($tempoEstimado)->format('d/m/Y');

                        if ($tempoEstimado > 1) {
                            $nome = "{$tabelaPreco->entrega_meio->nome} - Coleta até {$previsaoColeta}";
                        } else {
                            $nome = "{$tabelaPreco->entrega_meio->nome} - Coleta até {$previsaoColeta}";
                        }

                        $resultMeiosColeta[] = [
                            'id' => $tabelaPreco->entrega_meio_id,
                            'nome' => $nome,
                            'valor' => 'R$ ' . number_format($valor + $valorAdicional, 2, ',', '.'),
                            'previsao_coleta' => $previsaoColeta,
                            'tempo_estimado' => $tempoEstimado,
                        ];
                    }
                });
        }

        // Buscar os meios de entrega
        $this->EntregaMeios->TabelaPrecos
            ->find()
            ->contain('EntregaMeios')
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
            ->each(function (TabelaPreco $tabelaPreco) use ($peso, $cep_entrega, &$resultMeiosEntrega, $cliente) {
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
                        ->where(function (QueryExpression $expression, Query $query) use ($peso, $cep_entrega, $tabelaPreco) {
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
                    if (!empty($cliente->valor_fixo_pedidos)) {
                        $valor = $cliente->valor_fixo_pedidos;
                        $valorAdicional = 0;
                    } else {
                        $valor = $taxaBasica->valor;
                    }

                    $data = new FrozenDate();
                    $tempoEstimado = $taxaBasica->tempo_estimado + $tempoEstimadoAdicional;
                    $previsaoEntrega = $data->addDays($tempoEstimado)->format('d/m/Y');

                    if ($tempoEstimado > 1) {
                        $nome = "{$tabelaPreco->entrega_meio->nome} - Entrega até {$previsaoEntrega}";
                    } else {
                        $nome = "{$tabelaPreco->entrega_meio->nome} - Entrega até {$previsaoEntrega}";
                    }

                    $resultMeiosEntrega[] = [
                        'id' => $tabelaPreco->entrega_meio_id,
                        'nome' => $nome,
                        'valor' => 'R$ ' . number_format($valor + $valorAdicional, 2, ',', '.'),
                        'previsao_entrega' => $previsaoEntrega,
                        'tempo_estimado' => $tempoEstimado,
                    ];
                }
            });

        $result = [
            'meios_coleta' => $resultMeiosColeta,
            'meios_entrega' => $resultMeiosEntrega,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
}
