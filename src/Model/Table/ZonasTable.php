<?php
declare(strict_types=1);

namespace App\Model\Table;

use AuditLog\Model\Table\CurrentUserTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Zonas Model
 *
 * @property \App\Model\Table\TaxasTable&\Cake\ORM\Association\HasMany $Taxas
 * @property \App\Model\Table\FaixasTable&\Cake\ORM\Association\HasMany $Faixas
 * @property \App\Model\Table\CidadesTable&\Cake\ORM\Association\BelongsToMany $Cidades
 * @property \App\Model\Table\TabelaPrecosTable&\Cake\ORM\Association\BelongsToMany $TabelaPrecos
 * @method \App\Model\Entity\Zona newEmptyEntity()
 * @method \App\Model\Entity\Zona newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Zona[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Zona get($primaryKey, $options = [])
 * @method \App\Model\Entity\Zona findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Zona patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Zona[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Zona|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Zona saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Zona[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Zona[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Zona[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Zona[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ZonasTable extends Table
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

        $this->setTable('zonas');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->hasMany('Taxas', [
            'foreignKey' => 'zona_id',
        ]);
        $this->hasMany('Faixas', [
            'foreignKey' => 'zona_id',
            'saveStrategy' => 'replace',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsTo('Cidades', [
            'foreignKey' => 'cidade_id',
            'joinTable' => 'cidades',
        ]);
        $this->belongsToMany('TabelaPrecos', [
            'foreignKey' => 'zona_id',
            'targetForeignKey' => 'tabela_preco_id',
            'joinTable' => 'tabela_precos_zonas',
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
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('nome_abreviado')
            ->requirePresence('nome_abreviado', 'create')
            ->allowEmptyString('nome_abreviado');

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
        $rules->add($rules->existsIn(['cidade_id'], 'Cidades'), [
            'errorField' => 'cidade_id',
            'message' => 'Cidade inválida!',
        ]);

        return $rules;
    }

}
