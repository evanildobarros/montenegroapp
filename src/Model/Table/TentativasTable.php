<?php
declare(strict_types=1);

namespace App\Model\Table;

use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tentativas Model
 *
 * @property \App\Model\Table\RotaPedidosTable&\Cake\ORM\Association\BelongsTo $RotaPedidos
 * @property \App\Model\Table\MotivosTable&\Cake\ORM\Association\BelongsTo $Motivos
 * @method \App\Model\Entity\Tentativa newEmptyEntity()
 * @method \App\Model\Entity\Tentativa newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Tentativa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tentativa get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tentativa findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Tentativa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tentativa[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tentativa|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tentativa saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tentativa[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tentativa[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tentativa[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tentativa[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TentativasTable extends Table
{
    use CurrentUserTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tentativas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('RotaPedidos', [
            'foreignKey' => 'rota_pedido_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Motivos', [
            'foreignKey' => 'motivo_id',
            'joinType' => 'INNER',
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
            ->scalar('nome_motivo')
            ->maxLength('nome_motivo', 255)
            ->allowEmptyString('nome_motivo');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        $validator
            ->dateTime('data', ['dmy'])
            ->requirePresence('data', 'create', 'Este campo é obrigatório')
            ->notEmptyDateTime('data', 'Este campo é obrigatório');

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
        $rules->add($rules->existsIn(['rota_pedido_id'], 'RotaPedidos'), [
            'errorField' => 'rota_pedido_id',
            'message' => 'Parada inválida!',
        ]);
        $rules->add($rules->existsIn(['motivo_id'], 'Motivos'), [
            'errorField' => 'motivo_id',
            'message' => 'Motivo inválido!',
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
        if (!empty($entity->motivo_id) && $entity->isDirty('motivo_id')) {
            /** @var \App\Model\Entity\Motivo $motivo */
            $motivo = $this->Motivos
                ->find()
                ->where(['id' => $entity->motivo_id])
                ->firstOrFail();

            $entity->nome_motivo = $motivo->nome;
        }
    }
}
