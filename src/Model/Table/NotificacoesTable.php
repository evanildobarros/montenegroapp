<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Adapter\Fcm;
use App\Model\Entity\Notificacao;
use App\Push;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Cake\Log\LogTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Psr\Log\LogLevel;

/**
 * Notificacoes Model
 *
 * @property \App\Model\Table\PessoasTable&\Cake\ORM\Association\BelongsTo $Pessoas
 * @method \App\Model\Entity\Notificacao newEmptyEntity()
 * @method \App\Model\Entity\Notificacao newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Notificacao[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Notificacao get($primaryKey, $options = [])
 * @method \App\Model\Entity\Notificacao findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Notificacao patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Notificacao[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Notificacao|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notificacao saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notificacao[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Notificacao[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Notificacao[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Notificacao[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotificacoesTable extends Table
{
    use LogTrait;
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

        $this->setTable('notificacoes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Pessoas', [
            'foreignKey' => 'pessoa_id',
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
            ->requirePresence('titulo', 'create')
            ->notEmptyString('titulo');

        $validator
            ->scalar('mensagem')
            ->requirePresence('mensagem', 'create')
            ->notEmptyString('mensagem');

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
        $rules->add($rules->existsIn(['pessoa_id'], 'Pessoas'), ['errorField' => 'pessoa_id']);

        return $rules;
    }

    /**
     * afterSave callback.
     *
     * @param \Cake\Event\EventInterface $event The afterSave event that was fired.
     * @param \App\Model\Entity\Notificacao $notificacao The entity that was saved.
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function afterSave(EventInterface $event, Notificacao $notificacao, \ArrayObject $options): void
    {
        if ($notificacao->isNew()) {
            /** @var \App\Model\Entity\Dispositivo[] $dispositivos */

            if (!empty($notificacao->pessoa_id)) {
                $dispositivos = $this->Pessoas->Dispositivos
                    ->find()
                    ->where(function (QueryExpression $expression) use ($notificacao) {
                        $expression
                            ->eq('Dispositivos.pessoa_id', $notificacao->pessoa_id);

                        return $expression;
                    })
                    ->toArray();
            } else {
                $dispositivos = [];
            }

            foreach ($dispositivos as $dispositivo) {
                try {
                    $adapter = new Fcm();
                    $adapter
                        ->setTokens([$dispositivo->firebase_token])
                        ->setNotification([
                            'title' => $notificacao->titulo,
                            'body' => $notificacao->mensagem,
                        ]);

                    $push = new Push($adapter);

                    // Make the push
                    $push->send();

                    // Get the response
                    $push->response();
                } catch (\Exception $e) {
                    $this->log(
                        "Erro ao disparar Notificação #{$notificacao->id} para o Dispositivo " .
                        "#{$dispositivo->id}: {$e->getMessage()}",
                        LogLevel::ERROR,
                        ['scope' => ['notifications']],
                    );
                }
            }
        }
    }
}
