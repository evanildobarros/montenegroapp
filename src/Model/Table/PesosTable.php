<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Peso;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pesos Model
 *
 * @property \App\Model\Table\TabelaPrecosTable&\Cake\ORM\Association\BelongsTo $TabelaPrecos
 * @property \App\Model\Table\TaxasTable&\Cake\ORM\Association\HasMany $Taxas
 * @method \App\Model\Entity\Peso newEmptyEntity()
 * @method \App\Model\Entity\Peso newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Peso[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Peso get($primaryKey, $options = [])
 * @method \App\Model\Entity\Peso findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Peso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Peso[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Peso|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Peso saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Peso[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Peso[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Peso[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Peso[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PesosTable extends Table
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

        $this->setTable('pesos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('TabelaPrecos', [
            'foreignKey' => 'tabela_preco_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Taxas', [
            'foreignKey' => 'peso_id',
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
            ->integer('peso_minimo')
            ->allowEmptyString('peso_minimo');

        $validator
            ->integer('peso_maximo')
            ->allowEmptyString('peso_maximo');

        $validator
            ->boolean('quilo_adicional')
            ->notEmptyString('quilo_adicional');

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
        $rules->add($rules->existsIn(['tabela_preco_id'], 'TabelaPrecos'), [
            'errorField' => 'tabela_preco_id',
            'message' => 'Tabela de preço inválida!',
        ]);

        return $rules;
    }

    /**
     * beforeSave method
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \App\Model\Entity\Peso $peso The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(EventInterface $event, Peso $peso, \ArrayObject $options)
    {
        /*
         * Quando o peso mínimo ou máximo sofrer alguma modificação o registro na tabela pesos deve ser
         * excluído e inserido novamente. Isto está sendo feito por causa de uma trigger (que só funciona no add)
         * nesta tabela que verifica se existem intervalos que se sobrepõe.
        */
        if (!$peso->isNew() && ($peso->isDirty('peso_minimo') || $peso->isDirty('peso_maximo'))) {
            $newPeso = clone $peso;
            $this->deleteOrFail($newPeso);

            $peso->unset('id');
            $options['_primary'] = true;
            $peso->setNew(true);

            foreach ($peso->taxas as $taxa) {
                $taxa->unset('peso_id');
                $taxa->setNew(true);
            }
        }
    }
}
