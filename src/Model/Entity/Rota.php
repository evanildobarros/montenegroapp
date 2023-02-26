<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Table\RotasTable;
use Cake\ORM\Entity;

/**
 * Rota Entity
 *
 * @property int $id
 * @property int $entregador_id
 * @property string|null $status
 * @property string|null $status_formatado
 * @property \Cake\I18n\FrozenDate $data_saida
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Pessoa $pessoa
 * @property \App\Model\Entity\RotaPedido[] $rota_pedidos
 */
class Rota extends Entity
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
        'entregador_id' => true,
        'data_saida' => true,
        'created' => true,
        'modified' => true,
        'pessoa' => true,
        'status' => true,
        'rota_pedidos' => true,
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'status_formatado',
    ];

    /**
     * @return string|null
     */
    protected function _getStatusFormatado(): ?string
    {
        $tmp = $this->status;
        $status = RotasTable::STATUS;

        if (isset($status[$tmp])) {
            return $status[$tmp];
        }

        return $tmp;
    }
}
