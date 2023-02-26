<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Pagamento;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pagamentos Model
 *
 * @property \App\Model\Table\PedidosTable&\Cake\ORM\Association\BelongsTo $Pedidos
 * @method \App\Model\Entity\Pagamento newEmptyEntity()
 * @method \App\Model\Entity\Pagamento newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Pagamento[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pagamento get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pagamento findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Pagamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pagamento[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pagamento|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pagamento saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pagamento[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pagamento[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pagamento[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pagamento[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PagamentosTable extends Table
{
    /**
     * @var int O comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.
     */
    public const AGUARDANDO_PAGAMENTO = 1;

    /**
     * @var int O comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.
     */
    public const EM_ANALISE = 2;

    /**
     * @var int A transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.
     */
    public const PAGA = 3;

    /**
     * @var int A transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.
     */
    public const DISPONIVEL = 4;

    /**
     * @var int O comprador, dentro do prazo de liberação da transação, abriu uma disputa.
     */
    public const EM_DISPUTA = 5;

    /**
     * @var int O valor da transação foi devolvido para o comprador.
     */
    public const DEVOLVIDA = 6;

    /**
     * @var int A transação foi cancelada sem ter sido finalizada.
     */
    public const CANCELADA = 7;

    /**
     * @var string[] Lista de status de transações
     */
    public const STATUS_TRANSACAO = [
        self::AGUARDANDO_PAGAMENTO => 'Aguardando pagamento',
        self::EM_ANALISE => 'Em análise',
        self::PAGA => 'Pago',
        self::DISPONIVEL => 'Disponível',
        self::EM_DISPUTA => 'Em disputa',
        self::DEVOLVIDA => 'Devolvido',
        self::CANCELADA => 'Cancelado',
    ];
    /**
     * @var string[] Lista de status que o user pode escolher
     */
    public const STATUS_TRANSACAO_USER = [
        self::AGUARDANDO_PAGAMENTO => 'Aguardando pagamento',
        self::EM_ANALISE => 'Em análise',
        self::PAGA => 'Pago',
        self::CANCELADA => 'Cancelado',
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

        $this->setTable('pagamentos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->scalar('transaction_code')
            ->maxLength('transaction_code', 255)
            ->allowEmptyString('transaction_code');

        $validator
            ->scalar('comentario')
            ->allowEmptyString('comentario');

        $validator
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->decimal('valor')
            ->requirePresence('valor', 'create')
            ->notEmptyString('valor');

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
        $rules->add($rules->existsIn(['pedido_id'], 'Pedidos'), ['errorField' => 'pedido_id']);

        return $rules;
    }

    /**
     * afterSave callback.
     *
     * @param \Cake\Event\EventInterface $event The afterSave event that was fired.
     * @param \App\Model\Entity\Pagamento $pagamento The entity that was saved.
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function afterSave(EventInterface $event, Pagamento $pagamento, \ArrayObject $options): void
    {
        if (
            $pagamento->isDirty('status') &&
            !in_array($pagamento->status, [self::AGUARDANDO_PAGAMENTO, self::EM_ANALISE])
        ) {
            $pedido = $this->Pedidos->get($pagamento->pedido_id);

            switch ($pagamento->status) {
                case self::PAGA:
                    $pedido->status = PedidosTable::CONFIRMADO;
                    $this->Pedidos->saveOrFail($pedido);

                    switch ($pedido->modalidade_distribuicao) {
                        case PedidosTable::COLETA:
                            $pedido->status = PedidosTable::PROCESSO_COLETA;
                            break;
                        case PedidosTable::ENTREGA:
                            $pedido->status = PedidosTable::PROCESSO_ENTREGA;
                            break;
                        default:
                            break;
                    }

                    break;
                case self::EM_DISPUTA:
                case self::DEVOLVIDA:
                case self::CANCELADA:
                    $pedido->status = PedidosTable::CANCELADO;
                    break;
            }

            if ($pedido->isDirty()) {
                $this->Pedidos->saveOrFail($pedido);
            }

            $tableLocator = new TableLocator();
            $queue = $tableLocator->get('Queue.QueuedJobs');

            $queue->createJob('EmailAtualizacaoPagamentoCliente', [
                'pagamento_id' => $pagamento->id,
            ]);

            $notificacao = $this->Pedidos->Pessoas->Notificacoes->newEntity([
                'pessoa_id' => $pedido->cliente_id,
                'titulo' => 'Alteração do pedido #' . $pedido->id,
                'mensagem' => 'O novo status do seu pedido é: ' . $pedido->status_formatado,
            ]);
            $this->Pedidos->Pessoas->Notificacoes->saveOrFail($notificacao);
        }
    }
}
