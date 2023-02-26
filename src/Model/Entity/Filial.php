<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Filial Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $horario_atendimento
 * @property int $endereco_id
 * @property bool $status
 * @property string|null $observacoes
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Endereco $endereco
 * @property \App\Model\Entity\Pedido[] $pedidos
 */
class Filial extends Entity
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
        'horario_atendimento' => true,
        'endereco_id' => true,
        'status' => true,
        'observacoes' => true,
        'created' => true,
        'modified' => true,
        'endereco' => true,
        'pedidos' => true,
    ];
}
