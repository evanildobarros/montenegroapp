<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Taxas Model
 *
 * @property \App\Model\Table\PesosTable&\Cake\ORM\Association\BelongsTo $Pesos
 * @property \App\Model\Table\ZonasTable&\Cake\ORM\Association\BelongsTo $Zonas
 * @method \App\Model\Entity\Taxa newEmptyEntity()
 * @method \App\Model\Entity\Taxa newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Taxa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Taxa get($primaryKey, $options = [])
 * @method \App\Model\Entity\Taxa findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Taxa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Taxa[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Taxa|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Taxa saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Taxa[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Taxa[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Taxa[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Taxa[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TaxasTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('taxas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Pesos', [
            'foreignKey' => 'peso_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Zonas', [
            'foreignKey' => 'zona_id',
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
            ->decimal('valor')
            ->requirePresence('valor', 'create')
            ->notEmptyString('valor');

        $validator
            ->integer('tempo_estimado')
            ->requirePresence('tempo_estimado', 'create')
            ->notEmptyString('tempo_estimado');

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
        $rules->add($rules->existsIn(['peso_id'], 'Pesos'), [
            'errorField' => 'peso_id',
            'message' => 'Peso inválido!',
        ]);
        $rules->add($rules->existsIn(['zona_id'], 'Zonas'), [
            'errorField' => 'zona_id',
            'message' => 'Zona inválida!',
        ]);
        $rules->add($rules->isUnique(['zona_id', 'peso_id'], 'Zonas'), [
            'errorField' => 'zona_id',
            'message' => 'Atenção! Está zona já possui esta faixa de peso',
        ]);

        return $rules;
    }
}
