<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Notificacao Entity
 *
 * @property int $id
 * @property int|null $pessoa_id
 * @property string $titulo
 * @property string $mensagem
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Remetente $remetente
 * @property \App\Model\Entity\Destinatario $destinatario
 */
class Notificacao extends Entity
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
        'pessoa_id' => true,
        'titulo' => true,
        'mensagem' => true,
        'created' => true,
        'modified' => true,
        'remetente' => true,
        'destinatario' => true,
    ];
}
