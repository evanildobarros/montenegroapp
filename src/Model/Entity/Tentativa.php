<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tentativa Entity
 *
 * @property int $id
 * @property int $rota_pedido_id
 * @property int $motivo_id
 * @property string $nome_motivo
 * @property string $observacoes
 * @property \Cake\I18n\FrozenTime|null $data
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\RotaPedido $rota_pedido
 * @property \App\Model\Entity\Motivo $motivo
 */
class Tentativa extends Entity
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
        'rota_pedido_id' => true,
        'motivo_id' => true,
        'nome_motivo' => true,
        'observacoes' => true,
        'data' => true,
        'created' => true,
        'modified' => true,
        'rota_pedido' => true,
        'motivo' => true,
    ];
}
