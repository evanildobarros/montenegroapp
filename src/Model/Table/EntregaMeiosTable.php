<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\EntregaMeio;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EntregaMeios Model
 *
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\HasMany $Pedidos
 * @property \App\Model\Table\TabelaPrecosTable&\Cake\ORM\Association\HasMany $TabelaPrecos
 * @method \App\Model\Entity\EntregaMeio newEmptyEntity()
 * @method \App\Model\Entity\EntregaMeio newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\EntregaMeio[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EntregaMeio get($primaryKey, $options = [])
 * @method \App\Model\Entity\EntregaMeio findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\EntregaMeio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EntregaMeio[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\EntregaMeio|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EntregaMeio saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EntregaMeio[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\EntregaMeio[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\EntregaMeio[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\EntregaMeio[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EntregaMeiosTable extends Table
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

        $this->setTable('entrega_meios');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->hasMany('Pedidos', [
            'foreignKey' => 'entrega_meio_id',
        ]);
        $this->hasMany('TabelaPrecos', [
            'foreignKey' => 'entrega_meio_id',
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
            ->scalar('icone')
            ->maxLength('icone', 255)
            ->allowEmptyString('icone');

        $validator
            ->integer('altura_maxima')
            ->allowEmptyString('altura_maxima', 'Este campo é obrigatório');

        $validator
            ->integer('profundidade_maxima')
            ->allowEmptyString('profundidade_maxima', 'Este campo é obrigatório');

        $validator
            ->integer('largura_maxima')
            ->allowEmptyString('largura_maxima', 'Este campo é obrigatório');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create', 'Este campo é obrigatório')
            ->notEmptyString('status', 'Este campo é obrigatório', 'create');

        return $validator;
    }

    /**
     * listaAll method
     * Retorna query para listagem
     *
     * @return \Cake\ORM\Query
     */
    public function listaAll(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (EntregaMeio $entity) {
                    return sprintf('%s #%s', $entity->nome, $entity->id);
                },
            ]);
    }

    /**
     * listaAtivas method
     * Retorna query para listagem de ativas
     *
     * @return \Cake\ORM\Query
     */
    public function listaAtivas(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (EntregaMeio $entity) {
                    return sprintf('%s #%s', $entity->nome, $entity->id);
                },
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('EntregaMeios.status', true);

                return $expression;
            });
    }
}
