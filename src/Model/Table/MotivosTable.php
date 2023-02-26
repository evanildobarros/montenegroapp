<?php
declare(strict_types=1);

namespace App\Model\Table;

use AuditLog\Model\Table\CurrentUserTrait;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Motivos Model
 *
 * @property \App\Model\Table\TentativasTable&\Cake\ORM\Association\HasMany $Tentativas
 * @method \App\Model\Entity\Motivo newEmptyEntity()
 * @method \App\Model\Entity\Motivo newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Motivo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Motivo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Motivo findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Motivo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Motivo[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Motivo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Motivo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Motivo[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Motivo[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Motivo[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Motivo[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MotivosTable extends Table
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

        $this->setTable('motivos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->hasMany('Tentativas', [
            'foreignKey' => 'motivo_id',
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
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create', 'Este campo é obrigatório')
            ->notEmptyString('nome', 'Este campo é obrigatório', 'create');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create', 'Este campo é obrigatório')
            ->notEmptyString('status', 'Este campo é obrigatório', 'create');

        return $validator;
    }

    /**
     * MotivosAtivos method
     * Retorna todos os motivos com status true
     *
     * @return \Cake\ORM\Query
     */
    public function motivosAtivos(): Query
    {
        return $this
            ->find('list')
            ->where(['status', true]);
    }
}
