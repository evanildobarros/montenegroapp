<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Peso Entity
 *
 * @property int $id
 * @property int $tabela_preco_id
 * @property int|null $peso_minimo
 * @property int|null $peso_maximo
 * @property bool $quilo_adicional
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\TabelaPreco $tabela_preco
 * @property \App\Model\Entity\Taxa[] $taxas
 */
class Peso extends Entity
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
        'peso_minimo' => true,
        'peso_maximo' => true,
        'quilo_adicional' => true,
        'created' => true,
        'modified' => true,
        'tabela_preco' => true,
        'taxas' => true,
    ];
}
