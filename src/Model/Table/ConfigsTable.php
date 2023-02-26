<?php
declare(strict_types=1);

namespace App\Model\Table;

use AuditLog\Model\Table\CurrentUserTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Configs Model
 *
 * @method \App\Model\Entity\Config newEmptyEntity()
 * @method \App\Model\Entity\Config newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Config[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Config get($primaryKey, $options = [])
 * @method \App\Model\Entity\Config findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Config patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Config[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Config|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Config saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ConfigsTable extends Table
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

        $this->setTable('configs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog.Auditable');
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
            ->scalar('parametro')
            ->maxLength('parametro', 255)
            ->allowEmptyString('parametro');

        $validator
            ->scalar('valor')
            ->allowEmptyString('valor');

        return $validator;
    }

    /**
     * @return array
     */
    public function parametros()
    {
        $configs = $this
            ->find('list', [
                'keyField' => 'parametro',
                'valueField' => 'valor',
            ])
            ->toArray();

        return $configs;
    }

    /**
     * @param string $parametro dados dos parametros
     * @param null $default dados default
     * @return mixed
     */
    public function parametro($parametro, $default = null)
    {
        /** @var \App\Model\Entity\Config $parametros */
        $parametros = $this
            ->find()
            ->where(function (QueryExpression $expression) use ($parametro) {
                $expression->eq('Configs.parametro', $parametro);

                return $expression;
            })
            ->first();

        if (empty($parametros)) {
            return $default;
        }

        return $parametros->valor;
    }

    /**
     * @return array Array de classificação pequeno
     */
    public function classificacaoPequeno(): array
    {
        $altura = $this->parametro('pequeno_altura_maxima');
        $largura = $this->parametro('pequeno_largura_maxima');
        $profundidade = $this->parametro('pequeno_profundidade_maxima');

        $tmp = [
            'altura_maxima' => $altura,
            'largura_maxima' => $largura,
            'profundidade_maxima' => $profundidade,
            'vazio' => (empty($altura) || empty($largura) || empty($profundidade)),
        ];

        return $tmp;
    }

    /**
     * @return array Array de classificação médio
     */
    public function classificacaoMedio(): array
    {
        $altura = $this->parametro('medio_altura_maxima');
        $largura = $this->parametro('medio_largura_maxima');
        $profundidade = $this->parametro('medio_profundidade_maxima');

        $tmp = [
            'altura_maxima' => $altura,
            'largura_maxima' => $largura,
            'profundidade_maxima' => $profundidade,
            'vazio' => (empty($altura) || empty($largura) || empty($profundidade)),
        ];

        return $tmp;
    }

    /**
     * @return array Array de classificação grande
     */
    public function classificacaoGrande(): array
    {
        $altura = $this->parametro('grande_altura_maxima');
        $largura = $this->parametro('grande_largura_maxima');
        $profundidade = $this->parametro('grande_profundidade_maxima');

        $tmp = [
            'altura_maxima' => $altura,
            'largura_maxima' => $largura,
            'profundidade_maxima' => $profundidade,
            'vazio' => (empty($altura) || empty($largura) || empty($profundidade)),
        ];

        return $tmp;
    }

    /**
     * @param string $altura Altura
     * @param string $largura Largura
     * @param string $profundidade Profundidade
     * @param string $unidade_medida Unidade de medida
     * @return string Retorna classificação do objeto conforme as informações
     */
    public function classificacao(
        string $altura,
        string $largura,
        string $profundidade,
        string $unidade_medida = ObjetosTable::CENTIMENTRO
    ): string {
        $pequeno = $this->classificacaoPequeno();
        $medio = $this->classificacaoMedio();
        $grande = $this->classificacaoGrande();

        if ($unidade_medida === ObjetosTable::METRO) {
            $altura = $altura * 100;
            $largura = $largura * 100;
            $profundidade = $profundidade * 100;
        }

        if (
            $pequeno['vazio'] || (
                $altura <= $pequeno['altura_maxima'] &&
                $largura <= $pequeno['largura_maxima'] &&
                $profundidade <= $pequeno['profundidade_maxima']
            )
        ) {
            return ObjetosTable::PEQUENO;
        }

        if (
            $medio['vazio'] || (
                $altura <= $medio['altura_maxima'] &&
                $largura <= $medio['largura_maxima'] &&
                $profundidade <= $medio['profundidade_maxima']
            )
        ) {
            return ObjetosTable::MEDIO;
        }

        if (
            $grande['vazio'] || (
                $altura <= $grande['altura_maxima'] &&
                $largura <= $grande['largura_maxima'] &&
                $profundidade <= $grande['profundidade_maxima']
            )
        ) {
            return ObjetosTable::GRANDE;
        }

        return '';
    }
}
