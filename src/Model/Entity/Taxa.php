<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Taxa Entity
 *
 * @property int $id
 * @property int $peso_id
 * @property int $zona_id
 * @property float $valor
 * @property int $tempo_estimado
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Peso $peso
 * @property \App\Model\Entity\Zona $zona
 */
class Taxa extends Entity
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
        'peso_id' => true,
        'zona_id' => true,
        'valor' => true,
        'tempo_estimado' => true,
        'created' => true,
        'modified' => true,
        'peso' => true,
        'zona' => true,
    ];
}
