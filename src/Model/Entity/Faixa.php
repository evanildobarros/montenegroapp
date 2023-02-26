<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Faixa Entity
 *
 * @property int $id
 * @property int $zona_id
 * @property string $inicio
 * @property string $fim
 *
 * @property \App\Model\Entity\Zona $zona
 */
class Faixa extends Entity
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
        'zona_id' => true,
        'inicio' => true,
        'fim' => true,
        'zona' => true,
    ];
}
