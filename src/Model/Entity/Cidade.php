<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cidade Entity
 *
 * @property int $id
 * @property int $estado_id
 * @property string $nome
 * @property string $ibge
 * @property string $latitude
 * @property string $longitude
 * @property int $populacao
 *
 * @property \App\Model\Entity\Estado $estado
 * @property \App\Model\Entity\Filial[] $filiais
 * @property \App\Model\Entity\Objeto[] $objetos
 * @property \App\Model\Entity\Pessoa[] $pessoas
 */
class Cidade extends Entity
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
        'estado_id' => true,
        'nome' => true,
        'ibge' => true,
        'latitude' => true,
        'longitude' => true,
        'populacao' => true,
        'estado' => true,
        'filiais' => true,
        'objetos' => true,
        'pessoas' => true,
    ];
}
