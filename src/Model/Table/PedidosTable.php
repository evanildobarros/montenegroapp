<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Pedido;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Pedidos Model
 *
 * @property \App\Model\Table\PessoasTable&\Cake\ORM\Association\BelongsTo $Pessoas
 * @property \App\Model\Table\ObjetosTable&\Cake\ORM\Association\BelongsTo $Objetos
 * @property \App\Model\Table\FiliaisTable&\Cake\ORM\Association\BelongsTo $Filiais
 * @property \App\Model\Table\EntregaMeiosTable&\Cake\ORM\Association\BelongsTo $EntregaMeios
 * @property \App\Model\Table\EntregaMeiosTable&\Cake\ORM\Association\BelongsTo $ColetaMeios
 * @property \App\Model\Table\AtualizacoesTable&\Cake\ORM\Association\HasMany $Atualizacoes
 * @property \App\Model\Table\PagamentosTable&\Cake\ORM\Association\HasMany $Pagamentos
 * @property \App\Model\Table\RotaPedidosTable&\Cake\ORM\Association\HasMany $RotaPedidos
 * @method \App\Model\Entity\Pedido newEmptyEntity()
 * @method \App\Model\Entity\Pedido newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Pedido[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pedido get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pedido findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Pedido patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pedido[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pedido|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pedido saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pedido[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pedido[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pedido[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pedido[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PedidosTable extends Table
{
    use CurrentUserTrait;

    //------------------------------MODALIDADE DE DISTRIBUIÇÃO------------------------------
    /** @var string Modalidade coleta: a empresa designa os entregadores para buscarem os pacotes */
    public const COLETA = 'coleta';

    /** @var string Modalidade entrega: o cliente envia os produtos para o centro de distribuição */
    public const ENTREGA = 'entrega';

    /** @var string[] Lista das modalidades de distribuição */
    public const MODALIDADE_DISTRIBUICAO = [
        self::COLETA => 'Coleta',
        self::ENTREGA => 'Entrega',
    ];

    //------------------------------STATUS------------------------------
    /** @var string Pedido com pagamento confirmado */
    public const CONFIRMADO = 'confirmado';

    /** @var string Pedido com pagamento pendente */
    public const PENDENTE = 'pendente';

    /** @var string Pedido cancelado */
    public const CANCELADO = 'cancelado';

    /** @var string Pedido em processo de entrega */
    public const PROCESSO_COLETA = 'processo-de-coleta';

    /** @var string Pedido em processo de entrega */
    public const PROCESSO_ENTREGA = 'processo-de-entrega';

    /** @var string Pedido finalizado, isto é, a entrega já foi realizada */
    public const FINALIZADO = 'finalizado';

    /** @var string[] Lista de status */
    public const STATUS = [
        self::PENDENTE => 'Pagamento pendente',
        self::CONFIRMADO => 'Pagamento confirmado',
        self::PROCESSO_COLETA => 'Processo de coleta',
        self::PROCESSO_ENTREGA => 'Processo de entrega',
        self::CANCELADO => 'Cancelado',
        self::FINALIZADO => 'Finalizado',
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pedidos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'comprovante' => [
                'transformer' => 'Winsite\File\Transformer\WinsiteTransformer',
                'writer' => 'Winsite\File\Writer\WinsiteWriter',
                'nameCallback' => function (
                    Table $table,
                    EntityInterface $entity,
                    UploadedFileInterface $data,
                    $field,
                    $settings
                ) {
                    $extension = pathinfo($data->getClientFilename(), PATHINFO_EXTENSION);
                    $nomeSemExtension = str_replace('.' . $extension, '', $data->getClientFilename());
                    $slug = mb_strtolower(
                        Text::slug($nomeSemExtension)
                        . '-' . (int)microtime(true)
                        . '.' . $extension
                    );

                    return $slug;
                },
                'deleteCallback' => function ($path, EntityInterface $entity, $field, $settings) {
                    return [
                        $path . $entity->{$field},
                    ];
                },
                'keepFilesOnDelete' => false,
            ],
        ]);

        $this->belongsTo('Pessoas', [
            'foreignKey' => 'cliente_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Objetos', [
            'foreignKey' => 'objeto_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Filiais', [
            'foreignKey' => 'filial_id',
        ]);
        $this->belongsTo('EntregaMeios', [
            'foreignKey' => 'entrega_meio_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ColetaMeios', [
            'className' => 'EntregaMeios',
            'foreignKey' => 'coleta_meio_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('Atualizacoes', [
            'foreignKey' => 'pedido_id',
        ]);
        $this->hasMany('EntregaTentativas', [
            'foreignKey' => 'pedido_id',
        ]);
        $this->hasMany('Pagamentos', [
            'foreignKey' => 'pedido_id',
        ]);
        $this->hasMany('RotaPedidos', [
            'foreignKey' => 'pedido_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('instrucoes')
            ->allowEmptyString('instrucoes');

        $validator
            ->scalar('observacoes_tratativa_coleta')
            ->allowEmptyString('observacoes_tratativa_coleta');

        $validator
            ->scalar('observacoes_tratativa_entrega')
            ->allowEmptyString('observacoes_tratativa_entrega');

        $validator
            ->scalar('modalidade_entrega')
            ->maxLength('modalidade_entrega', 255)
            ->allowEmptyString('modalidade_entrega');

        $validator
            ->scalar('modalidade_distribuicao')
            ->inList(
                'modalidade_distribuicao',
                array_keys(self::MODALIDADE_DISTRIBUICAO),
                'Modalidade de distribuição inválida!',
            )
            ->requirePresence('modalidade_distribuicao', 'create')
            ->notEmptyString('modalidade_distribuicao');

        $validator
            ->scalar('meio_entrega')
            ->maxLength('meio_entrega', 255)
            ->allowEmptyString('meio_entrega', 'Este campo é obrigatório', 'create');

        $validator
            ->decimal('valor_total')
            ->requirePresence('valor_total', 'create', 'Este campo é obrigatório')
            ->notEmptyString('valor_total', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('status')
            ->inList('status', array_keys(self::STATUS), 'Status inválido!')
            ->requirePresence('status', 'create', 'Este campo é obrigatório')
            ->notEmptyString('status', 'Este campo é obrigatório', 'create');

        $validator
            ->date('prazo_envio', ['dmy'])
            ->allowEmptyDate('prazo_envio');

        $validator
            ->date('previsao_entrega', ['dmy'])
            ->allowEmptyDate('previsao_entrega');

        $validator
            ->date('previsao_coleta', ['dmy'])
            ->allowEmptyDate('previsao_coleta');

        $validator
            ->dateTime('data_tratativa_coleta', ['dmy'])
            ->allowEmptyDate('data_tratativa_coleta');

        $validator
            ->dateTime('data_tratativa_entrega', ['dmy'])
            ->allowEmptyDate('data_tratativa_entrega');

        $validator
            ->dateTime('data_chegada', ['dmy'])
            ->allowEmptyDate('data_chegada');

        $validator
            ->dateTime('data_postagem', ['dmy'])
            ->allowEmptyDate('data_postagem');

        $validator
            ->dateTime('data_entrega', ['dmy'])
            ->allowEmptyDate('data_entrega');

        $validator
            ->add('comprovante', [
                'uploadError' => [
                    'rule' => 'uploadError',
                    'message' => 'O upload da imagem falhou.',
                    'allowEmpty' => true,
                ],
                'mimeType' => [
                    'rule' => ['mimeType', ['image/png', 'image/jpg', 'image/jpeg']],
                    'message' => 'Por favor insira a imagem no formato png, jpg ou jpeg.',
                    'allowEmpty' => true,
                ],
                'fileSize' => [
                    'rule' => ['fileSize', '<=', '5MB'],
                    'message' => 'O Tamanho da imagem não pode passar de 5MB',
                    'allowEmpty' => true,
                ],
            ])
            ->allowEmptyFile('comprovante');

        $validator
            ->scalar('nome_recebedor')
            ->maxLength('nome_recebedor', 255)
            ->allowEmptyString('nome_recebedor');

        $validator
            ->scalar('documento_recebedor')
            ->maxLength('documento_recebedor', 255)
            ->allowEmptyString('documento_recebedor');

        $validator
            ->scalar('dados_filial')
            ->allowEmptyString('dados_filial');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['cliente_id'], 'Pessoas'), [
            'errorField' => 'cliente_id',
            'message' => 'Cliente inválido!',
        ]);
        $rules->add($rules->existsIn(['objeto_id'], 'Objetos'), [
            'errorField' => 'objeto_id',
            'message' => 'Objeto inválido!',
        ]);
        $rules->add($rules->existsIn(['filial_id'], 'Filiais'), [
            'errorField' => 'filial_id',
            'message' => 'Filial inválida!',
        ]);
        $rules->add($rules->existsIn(['entrega_meio_id'], 'EntregaMeios'), [
            'errorField' => 'entrega_meio_id',
            'message' => 'Meio de entrega inválido!',
        ]);
        $rules->add($rules->existsIn(['coleta_meio_id'], 'ColetaMeios'), [
            'errorField' => 'coleta_meio_id',
            'message' => 'Meio de coleta inválido!',
        ]);

        return $rules;
    }

    /**
     * @param \Cake\Event\Event $event dados do evento
     * @param \App\Model\Entity\Pedido $pedido dados da entity
     * @param \ArrayObject $options dados de options
     * @return void
     */
    public function beforeSave(Event $event, Pedido $pedido, \ArrayObject $options)
    {
        if (!empty($pedido->comprovante) && $pedido->isDirty('comprovante') && !$pedido->isNew()) {
            $caminho = 'files/Pedidos/comprovante/';
            $arquivo = ($pedido->extractOriginal(['comprovante'])['comprovante']);

            if (!empty($arquivo) && file_exists($caminho . $arquivo)) {
                unlink($caminho . $arquivo);
            }
        }

        // Atualiza meio de entrega
        if (!empty($pedido->entrega_meio_id) && $pedido->isDirty('entrega_meio_id')) {
            $meio_entrega = $this->EntregaMeios->get($pedido->entrega_meio_id);

            $pedido->meio_entrega = $meio_entrega->nome;
        }
        if (!empty($pedido->coleta_meio_id) && $pedido->isDirty('coleta_meio_id')) {
            $meio_coleta = $this->ColetaMeios->get($pedido->coleta_meio_id);

            $pedido->meio_coleta = $meio_coleta->nome;
        }
        // Atualiza dados filial
        if (!empty($pedido->filial_id) && $pedido->isDirty('filial_id')) {
            $filial = $this->Filiais->get($pedido->filial_id, [
                'contain' => [
                    'Enderecos' => [
                        'Cidades' => [
                            'Estados',
                        ],
                    ],
                ],
            ]);

            $pedido->dados_filial = "{$filial->nome}, localizado(a) na(o) " .
                "{$filial->endereco->logradouro}, {$filial->endereco->numero} - {$filial->endereco->bairro}";

            if (!empty($filial->endereco->complemento)) {
                $pedido->dados_filial .= ", {$filial->endereco->complemento}";
            }
            if (!empty($filial->endereco->referencia)) {
                $pedido->dados_filial .= ", {$filial->endereco->referencia}";
            }
            $pedido->dados_filial .= ". Na cidade de {$filial->endereco->cidade->nome}/" .
            "{$filial->endereco->cidade->estado->sigla}, CEP: {$filial->endereco->cep}.";
        }
        if (!empty($pedido->dados_filial) && empty($pedido->filial_id)) {
            $pedido->dados_filial = null;
        }
    }

    /**
     * PedidosSemRotaColetas method
     * Retorna os pedidos que não possuem rota de entrega para a modalidade de distribuição coleta
     *
     * @return \Cake\ORM\Query
     */
    public function pedidosSemRotaColetas(): Query
    {
        $query = $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Pedido $entity) {
                    if ($entity->etapa === PedidosTable::COLETA) {
                        $endereco = $entity->objeto->endereco_coleta->endereco_formatado;
                    } else {
                        $endereco = $entity->objeto->endereco_entrega->endereco_formatado;
                    }

                    return sprintf(
                        '#%s [%s] - %s',
                        $entity->id,
                        self::MODALIDADE_DISTRIBUICAO[$entity->etapa],
                        $endereco,
                    );
                },
            ])
            ->contain([
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
            ])
            ->leftJoinWith('RotaPedidos', function (Query $query) {
                $query->leftJoinWith('Rotas');

                return $query;
            })
            ->where(function (QueryExpression $expression) {
                $or = $expression->or(function (QueryExpression $orExpression) {
                    return $orExpression
                        ->notIn('Rotas.status', [
                            RotasTable::AGUARDANDO_INICIO,
                            RotasTable::EM_ROTA, RotasTable::ATRASADA,
                        ])
                        ->isNull('RotaPedidos.id');
                });

                $expression
                    ->add($or)
                    ->eq('Pedidos.modalidade_distribuicao', self::COLETA)
                    ->notIn('Pedidos.status', [self::PENDENTE, self::CANCELADO, self::FINALIZADO])
                    ->isNull('Pedidos.data_chegada') //objeto não coletado ou recebido
                    ->isNull('Pedidos.data_entrega'); //objeto não entregue

                return $expression;
            });

        return $query;
    }

    /**
     * PedidosSemRotaEntregas method
     * Retorna os pedidos que não possuem rota de entrega: pedidos da modalidade COLETA que já foram coletados e
     * pedidos da modalidade ENTREGA
     *
     * @return \Cake\ORM\Query
     */
    public function pedidosSemRotaEntregas(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Pedido $entity) {
                    if ($entity->etapa === PedidosTable::COLETA) {
                        $endereco = $entity->objeto->endereco_coleta->endereco_formatado;
                    } else {
                        $endereco = $entity->objeto->endereco_entrega->endereco_formatado;
                    }

                    return sprintf(
                        '#%s [%s] - %s',
                        $entity->id,
                        self::MODALIDADE_DISTRIBUICAO[$entity->etapa],
                        $endereco,
                    );
                },
            ])
            ->contain([
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
            ])
            ->leftJoinWith('RotaPedidos', function (Query $query) {
                $query->leftJoinWith('Rotas');

                return $query;
            })
            ->where(function (QueryExpression $expression) {
                $or = $expression->or(function (QueryExpression $orExpression) {
                    return $orExpression
                        ->eq('temRotaAtiva(Pedidos.id, \'' . RotaPedidosTable::ENTREGA . '\' )', false, 'boolean')
                        ->isNull('RotaPedidos.id');
                });

                $expression
                    ->add($or)
                    ->notIn('Pedidos.status', [self::PENDENTE, self::CANCELADO, self::FINALIZADO])
                    ->isNotNull('Pedidos.data_chegada') //objeto não coletado ou recebido
                    ->isNull('Pedidos.data_entrega'); //objeto não entregue

                return $expression;
            });
    }

    /**
     * pedidosSemRota method
     * Retorna os pedidos que não possuem rota
     *
     * @return \Cake\ORM\Query
     */
    public function pedidosSemRota(): Query
    {
        return $this->pedidosSemRotaColetas()->union($this->pedidosSemRotaEntregas());
    }
}
