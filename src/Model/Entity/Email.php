<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Email Entity
 *
 * @property string $id
 * @property string $to_email
 * @property string|null $to_name
 * @property string $subject
 * @property string|null $message
 * @property string $metadata
 * @property bool $message_opened
 * @property \Cake\I18n\FrozenTime|null $opening_date
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Email extends Entity
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
        'to_email' => true,
        'to_name' => true,
        'subject' => true,
        'message' => true,
        'metadata' => true,
        'message_opened' => true,
        'opening_date' => true,
        'created' => true,
        'modified' => true,
    ];
}
