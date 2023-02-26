<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Rota;
use ArrayObject;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Rotas Model
 *
 * @property \App\Model\Table\PessoasTable&\Cake\ORM\Association\BelongsTo $Pessoas
 * @property \App\Model\Table\RotaPedidosTable&\Cake\ORM\Association\HasMany $RotaPedidos
 * @method \App\Model\Entity\Rota newEmptyEntity()
 * @method \App\Model\Entity\Rota newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Rota[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rota get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rota findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Rota patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rota[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rota|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rota saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rota[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rota[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rota[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rota[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RotasTable extends Table
{
    use CurrentUserTrait;

    //------------------------------STATUS------------------------------
    /** @var string Status aguardando = data de hoje é maior que a data de saida */
    public const AGUARDANDO_INICIO = 'aguardando-inicio';

    /** @var string Status atrasada = rota não iniciada na data de saída */
    public const ATRASADA = 'atrasada';

    /** @var string Status em roda = data de hoje é igual a data de saida */
    public const EM_ROTA = 'em-rota';

    /** @var string Status finalizado = rota finalizada */
    public const FINALIZADA = 'finalizado';

    /** @var string[] Lista de status */
    public const STATUS = [
        self::AGUARDANDO_INICIO => 'Aguardando início',
        self::ATRASADA => 'Atrasada',
        self::EM_ROTA => 'Em rota',
        self::FINALIZADA => 'Finalizada',
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

        $this->setTable('rotas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Pessoas', [
            'foreignKey' => 'entregador_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('RotaPedidos', [
            'foreignKey' => 'rota_id',
            'saveStrategy' => 'replace',
            'dependent' => true,
            'cascadeCallbacks' => true,
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
            ->date('data_saida', ['dmy'])
            ->requirePresence('data_saida', 'create', 'Este campo é obrigatório')
            ->notEmptyDate('data_saida', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('status')
            ->inList('status', array_keys(self::STATUS), 'Status inválido!')
            ->notEmptyString('status', 'Este campo é obrigatório', 'create');

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
        $rules->add($rules->existsIn(['entregador_id'], 'Pessoas'), [
            'errorField' => 'entregador_id',
            'message' => 'Entregador inválido',
        ]);
        $rules->add($rules->isUnique(
            ['entregador_id', 'data_saida'],
            'Atenção! Este entregador já possui uma rota para este dia.'
        ));

        return $rules;
    }

    /**
     * beforeSave method
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        if ($entity->isNew()) {
            $dataHoje = new FrozenDate();

            if ($entity->data_saida > $dataHoje) {
                $entity->status = self::AGUARDANDO_INICIO;
            } elseif ($entity->data_saida < $dataHoje) {
                $entity->status = self::ATRASADA;
            } else {
                $entity->status = self::EM_ROTA;
            }
        }
    }

    /**
     * rotasAtivas method
     * Retorna todas as rotas que estão ativas
     *
     * @return \Cake\ORM\Query
     */
    public function rotasAtivas(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Rota $entity) {
                    return sprintf('#%s - %s [%s]', $entity->id, $entity->pessoa->nome, $entity->data_saida);
                },
            ])
            ->contain([
                'Pessoas',
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->notEq('Rotas.status', RotasTable::FINALIZADA);

                return $expression;
            });
    }

    /**
     * afterSave callback.
     *
     * @param \Cake\Event\EventInterface $event The afterSave event that was fired.
     * @param \App\Model\Entity\Rota $rota The entity that was saved.
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function afterSave(EventInterface $event, Rota $rota, ArrayObject $options)
    {
        if (!empty($rota->status) && $rota->isDirty('status')) {
            if ($rota->status === RotasTable::EM_ROTA) {
                foreach ($rota->rota_pedidos as $rotaPedido) {
                    if ($rotaPedido->tipo === RotaPedidosTable::ENTREGA) {
                        $pedido = $this->RotaPedidos->Pedidos->get($rotaPedido->pedido_id);

                        if (empty($pedido->data_postagem)) {
                            $pedido->data_postagem = new FrozenTime();
                            $this->RotaPedidos->Pedidos->saveOrFail($pedido);

                            $atualizacao = [
                                'pedido_id' => $pedido->id,
                                'titulo' => ($rotaPedido->tipo === RotaPedidosTable::COLETA ?
                                    'Entregador em rota de coleta' : 'Pedido em rota de entrega'),
                                'data' => new FrozenTime(),
                            ];
                            $this->RotaPedidos->Pedidos->Atualizacoes->add($atualizacao);
                        }
                    }
                }
            }

            // enviar email de mudança de status de rota
            $tableLocator = new TableLocator();
            $QueuedJobs = $tableLocator->get('Queue.QueuedJobs');

            $QueuedJobs->createJob('EmailAtualizacaoRotaAdmin', [
                'rota_id' => $rota->id,
            ]);
        }
    }
}
