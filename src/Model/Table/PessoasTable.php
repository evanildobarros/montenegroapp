<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Pessoa;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Localized\Validation\BrValidation;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pessoas Model
 *
 * @property \App\Model\Table\EnderecosTable&\Cake\ORM\Association\BelongsTo $Enderecos
 * @property \App\Model\Table\RotasTable&\Cake\ORM\Association\HasMany $Rotas
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\HasMany $Pedidos
 * @property \App\Model\Table\NotificacoesTable&\Cake\ORM\Association\HasMany $Notificacoes
 * @property \App\Model\Table\DispositivosTable&\Cake\ORM\Association\HasMany $Dispositivos
 * @method \App\Model\Entity\Pessoa newEmptyEntity()
 * @method \App\Model\Entity\Pessoa newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Pessoa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pessoa get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pessoa findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Pessoa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pessoa[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pessoa|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pessoa saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pessoa[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pessoa[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pessoa[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pessoa[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PessoasTable extends Table
{
    use CurrentUserTrait;

    //------------------------------MODEL------------------------------
    /** @var string Pessoa do tipo cliente */
    public const CLIENTE = 'cliente';

    /** @var string Pessoa do tipo entregador */
    public const ENTREGADOR = 'entregador';

    /** @var string[] Lista de todos os possíveis tipos */
    public const MODELS = [
        self::CLIENTE => 'Cliente',
        self::ENTREGADOR => 'Entregador',
    ];

    //------------------------------TIPO------------------------------
    /** @var string Pessoa física */
    public const FISICA = 'pf';

    /** @var string Pessoa física */
    public const JURIDICA = 'pj';

    /** @var string[] Lista de todos os possíveis tipos */
    public const TIPOS = [
        self::FISICA => 'Física',
        self::JURIDICA => 'Jurídica',
    ];

    //------------------------------STATUS------------------------------
    /** @var string Pessoa nova, ainda não validou o email */
    public const AGUARDANDO_VALIDACAO = 'aguardando-validacao-email';

    /** @var string Pessoa ativa */
    public const ATIVO = 'ativo';

    /** @var string Pessoa inativada */
    public const INATIVO = 'inativo';

    /** @var string[] Lista de todos os possíveis status para o cliente */
    public const STATUS_CLIENTE = [
        self::AGUARDANDO_VALIDACAO => 'Aguardando validação email',
        self::ATIVO => 'Ativo',
        self::INATIVO => 'Inativo',
    ];

    // Status para entregador
    /** @var string[] Lista de todos os possíveis status para entregar */
    public const STATUS_ENTREGADOR = [
        self::ATIVO => 'Ativo',
        self::INATIVO => 'Inativo',
    ];

    // Todos os status
    /** @var string[] Lista de todos os possíveis status */
    public const STATUS = [
        self::AGUARDANDO_VALIDACAO => 'Aguardando validação email',
        self::ATIVO => 'Ativo',
        self::INATIVO => 'Inativo',
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

        $this->setTable('pessoas');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->hasMany('Pedidos', [
            'foreignKey' => 'cliente_id',
        ]);
        $this->hasMany('Notificacoes', [
            'foreignKey' => 'pessoa_id',
        ]);
        $this->hasMany('Dispositivos', [
            'foreignKey' => 'pessoa_id',
        ]);
        $this->hasMany('Rotas', [
            'foreignKey' => 'entregador_id',
        ]);
        $this->belongsTo('Enderecos', [
            'foreignKey' => 'endereco_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Override this function in order to alter the schema used by this table.
     * This function is only called after fetching the schema out of the database.
     * If you wish to provide your own schema to this table without touching the
     * database, you can override schema() or inject the definitions though that
     * method.
     *
     * @param \Cake\Database\Schema\TableSchemaInterface $schema The table definition fetched from database.
     * @return \Cake\Database\Schema\TableSchemaInterface the altered schema
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('cpf', 'cpf');
        $schema->setColumnType('cnpj', 'cnpj');
        $schema->setColumnType('cep', 'cep');

        return $schema;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('br', BrValidation::class);

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('model')
            ->inList('model', array_keys(self::MODELS), 'Status inválido!')
            ->requirePresence('model', 'create', 'Este campo é obrigatório')
            ->notEmptyString('model');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create', 'Este campo é obrigatório')
            ->notEmptyString('nome', 'Este campo é obrigatório');

        $validator
            ->email('email')
            ->notEmptyString('email', 'Este campo é obrigatório')
            ->requirePresence('email', 'create', 'Este campo é obrigatório')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Este email já está em uso',
            ]);

        $validator
            ->scalar('senha')
            ->maxLength('senha', 255)
            ->requirePresence('senha', 'create', 'Este campo é obrigatório')
            ->notEmptyString('senha', 'Este campo é obrigatório', 'create');

        $validator
            ->requirePresence('senha_confirm', 'create', 'Este campo é obrigatório')
            ->notEmptyString('senha_confirm', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('token')
            ->maxLength('token', 255)
            ->allowEmptyString('token');

        $validator
            ->scalar('token_ativacao')
            ->maxLength('token_ativacao', 255)
            ->allowEmptyString('token_ativacao');

        $validator
            ->date('data_nascimento', ['dmy'])
            ->allowEmptyDate('data_nascimento');

        $validator
            ->scalar('cpf')
            ->add('cpf', 'cpf', ['rule' => 'personId', 'provider' => 'br', 'message' => 'CPF inválido!'])
            ->allowEmptyString('cpf');

        $validator
            ->scalar('cnpj')
            ->add('cnpj', 'cnpj', ['rule' => 'personId', 'provider' => 'br', 'message' => 'CNPJ inválido!'])
            ->allowEmptyString('cnpj');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 16)
            ->allowEmptyString('telefone');

        $validator
            ->scalar('celular')
            ->maxLength('celular', 16)
            ->requirePresence('celular', 'create', 'Este campo é obrigatório')
            ->notEmptyString('celular', 'Este campo é obrigatório', 'create');

        $validator
            ->scalar('cep')
            ->maxLength('cep', 10)
            ->allowEmptyString('cep');

        $validator
            ->scalar('logradouro')
            ->maxLength('logradouro', 255)
            ->allowEmptyString('logradouro');

        $validator
            ->scalar('numero')
            ->maxLength('numero', 255)
            ->allowEmptyString('numero');

        $validator
            ->scalar('bairro')
            ->maxLength('bairro', 255)
            ->allowEmptyString('bairro');

        $validator
            ->scalar('complemento')
            ->maxLength('complemento', 255)
            ->allowEmptyString('complemento');

        $validator
            ->scalar('referencia')
            ->maxLength('referencia', 255)
            ->allowEmptyString('referencia');

        $validator
            ->scalar('nome_representante')
            ->maxLength('nome_representante', 255)
            ->allowEmptyString('nome_representante');

        $validator
            ->scalar('celular_representante')
            ->maxLength('celular_representante', 255)
            ->allowEmptyString('celular_representante');

        $validator
            ->scalar('email_representante')
            ->maxLength('email_representante', 255)
            ->allowEmptyString('email_representante');

        $validator
            ->scalar('status')
            ->inList('status', array_keys(self::STATUS), 'Status inválido!')
            ->requirePresence('status', 'create', 'Este campo é obrigatório')
            ->notEmptyString('status', 'Este campo é obrigatório');

        $validator
            ->integer('quantidade_entregas')
            ->allowEmptyString('quantidade_entregas');

        $validator
            ->decimal('valor_fixo_pedidos')
            ->allowEmptyString('valor_fixo_pedidos');

        $validator
            ->scalar('tipo')
            ->inList('tipo', array_keys(self::TIPOS), 'Tipo inválido!')
            ->notEmptyString('tipo', 'Este campo é obrigatório', 'create');

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
        $rules->add($rules->isUnique(['email']), [
            'errorField' => 'email',
            'message' => 'Este email já está em uso!',
        ]);

        // Obrigar o usuário a adicionar nome e sobrenome
        $rules->add(function (Pessoa $pessoa, $options) {
            if ($pessoa->isNew() || (!$pessoa->isNew() && $pessoa->isDirty('nome'))) {
                $dados = explode(' ', $pessoa->nome);

                if (!isset($dados[1]) || empty($dados[1])) {
                    return false;
                }

                return true;
            }

            return true;
        }, 'valid_name', [
            'errorField' => 'nome',
            'message' => 'Atenção informe o nome e sobrenome!',
        ]);

        // Obrigar o usuário a adicionar um documento quando for cliente por causa do pagseguro
        $rules->add(function (Pessoa $pessoa, $options) {
            if ($pessoa->model === self::CLIENTE) {
                if (
                    $pessoa->isNew() ||
                    (!$pessoa->isNew() && ($pessoa->isDirty('cpf') || $pessoa->isDirty('cnpj')))
                ) {
                    if (empty($pessoa->cpf) && empty($pessoa->cnpj)) {
                        return false;
                    }

                    return true;
                }
            }

            return true;
        }, 'valid_document', [
            'errorField' => 'tipo',
            'message' => 'Atenção informe um CPF ou um CNPJ',
        ]);

        $rules->add(function (EntityInterface $entity, $options) {
            if ($entity->isNew() || (!$entity->isNew() && $entity->isDirty('senha'))) {
                return $entity->senha == $entity->senha_confirm;
            }

            return true;
        }, 'valid_password', [
            'errorField' => 'senha',
            'message' => 'As senhas não conferem!',
        ]);

        return $rules;
    }

    /**
     * beforeSave method
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (!empty($entity->senha) && $entity->isDirty('senha')) {
            $hasher = new DefaultPasswordHasher();
            $entity->senha = $hasher->hash($entity->senha);
        } else {
            $entity->unset('senha');
        }
    }

    /**
     * listaClientes method
     * Retorna query para listagem de entregadores
     *
     * @return \Cake\ORM\Query
     */
    public function listaClientes(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Pessoa $entity) {
                    return sprintf('%s #%s', $entity->nome, $entity->id);
                },
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Pessoas.model', PessoasTable::CLIENTE);

                return $expression;
            });
    }

    /**
     * listaEntregadores method
     * Retorna query para listagem de entregadores
     *
     * @return \Cake\ORM\Query
     */
    public function listaEntregadores(): Query
    {
        return $this
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Pessoa $entity) {
                    return sprintf('%s #%s', $entity->nome, $entity->id);
                },
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Pessoas.model', PessoasTable::ENTREGADOR);

                return $expression;
            });
    }

    /**
     * listaEntregadores method
     * Retorna query para listagem de entregadores
     *
     * @param int $entregador_id Pessoa id
     * @param \Cake\I18n\Date|string $data_saida Data saida da rota
     * @return int Return
     */
    public function quantidadeParadas($entregador_id, $data_saida): int
    {
        return $this->Rotas->RotaPedidos
            ->find()
            ->contain('Rotas')
            ->where(function (QueryExpression $expression) use ($entregador_id, $data_saida) {
                $expression
                    ->eq('Rotas.entregador_id', $entregador_id)
                    ->eq('Rotas.data_saida', $data_saida);

                return $expression;
            })
            ->count();
    }

    /**
     * FindAtivo method
     * Retorna apenas pessoas(clientes/entregadores) ativos
     *
     * @param \Cake\ORM\Query $query Query
     * @return \Cake\ORM\Query
     */
    public function findAtivo(Query $query)
    {
        return $query->where(function (QueryExpression $expression) {
            return $expression->eq('Pessoas.status', self::ATIVO);
        });
    }
}
