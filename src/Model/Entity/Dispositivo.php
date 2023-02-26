<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Dispositivo Entity
 *
 * @property int $id
 * @property int|null $pessoa_id
 * @property string $id_dispositivo
 * @property string|null $firebase_token
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Pessoa $pessoa
 */
class Dispositivo extends Entity
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
        'id_dispositivo' => true,
        'firebase_token' => true,
        'created' => true,
        'modified' => true,
        'pessoa' => true,
    ];
}
