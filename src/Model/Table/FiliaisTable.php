<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Filial;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Filiais Model
 *
 * @property \App\Model\Table\EnderecosTable&\Cake\ORM\Association\BelongsTo $Enderecos
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\HasMany $Pedidos
 * @method \App\Model\Entity\Filial newEmptyEntity()
 * @method \App\Model\Entity\Filial newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Filial[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Filial get($primaryKey, $options = [])
 * @method \App\Model\Entity\Filial findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Filial patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Filial[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Filial|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Filial saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Filial[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Filial[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Filial[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Filial[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FiliaisTable extends Table
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

        $this->setTable('filiais');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Enderecos', [
            'foreignKey' => 'endereco_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Pedidos', [
            'foreignKey' => 'filial_id',
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
            ->scalar('horario_atendimento')
            ->maxLength('horario_atendimento', 255)
            ->requirePresence('horario_atendimento', 'create', 'Este campo é obrigatório')
            ->notEmptyString('horario_atendimento', 'Este campo é obrigatório', 'create');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create', 'Este campo é obrigatório')
            ->notEmptyString('status', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

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
        $rules->add($rules->existsIn(['endereco_id'], 'Enderecos'), [
            'errorField' => 'endereco_id',
            'message' => 'Endereço inválido!',
        ]);

        return $rules;
    }

    /**
     * listaAll method
     * Retorna query para listagem de filiais
     *
     * @return \Cake\ORM\Query
     */
    public function listaAll(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Filial $entity) {
                    return sprintf('%s #%s', $entity->nome, $entity->id);
                },
            ]);
    }

    /**
     * listaAtivas method
     * Retorna query para listagem de filiais ativas
     *
     * @return \Cake\ORM\Query
     */
    public function listaAtivas(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Filial $entity) {
                    return sprintf('%s #%s', $entity->nome, $entity->id);
                },
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Filiais.status', true);

                return $expression;
            });
    }
}
