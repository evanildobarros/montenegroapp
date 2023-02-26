<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Table\RotaPedidosTable;
use Cake\ORM\Entity;

/**
 * RotaPedido Entity
 *
 * @property int $id
 * @property int $pedido_id
 * @property int $rota_id
 * @property int|null $ordem
 * @property int|null $parent_id
 * @property bool|null $entregue
 * @property string|null $tipo
 * @property string|null $tipo_formatado
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\RotaPedido $parent_rota_pedido
 * @property \App\Model\Entity\Tentativa[] $tentativas
 * @property \App\Model\Entity\Pedido $pedido
 * @property \App\Model\Entity\Rota $rota
 */
class RotaPedido extends Entity
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
        'pedido_id' => true,
        'rota_id' => true,
        'ordem' => true,
        'tipo' => true,
        'entregue' => true,
        'created' => true,
        'modified' => true,
        'pedido' => true,
        'rota' => true,
        'tentativas' => true,
        'parent_id' => true,
        'parent_rota_pedido' => true,
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'tipo_formatado',
    ];

    /**
     * @return string|null
     */
    protected function _getTipoFormatado(): ?string
    {
        $tmp = $this->tipo;
        $array = RotaPedidosTable::TIPOS;

        if (isset($array[$tmp])) {
            return $array[$tmp];
        }

        return $tmp;
    }
}
