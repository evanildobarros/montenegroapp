<?php
declare(strict_types=1);

namespace Correios\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogBairro Entity
 *
 * @property int|null $bai_nu
 * @property string|null $ufe_sg
 * @property int|null $loc_nu
 * @property string|null $bai_no
 * @property string|null $bai_no_abrev
 *
 * @property \Correios\Model\Entity\LogLocalidade $log_localidade
 * @property \Correios\Model\Entity\LogFaixaBairro[] $log_faixa_bairro
 */
class LogBairro extends Entity
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
        'ufe_sg' => true,
        'loc_nu' => true,
        'bai_no' => true,
        'bai_no_abrev' => true,
        'log_localidade' => true,
        'log_faixa_bairro' => true,
    ];
}
