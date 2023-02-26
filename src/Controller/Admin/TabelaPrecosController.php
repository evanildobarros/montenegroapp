<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\TabelaPreco;
use App\Model\Entity\Zona;
use App\Model\Table\TabelaPrecosTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * TabelaPrecos Controller
 *
 * @property \App\Model\Table\TabelaPrecosTable $TabelaPrecos
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\TabelaPreco[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TabelaPrecosController extends AppController
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
        $query = $this->TabelaPrecos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain([
                'EntregaMeios',
                'Zonas' => function (Query $query) {
                    return $query->orderAsc('nome');
                },
            ]);
        $tabelaPrecos = $this->paginate($query);

        $entregaMeios = $this->TabelaPrecos->EntregaMeios->listaAll();
        $isSearch = $this->TabelaPrecos->isSearch();
        $this->set(compact('tabelaPrecos', 'entregaMeios', 'isSearch'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tabelaPreco = $this->TabelaPrecos->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->TabelaPrecos->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                if (empty($data['pesos'])) {
                    throw new BadRequestException('Atenção! Informe pelo menos uma faixa de peso.');
                }
                if (empty($data['zonas'])) {
                    throw new BadRequestException('Atenção! Informe pelo menos um bairro.');
                }

                foreach ($data['pesos'] as $idx => $peso) {
                    if ($peso['quilo_adicional'] === 'true') {
                        $data['pesos'][$idx]['quilo_adicional'] = true;
                    } else {
                        $data['pesos'][$idx]['quilo_adicional'] = false;
                    }

                    $novasTaxas = [];
                    foreach ($peso['taxas'] as $taxa) {
                        foreach ($data['zonas']['_ids'] as $zona) {
                            /*
                             * Verifica se tem salvo no banco esse bairro em outra tabela de preco
                             */
                            $tabelaPrecoBanco = $this->TabelaPrecos
                                ->find()
                                ->join([
                                    'TabelaPrecosZonas' => [
                                        'table' => 'tabela_precos_zonas',
                                        'type' => 'INNER',
                                        'conditions' => 'TabelaPrecosZonas.tabela_preco_id = TabelaPrecos.id',
                                    ],
                                ])
                                ->where(function (QueryExpression $expression) use ($tabelaPreco, $data, $zona) {
                                    if (!empty($tabelaPreco->id)) {
                                        $expression->notEq('TabelaPrecos.id', $tabelaPreco->id);
                                    }

                                    $expression
                                        ->eq('TabelaPrecos.modalidade_distribuicao', $data['modalidade_distribuicao'])
                                        ->eq('TabelaPrecos.entrega_meio_id', $data['entrega_meio_id'])
                                        ->eq('TabelaPrecosZonas.zona_id', $zona);

                                    return $expression;
                                })
                                ->first();

                            if (!empty($tabelaPrecoBanco)) {
                                $bairro = $this->TabelaPrecos->Pesos->Taxas->Zonas->get($zona);

                                throw new BadRequestException(
                                    "Atenção! O bairro {$bairro->nome} (Cód. {$bairro->id}) já está" .
                                    " cadastrado na tabela de preço '{$tabelaPrecoBanco->nome}' " .
                                    "(Cód. {$tabelaPrecoBanco->id}), na modalidade de " .
                                    "distribuição e no meio de entrega/coleta informado. "
                                );
                            }

                            /*
                             * Verifica se tem salvo no banco essa taxa
                             */
                            $taxaNoBanco = $this->TabelaPrecos->Pesos->Taxas
                                ->find()
                                ->where([
                                    'Taxas.peso_id' => $peso['id'],
                                    'Taxas.zona_id' => $zona,
                                ])
                                ->first();

                            $novasTaxas[] = [
                                'id' => $taxaNoBanco->id ?? null,
                                'valor' => $taxa['valor'],
                                'tempo_estimado' => $taxa['tempo_estimado'],
                                'zona_id' => $zona,
                            ];
                        }
                    }
                    $data['pesos'][$idx]['taxas'] = $novasTaxas;
                }

                $tabelaPreco = $this->TabelaPrecos->patchEntity($tabelaPreco, $data, [
                    'associated' => [
                        'Zonas',
                        'Pesos' => [
                            'associated' => [
                                'Taxas',
                            ],
                        ],
                    ],
                ]);

                $this->TabelaPrecos->saveOrFail($tabelaPreco);

                $conn->commit();
                $this->Flash->success(__('A tabela de preço foi salva com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());

                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                dd($e->getMessage());

                if ($e->getCode() === '10000') {
                    $this->Flash->error(__('A tabela de preço não pode ser salvo.  ' .
                        'Por favor, verifique os intervalos de peso.'));
                } else {
                    $this->Flash->error(__('A tabela preco não pode ser salva. Por favor, tente novamente.'));
                }
            }
        }

        //Pedidos sem rota que foram selecionados
        $zonas_selecionadas_cadastro = [];
        if (isset($data['zonas']['_ids'])) {
            $zonas_selecionadas_cadastro = $this->TabelaPrecos->Zonas
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => function (Zona $entity) {
                        return sprintf('%s (%s)', $entity->nome, $entity->cidade->nome . '/' . $entity->cidade->estado->sigla);
                    },
                ])
                ->contain(['Cidades' => ['Estados']])
                ->orderAsc('Zonas.nome')
                ->where(['Zonas.id IN' => $data['zonas']['_ids']]);
        }

        $entregaMeios = $this->TabelaPrecos->EntregaMeios->listaAtivas();
        $zonas = $this->TabelaPrecos->Zonas
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Zona $entity) {
                    return sprintf('%s (%s)', $entity->nome, $entity->cidade->nome . '/' . $entity->cidade->estado->sigla);
                },
            ])
            ->contain(['Cidades' => ['Estados']])
            ->orderAsc('Zonas.nome');
        $modalidadesDistribuicao = TabelaPrecosTable::MODALIDADE_DISTRIBUICAO;
        $this->set(compact('tabelaPreco', 'entregaMeios', 'zonas', 'modalidadesDistribuicao', 'zonas_selecionadas_cadastro'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tabela Preco id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tabelaPreco = $this->TabelaPrecos->get($id, [
            'contain' => [
                'Zonas' => [
                    'Cidades' => [
                        'Estados',
                    ],
                ],
                'Pesos' => [
                    'Taxas',
                ],
            ],
        ]);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->TabelaPrecos->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                if (empty($data['pesos'])) {
                    throw new BadRequestException('Atenção! Informe pelo menos uma faixa de peso.');
                }
                if (empty($data['zonas'])) {
                    throw new BadRequestException('Atenção! Informe pelo menos um bairro.');
                }

                foreach ($data['pesos'] as $idx => $peso) {
                    if ($peso['quilo_adicional'] === 'true') {
                        $data['pesos'][$idx]['quilo_adicional'] = true;
                    } else {
                        $data['pesos'][$idx]['quilo_adicional'] = false;
                    }

                    $novasTaxas = [];
                    foreach ($peso['taxas'] as $taxa) {
                        foreach ($data['zonas']['_ids'] as $zona) {
                            /*
                             * Verifica se tem salvo no banco esse bairro em outra tabela de preco
                             */
                            $tabelaPrecoBanco = $this->TabelaPrecos
                                ->find()
                                ->join([
                                    'TabelaPrecosZonas' => [
                                        'table' => 'tabela_precos_zonas',
                                        'type' => 'INNER',
                                        'conditions' => 'TabelaPrecosZonas.tabela_preco_id = TabelaPrecos.id',
                                    ],
                                ])
                                ->where(function (QueryExpression $expression) use ($tabelaPreco, $data, $zona) {
                                    if (!empty($tabelaPreco->id)) {
                                        $expression->notEq('TabelaPrecos.id', $tabelaPreco->id);
                                    }

                                    $expression
                                        ->eq('TabelaPrecos.modalidade_distribuicao', $data['modalidade_distribuicao'])
                                        ->eq('TabelaPrecos.entrega_meio_id', $data['entrega_meio_id'])
                                        ->eq('TabelaPrecosZonas.zona_id', $zona);

                                    return $expression;
                                })
                                ->first();

                            if (!empty($tabelaPrecoBanco)) {
                                $bairro = $this->TabelaPrecos->Pesos->Taxas->Zonas->get($zona);

                                throw new BadRequestException(
                                    "Atenção! O bairro {$bairro->nome} (Cód. {$bairro->id}) já está" .
                                    " cadastrado na tabela de preço '{$tabelaPrecoBanco->nome}' " .
                                    "(Cód. {$tabelaPrecoBanco->id}), na modalidade de " .
                                    "distribuição e no meio de entrega/coleta informado. "
                                );
                            }

                            /*
                             * Verifica se tem saldo no banco essa taxa
                             */
                            $taxaNoBanco = $this->TabelaPrecos->Pesos->Taxas
                                ->find()
                                ->where([
                                    'Taxas.peso_id' => $peso['id'],
                                    'Taxas.zona_id' => $zona,
                                ])
                                ->first();

                            $novasTaxas[] = [
                                'id' => $taxaNoBanco->id ?? null,
                                'valor' => $taxa['valor'],
                                'tempo_estimado' => $taxa['tempo_estimado'],
                                'zona_id' => $zona,
                            ];
                        }
                    }

                    $data['pesos'][$idx]['taxas'] = $novasTaxas;
                }

                $tabelaPreco = $this->TabelaPrecos->patchEntity($tabelaPreco, $data, [
                    'associated' => [
                        'Zonas',
                        'Pesos' => [
                            'associated' => [
                                'Taxas',
                            ],
                        ],
                    ],
                ]);
                $this->TabelaPrecos->saveOrFail($tabelaPreco);

                $conn->commit();
                $this->Flash->success(__('A tabela de preço foi salva com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());

                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());

                if ($e->getCode() === '10000') {
                    $this->Flash->error(__('A tabela de preço não pode ser salvo.  ' .
                        'Por favor, verifique os intervalos de peso.'));
                } else {
                    $this->Flash->error(__('A tabela de preço não pode ser salva. Por favor, tente novamente.'));
                }
            }
        }
        $entregaMeios = $this->TabelaPrecos->EntregaMeios->listaAll();
        $zonas = $this->TabelaPrecos->Zonas
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Zona $entity) {
                    return sprintf('%s (%s)', $entity->nome, $entity->cidade->nome . '/' . $entity->cidade->estado->sigla);
                },
            ])
            ->contain(['Cidades' => ['Estados']])
            ->orderAsc('Zonas.nome');
        $modalidadesDistribuicao = TabelaPrecosTable::MODALIDADE_DISTRIBUICAO;
        $this->set(compact('tabelaPreco', 'entregaMeios', 'zonas', 'modalidadesDistribuicao'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tabela Preco id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->TabelaPrecos->getConnection();
        try {
            $conn->begin();
            $tabelaPreco = $this->TabelaPrecos->get($id);
            $this->TabelaPrecos->deleteOrFail($tabelaPreco);
            $conn->commit();
            $this->Flash->success(__('A tabela de preço foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A tabela de preço não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Tabela Preco id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->TabelaPrecos->getConnection();
        try {
            $conn->begin();
            $this->TabelaPrecos
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('TabelaPrecos.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (TabelaPreco $tabelaPreco) {
                    $this->TabelaPrecos->deleteOrFail($tabelaPreco);
                });
            $conn->commit();
            $this->Flash->success(__('A tabela de preço foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A tabela de preço não pode ser excluída. Por favor, tente novamente.'));
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

            $tabelaPreco = $this->TabelaPrecos
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

            $tabelaPreco = $this->TabelaPrecos->patchEntity($tabelaPreco, [
                $campo => !$tabelaPreco->get($campo),
            ]);

            $this->TabelaPrecos->saveOrFail($tabelaPreco);

            $this->set(compact('tabelaPreco'));
            $this->set('_serialize', ['tabelaPreco']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
