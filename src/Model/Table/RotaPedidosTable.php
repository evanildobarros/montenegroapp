<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\RotaPedido;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RotaPedidos Model
 *
 * @property \App\Model\Table\TentativasTable&\Cake\ORM\Association\HasMany $Tentativas
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\BelongsTo $Pedidos
 * @property \App\Model\Table\RotaPedidosTable&\Cake\ORM\Association\BelongsTo $ParentRotaPedidos
 * @property \App\Model\Table\RotasTable&\Cake\ORM\Association\BelongsTo $Rotas
 * @method \App\Model\Entity\RotaPedido newEmptyEntity()
 * @method \App\Model\Entity\RotaPedido newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RotaPedido[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RotaPedido get($primaryKey, $options = [])
 * @method \App\Model\Entity\RotaPedido findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RotaPedido patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RotaPedido[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RotaPedido|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RotaPedido saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RotaPedido[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RotaPedido[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RotaPedido[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RotaPedido[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RotaPedidosTable extends Table
{
    use CurrentUserTrait;

    //------------------------------TIPO------------------------------
    /** @var string Tipo Entrega */
    public const ENTREGA = PedidosTable::ENTREGA;

    /** @var string Tipo Coleta */
    public const COLETA = PedidosTable::COLETA;

    /** @var string[] Lista de todos os possíveis tipos */
    public const TIPOS = [
        self::ENTREGA => 'Entrega',
        self::COLETA => 'Coleta',
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

        $this->setTable('rota_pedidos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Pedidos', [
            'foreignKey' => 'pedido_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Rotas', [
            'foreignKey' => 'rota_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ParentRotaPedidos', [
            'className' => 'RotaPedidos',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('Tentativas', [
            'foreignKey' => 'rota_pedido_id',
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
            ->integer('ordem')
            ->allowEmptyString('ordem');

        $validator
            ->boolean('entregue')
            ->allowEmptyString('entregue');

        $validator
            ->scalar('tipo')
            ->inList('tipo', array_keys(self::TIPOS), 'Tipo inválido!')
            ->requirePresence('tipo', 'create', 'Este campo é obrigatório')
            ->notEmptyString('tipo');

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
        $rules->add($rules->existsIn(['pedido_id'], 'Pedidos'), [
            'errorField' => 'pedido_id',
            'message' => 'Pedido inválido!',
        ]);
        $rules->add($rules->existsIn(['rota_id'], 'Rotas'), [
            'errorField' => 'rota_id',
            'message' => 'Rota inválida!',
        ]);
        $rules->add($rules->existsIn(['parent_id'], 'ParentRotaPedidos'), [
            'errorField' => 'parent_id',
            'message' => 'Parada inválida',
        ]);

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
        if (!empty($entity->rota_id) && $entity->isDirty('rota_id')) {
            $entity->ordem = $this->proximaOrdem($entity->rota_id);
        }
    }

    /**
     * TemRotaAtiva method
     *
     * @param int $pedido_id Pedido id
     * @param string $tipo tipo da rota
     * @return bool Return
     */
    public function temRotaAtiva($pedido_id, $tipo = null): bool
    {
        $rotaDefinida = $this->Pedidos->RotaPedidos
            ->find()
            ->contain('Rotas')
            ->where(function (QueryExpression $expression) use ($pedido_id, $tipo) {
                $expression
                    ->notEq('Rotas.status', RotasTable::FINALIZADA)
                    ->eq('RotaPedidos.pedido_id', $pedido_id);

                if (!empty($tipo)) {
                    $expression->eq('RotaPedidos.tipo', $tipo);
                }

                return $expression;
            })
            ->count();

        return $rotaDefinida > 0;
    }

    /**
     * MarcarEntregue method
     *
     * @param int $pedido_id Pedido id
     * @return void Return
     */
    public function marcarEntregue($pedido_id)
    {
        $this->Pedidos->RotaPedidos
            ->find()
            ->contain('Rotas')
            ->where(function (QueryExpression $expression) use ($pedido_id) {
                $expression
                    ->notEq('Rotas.status', RotasTable::FINALIZADA)
                    ->eq('RotaPedidos.pedido_id', $pedido_id);

                return $expression;
            })
            ->each(function (RotaPedido $rotaPedido) {
                $rotaPedido->entregue = true;
                $this->saveOrFail($rotaPedido);
            });
    }

    /**
     * Reordenar method
     *
     * @param int $rota_id Rota id
     * @return void Return
     */
    public function reordenar($rota_id)
    {
        $ordem = 1;
        $this->Pedidos->RotaPedidos
            ->find()
            ->where(function (QueryExpression $expression) use ($rota_id) {
                $expression
                    ->eq('RotaPedidos.rota_id', $rota_id);

                return $expression;
            })
            ->orderAsc('ordem')
            ->each(function (RotaPedido $rotaPedido) use (&$ordem) {
                $rotaPedido->ordem = $ordem;
                $this->saveOrFail($rotaPedido);

                $ordem++;
            });
    }

    /**
     * ProximaOrdem method
     *
     * @param int $rota_id Rota id
     * @return int Return
     */
    public function proximaOrdem($rota_id): int
    {
        $ultimaParada = $this
            ->find()
            ->where(['rota_id' => $rota_id])
            ->orderDesc('ordem')
            ->first();

        return empty($ultimaParada) ? 1 : $ultimaParada->ordem + 1;
    }
}
