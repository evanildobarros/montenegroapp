<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TabelaPreco Entity
 *
 * @property int $id
 * @property int $entrega_meio_id
 * @property string $nome
 * @property string $modalidade_distribuicao
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\EntregaMeio $entrega_meio
 * @property \App\Model\Entity\Peso[] $pesos
 * @property \App\Model\Entity\Zona[] $zonas
 */
class TabelaPreco extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'entrega_meio_id' => true,
        'nome' => true,
        'modalidade_distribuicao' => true,
        'created' => true,
        'modified' => true,
        'entrega_meio' => true,
        'pesos' => true,
        'zonas' => true,
    ];
}
