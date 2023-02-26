<?php
declare(strict_types=1);

namespace Correios\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogLocalidade Entity
 *
 * @property int|null $loc_nu
 * @property string|null $ufe_sg
 * @property string|null $loc_no
 * @property string|null $cep
 * @property string|null $loc_in_sit
 * @property string|null $loc_in_tipo_loc
 * @property int|null $loc_nu_sub
 * @property string|null $loc_no_abrev
 * @property string|null $mun_nu
 *
 * @property \Correios\Model\Entity\LogBairro[] $log_bairro
 * @property \Correios\Model\Entity\LogFaixaLocalidade[] $log_faixa_localidade
 */
class LogLocalidade extends Entity
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
        'loc_nu' => true,
        'ufe_sg' => true,
        'loc_no' => true,
        'cep' => true,
        'loc_in_sit' => true,
        'loc_in_tipo_loc' => true,
        'loc_nu_sub' => true,
        'loc_no_abrev' => true,
        'mun_nu' => true,
        'log_bairro' => true,
        'log_faixa_localidade' => true,
    ];
}
