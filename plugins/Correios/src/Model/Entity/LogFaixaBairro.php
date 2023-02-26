<?php
declare(strict_types=1);

namespace Correios\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogFaixaBairro Entity
 *
 * @property int|null $bai_nu
 * @property string|null $fcb_cep_ini
 * @property string|null $fcb_cep_fim
 *
 * @property \Correios\Model\Entity\LogBairro $log_bairro
 */
class LogFaixaBairro extends Entity
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
        'bai_nu' => true,
        'fcb_cep_ini' => true,
        'fcb_cep_fim' => true,
        'log_bairro' => true,
    ];
}
