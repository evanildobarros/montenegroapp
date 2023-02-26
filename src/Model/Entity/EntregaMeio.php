<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EntregaMeio Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $icone
 * @property int $altura_maxima
 * @property int $profundidade_maxima
 * @property int $largura_maxima
 * @property bool $status
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Pedido[] $pedidos
 * @property \App\Model\Entity\TabelaPreco[] $tabela_precos
 */
class EntregaMeio extends Entity
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
        'icone' => true,
        'altura_maxima' => true,
        'profundidade_maxima' => true,
        'largura_maxima' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'pedidos' => true,
        'tabela_precos' => true,
    ];
}
