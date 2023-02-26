<?php
declare(strict_types=1);

namespace Correios\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LogFaixaBairro Model
 *
 * @property \Correios\Model\Table\LogLocalidadeTable&\Cake\ORM\Association\BelongsTo $LogLocalidade
 * @method \Correios\Model\Entity\LogFaixaBairro newEmptyEntity()
 * @method \Correios\Model\Entity\LogFaixaBairro newEntity(array $data, array $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro[] newEntities(array $data, array $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro get($primaryKey, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogFaixaBairro[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class LogFaixaLocalidadeTable extends Table
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

        $this->setTable('log_faixa_localidade');

        $this->belongsTo('Correios.LogLocalidade', [
            'foreignKey' => 'loc_nu',
            'joinType' => 'LEFT',
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
            ->integer('loc_nu')
            ->allowEmptyString('loc_nu');

        $validator
            ->scalar('loc_cep_ini')
            ->maxLength('loc_cep_ini', 8)
            ->allowEmptyString('loc_cep_ini');

        $validator
            ->scalar('loc_cep_fim')
            ->maxLength('loc_cep_fim', 8)
            ->allowEmptyString('loc_cep_fim');

        return $validator;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName(): string
    {
        return 'correios';
    }
}
