<?php
declare(strict_types=1);

namespace Correios\Model\Table;

use Cake\ORM\Table;

/**
 * LogLocalidade Model
 *
 * @property \Correios\Model\Table\LogBairroTable&\Cake\ORM\Association\HasMany $LogBairro
 * @property \Correios\Model\Table\LogFaixaLocalidadeTable&\Cake\ORM\Association\HasMany $LogFaixaLocalidade
 * @method \Correios\Model\Entity\LogLocalidade newEmptyEntity()
 * @method \Correios\Model\Entity\LogLocalidade newEntity(array $data, array $options = [])
 * @method \Correios\Model\Entity\LogLocalidade[] newEntities(array $data, array $options = [])
 * @method \Correios\Model\Entity\LogLocalidade get($primaryKey, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Correios\Model\Entity\LogLocalidade[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Correios\Model\Entity\LogLocalidade|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Correios\Model\Entity\LogLocalidade[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class LogLocalidadeTable extends Table
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

        $this->setTable('log_localidade');
        $this->setPrimaryKey('loc_nu');
        $this->setDisplayField('loc_no');

        $this->hasMany('Correios.LogBairro', [
            'foreignKey' => 'loc_nu',
        ]);
        $this->hasMany('Correios.LogFaixaLocalidade', [
            'foreignKey' => 'loc_nu',
        ]);
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
