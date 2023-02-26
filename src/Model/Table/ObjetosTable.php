<?php

declare(strict_types=1);

namespace App\Model\Table;

use AuditLog\Model\Table\CurrentUserTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Objetos Model
 *
 * @property \App\Model\Table\EnderecosTable&\Cake\ORM\Association\BelongsTo $EnderecoEntregas
 * @property \App\Model\Table\EnderecosTable&\Cake\ORM\Association\BelongsTo $EnderecoColetas
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\HasMany $Pedidos
 * @method \App\Model\Entity\Objeto newEmptyEntity()
 * @method \App\Model\Entity\Objeto newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Objeto[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Objeto get($primaryKey, $options = [])
 * @method \App\Model\Entity\Objeto findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Objeto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Objeto[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Objeto|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Objeto saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Objeto[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Objeto[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Objeto[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Objeto[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ObjetosTable extends Table
{
    use CurrentUserTrait;

    //------------------------------CLASSIFICAÇÕES------------------------------
    /** @var string Classificação do objeto em Pequeno */
    public const PEQUENO = 'pequeno';

    /** @var string Classificação do objeto em Médio */
    public const MEDIO = 'medio';

    /** @var string Classificação do objeto em grande */
    public const GRANDE = 'grande';

    /** @var string[] Lista das modalidades de distribuição */
    public const CLASSIFICACAO = [
        self::PEQUENO => 'Pequeno',
        self::MEDIO => 'Médio',
        self::GRANDE => 'Grande',
    ];

    //---------------------UNIDADE DE MEDIDA COMPRIMENTO (ALTURA, LARGURA, PROFUNDIDADE)---------------------
    /** @var string Unidade de medida de comprimento Centímetro */
    public const CENTIMENTRO = 'cm';

    /** @var string Unidade de medida de comprimento Metro */
    public const METRO = 'm';

    /** @var string[] Unidade de medida de comprimento para altura, largura e profundidade */
    public const UNIDADE_MEDIDA_COMPRIMENTO = [
        self::CENTIMENTRO => 'Centímetros (cm)',
        self::METRO => 'Metros (m)',
    ];

    //------------------------------UNIDADE DE MEDIDA PESO------------------------------
    /** @var string Classificação do objeto em Pequeno */
    public const QUILO = 'kg';

    /** @var string Classificação do objeto em Médio */
    public const GRAMA = 'g';

    /** @var string[] Lista das modalidades de distribuição */
    public const UNIDADE_MEDIDA_PESO = [
        self::GRAMA => 'Gramas (g)',
        self::QUILO => 'Quilograma (Kg)',
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('objetos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('EnderecoEntregas', [
            'className' => 'Enderecos',
            'foreignKey' => 'endereco_entrega_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('EnderecoColetas', [
            'className' => 'Enderecos',
            'foreignKey' => 'endereco_coleta_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('Pedidos', [
            'foreignKey' => 'objeto_id',
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
            ->decimal('altura')
            ->requirePresence('altura', 'create')
            ->notEmptyString('altura');

        $validator
            ->scalar('unidade_medida_comprimento')
            ->maxLength('unidade_medida_comprimento', 2)
            ->requirePresence('unidade_medida_comprimento', 'create', 'Este campo é obrigatório')
            ->notEmptyString('unidade_medida_comprimento', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('unidade_medida_peso')
            ->maxLength('unidade_medida_peso', 2)
            ->requirePresence('unidade_medida_peso', 'create', 'Este campo é obrigatório')
            ->notEmptyString('unidade_medida_peso', 'Este campo é obrigatório', 'create');

        $validator
            ->decimal('peso')
            ->requirePresence('peso', 'create')
            ->notEmptyString('peso');

        $validator
            ->decimal('largura')
            ->requirePresence('largura', 'create')
            ->notEmptyString('largura');

        $validator
            ->decimal('profundidade')
            ->requirePresence('profundidade', 'create')
            ->notEmptyString('profundidade');

        $validator
            ->scalar('classificacao')
            ->inList('classificacao', array_keys(self::CLASSIFICACAO), 'Classificação inválida!')
            ->notEmptyString('classificacao', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('nome_destinatario')
            ->maxLength('nome_destinatario', 255)
            ->requirePresence('nome_destinatario', 'create')
            ->notEmptyString('nome_destinatario');

        $validator
            ->scalar('telefone_destinatario')
            ->maxLength('telefone_destinatario', 65)
            ->allowEmptyString('telefone_destinatario');
        $validator
            ->scalar('celular_destinatario')
            ->maxLength('celular_destinatario', 65)
            ->requirePresence('celular_destinatario', 'create')
            ->notEmptyString('celular_destinatario');

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
        $rules->add($rules->existsIn(['endereco_entrega_id'], 'EnderecoEntregas'), [
            'errorField' => 'endereco_entrega_id',
            'message' => 'Endereço de entrega inválido',
        ]);
        $rules->add($rules->existsIn(['endereco_coleta_id'], 'EnderecoColetas'), [
            'errorField' => 'endereco_coleta_id',
            'message' => 'Endereço de coleta inválido',
        ]);

        return $rules;
    }
}
