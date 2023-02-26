<?php
declare(strict_types=1);

namespace Correios\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogFaixaLocalidade Entity
 *
 * @property int|null $loc_nu
 * @property string|null $loc_cep_ini
 * @property string|null $loc_cep_fim
 *
 * @property \Correios\Model\Entity\LogLocalidade $log_localidade
 */
class LogFaixaLocalidade extends Entity
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
        'loc_cep_ini' => true,
        'loc_cep_fim' => true,
        'log_localidade' => true,
    ];
}
