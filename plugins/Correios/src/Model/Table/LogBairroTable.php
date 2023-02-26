<?php
declare(strict_types=1);

namespace Correios\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LogBairro Model
 *
 * @property \Correios\Model\Table\LogLocalidadeTable&\Cake\ORM\Association\BelongsTo $LogLocalidade
 * @property \Correios\Model\Table\LogFaixaBairroTable&\Cake\ORM\Association\HasMany $LogFaixaBairro
 * @property \Correios\Model\Table\LogFaixaLocalidadeTable&\Cake\ORM\Association\HasMany $LogFaixaLocalidade
 * @method \Correios\Model\Entity\LogBairro newEmptyEntity()
 * @method \Correios\Model\Entity\LogBairro newEntity(array $data, array $options = [])
 * @method \Correios\Model\Entity\LogBairro[] newEntities(array $data, array $options = [])
 * @method \Correios\Model\Entity\LogBairro get($primaryKey, $options = [])
 * @method \Correios\Model\Entity\LogBairro findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Correios\Model\Entity\LogBairro patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Correios\Model\Entity\LogBairro[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Correios\Model\Entity\LogBairro|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Correios\Model\Entity\LogBairro saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Correios\Model\Entity\LogBairro[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogBairro[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogBairro[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogBairro[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class LogBairroTable extends Table
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

        $this->setTable('log_bairro');
        $this->setPrimaryKey('bai_nu');
        $this->setDisplayField('bai_no');

        $this->belongsTo('Correios.LogLocalidade', [
            'foreignKey' => 'loc_nu',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Correios.LogFaixaBairro', [
            'foreignKey' => 'bai_nu',
        ]);
        $this->hasMany('Correios.LogFaixaLocalidade', [
            'foreignKey' => 'loc_nu',
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
            ->integer('bai_nu')
            ->allowEmptyString('bai_nu');

        $validator
            ->scalar('ufe_sg')
            ->maxLength('ufe_sg', 2)
            ->allowEmptyString('ufe_sg');

        $validator
            ->integer('loc_nu')
            ->allowEmptyString('loc_nu');

        $validator
            ->scalar('bai_no')
            ->maxLength('bai_no', 72)
            ->allowEmptyString('bai_no');

        $validator
            ->scalar('bai_no_abrev')
            ->maxLength('bai_no_abrev', 36)
            ->allowEmptyString('bai_no_abrev');

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
