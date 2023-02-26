<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Atualizacao;
use ArrayObject;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Atualizacoes Model
 *
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\BelongsTo $Pedidos
 * @method \App\Model\Entity\Atualizacao newEmptyEntity()
 * @method \App\Model\Entity\Atualizacao newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Atualizacao[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Atualizacao get($primaryKey, $options = [])
 * @method \App\Model\Entity\Atualizacao findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Atualizacao patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Atualizacao[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Atualizacao|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Atualizacao saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Atualizacao[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Atualizacao[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Atualizacao[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Atualizacao[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AtualizacoesTable extends Table
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

        $this->setTable('atualizacoes');
        $this->setDisplayField('titulo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Pedidos', [
            'foreignKey' => 'pedido_id',
            'joinType' => 'INNER',
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
            ->scalar('titulo')
            ->maxLength('titulo', 255)
            ->requirePresence('titulo', 'create', 'Este campo é obrigatório')
            ->notEmptyString('titulo', 'Este campo é obrigatório');

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->dateTime('data', ['dmy'])
            ->requirePresence('data', 'create', 'Este campo é obrigatório')
            ->notEmptyDateTime('data', 'Este campo é obrigatório');

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
        $rules->add($rules->existsIn(['pedido_id'], 'Pedidos'), [
            'errorField' => 'pedido_id',
            'message' => 'Pedido inválido!',
        ]);

        return $rules;
    }

    /**
     * Add method
     *
     * @param array $data Dados para salvar uma nova atualização
     * @return \App\Model\Entity\Atualizacao Return.
     */
    public function add(array $data): Atualizacao
    {
        $atualizacao = $this->newEmptyEntity();
        $atualizacao = $this->patchEntity($atualizacao, $data);

        return $this->saveOrFail($atualizacao);
    }

    /**
     * afterSave callback.
     *
     * @param \Cake\Event\EventInterface $event The afterSave event that was fired.
     * @param \App\Model\Entity\Atualizacao $atualizacao The entity that was saved.
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function afterSave(EventInterface $event, Atualizacao $atualizacao, ArrayObject $options)
    {
        if ($atualizacao->isNew()) {
            $tableLocator = new TableLocator();
            $QueuedJobs = $tableLocator->get('Queue.QueuedJobs');

            $QueuedJobs->createJob('EmailAtualizacaoPedidoCliente', [
                'atualizacao_id' => $atualizacao->id,
            ]);

            $pedido = $this->Pedidos->get($atualizacao->pedido_id);
            $notificacao = $this->Pedidos->Pessoas->Notificacoes->newEntity([
                'pessoa_id' => $pedido->cliente_id,
                'titulo' => 'Alteração do pedido #' . $pedido->id,
                'mensagem' => $atualizacao->titulo,
            ]);
            $this->Pedidos->Pessoas->Notificacoes->saveOrFail($notificacao);
        }
    }
}
