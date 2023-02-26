<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TabelaPrecosZona Entity
 *
 * @property int $id
 * @property int $tabela_preco_id
 * @property int $zona_id
 *
 * @property \App\Model\Entity\TabelaPreco $tabela_preco
 * @property \App\Model\Entity\Zona $zona
 */
class TabelaPrecosZona extends Entity
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
        'tabela_preco_id' => true,
        'zona_id' => true,
        'tabela_preco' => true,
        'zona' => true,
    ];
}
