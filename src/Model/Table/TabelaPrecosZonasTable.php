<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TabelaPrecosZonas Model
 *
 * @property \App\Model\Table\TabelaPrecosTable&\Cake\ORM\Association\BelongsTo $TabelaPrecos
 * @property \App\Model\Table\ZonasTable&\Cake\ORM\Association\BelongsTo $Zonas
 * @method \App\Model\Entity\TabelaPrecosZona newEmptyEntity()
 * @method \App\Model\Entity\TabelaPrecosZona newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona get($primaryKey, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TabelaPrecosZona[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TabelaPrecosZonasTable extends Table
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

        $this->setTable('tabela_precos_zonas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('TabelaPrecos', [
            'foreignKey' => 'tabela_preco_id',
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
        $rules->add($rules->existsIn(['tabela_preco_id'], 'TabelaPrecos'), ['errorField' => 'tabela_preco_id']);
        $rules->add($rules->existsIn(['zona_id'], 'Zonas'), ['errorField' => 'zona_id']);

        return $rules;
    }
}
