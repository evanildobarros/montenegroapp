<?php
declare(strict_types=1);

namespace App\Model\Table;

use AuditLog\Model\Table\CurrentUserTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TabelaPrecos Model
 *
 * @property \App\Model\Table\EntregaMeiosTable&\Cake\ORM\Association\BelongsTo $EntregaMeios
 * @property \App\Model\Table\PesosTable&\Cake\ORM\Association\HasMany $Pesos
 * @property \App\Model\Table\ZonasTable&\Cake\ORM\Association\BelongsToMany $Zonas
 * @method \App\Model\Entity\TabelaPreco newEmptyEntity()
 * @method \App\Model\Entity\TabelaPreco newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPreco[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPreco get($primaryKey, $options = [])
 * @method \App\Model\Entity\TabelaPreco findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\TabelaPreco patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPreco[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TabelaPreco|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TabelaPreco saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TabelaPreco[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TabelaPreco[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\TabelaPreco[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\TabelaPreco[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TabelaPrecosTable extends Table
{
    use CurrentUserTrait;

    //------------------------------MODALIDADE DE DISTRIBUIÇÃO------------------------------
    /** @var string Modalidade coleta: a empresa designa os entregadores para buscarem os pacotes */
    public const COLETA = PedidosTable::COLETA;

    /** @var string Modalidade entrega: o cliente envia os produtos para o centro de distribuição */
    public const ENTREGA = PedidosTable::ENTREGA;

    /** @var string[] Lista das modalidades de distribuição */
    public const MODALIDADE_DISTRIBUICAO = PedidosTable::MODALIDADE_DISTRIBUICAO;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tabela_precos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('EntregaMeios', [
            'foreignKey' => 'entrega_meio_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Pesos', [
            'foreignKey' => 'tabela_preco_id',
            'saveStrategy' => 'replace',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsToMany('Zonas', [
            'foreignKey' => 'tabela_preco_id',
            'targetForeignKey' => 'zona_id',
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
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('modalidade_distribuicao')
            ->maxLength('modalidade_distribuicao', 255)
            ->requirePresence('modalidade_distribuicao', 'create')
            ->notEmptyString('modalidade_distribuicao');

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
        $rules->add($rules->existsIn(['entrega_meio_id'], 'EntregaMeios'), [
            'errorField' => 'entrega_meio_id',
            'message' => 'Meio de entrega inválido!',
        ]);
        $rules->add(
            $rules->isUnique(['nome'], 'Já existe uma tabela de preços com este nome.'),
            ['errorField' => 'nome'],
        );

        return $rules;
    }
}
