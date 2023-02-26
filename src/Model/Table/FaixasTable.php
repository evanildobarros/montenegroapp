<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Faixa;
use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Faixas Model
 *
 * @property \App\Model\Table\ZonasTable&\Cake\ORM\Association\BelongsTo $Zonas
 *
 * @method \App\Model\Entity\Faixa newEmptyEntity()
 * @method \App\Model\Entity\Faixa newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Faixa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Faixa get($primaryKey, $options = [])
 * @method \App\Model\Entity\Faixa findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Faixa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Faixa[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Faixa|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Faixa saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Faixa[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Faixa[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Faixa[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Faixa[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FaixasTable extends Table
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

        $this->setTable('faixas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('AuditLog.Auditable');

        $this->belongsTo('Zonas', [
            'foreignKey' => 'zona_id',
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
            ->scalar('inicio')
            ->requirePresence('inicio', 'create')
            ->notEmptyString('inicio');

        $validator
            ->scalar('fim')
            ->requirePresence('fim', 'create')
            ->notEmptyString('fim');

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
        $rules->add($rules->existsIn(['zona_id'], 'Zonas'), [
            'errorField' => 'zona_id',
            'message' => 'Bairro inválido!',
        ]);

        return $rules;
    }

    /**
     * beforeSave method
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \App\Model\Entity\Faixa $faixa The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function beforeSave(EventInterface $event, Faixa $faixa, \ArrayObject $options)
    {
        $faixa->inicio = str_replace(['-', '.'], '', $faixa->inicio);
        $faixa->fim = str_replace(['-', '.'], '', $faixa->fim);

        /** @var \App\Model\Entity\Faixa $faixaBanco */
        $faixaBanco = $this
            ->find()
            ->contain(['Zonas'])
            ->where(function (QueryExpression $expression, Query $query) use ($faixa) {
                $functionBuilder = $query->func();

                $or = $expression->or(function (QueryExpression $orExpression) use ($faixa, $functionBuilder) {
                    return $orExpression
                        ->between(
                            $faixa->inicio,
                            $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                            $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                        )
                        ->between(
                            $faixa->fim,
                            $functionBuilder->cast('Faixas.inicio', 'UNSIGNED'),
                            $functionBuilder->cast('Faixas.fim', 'UNSIGNED')
                        );
                });

                return $expression->add($or);
            })
            ->first();

        if (!empty($faixaBanco)) {
            throw new BadRequestException('Atenção essa faixa já está cadastrada no bairro ' .
                $faixaBanco->zona->nome . ' (Cód. ' . $faixaBanco->zona_id . ')');
        }
    }
}
