<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Zona Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $nome_abreviado
 * @property int|null $cidade_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Taxa[] $taxas
 * @property \App\Model\Entity\Faixa[] $faixas
 * @property \App\Model\Entity\TabelaPreco[] $tabela_precos
 * @property \App\Model\Entity\Cidade $cidade
 */
class Zona extends Entity
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
        'nome' => true,
        'nome_abreviado' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'taxas' => true,
        'faixas' => true,
        'cidade_id' => true,
        'cidade' => true,
        'tabela_precos' => true,
    ];
}
