<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Objeto Entity
 *
 * @property int $id
 * @property string $altura
 * @property string $peso
 * @property string $largura
 * @property string $profundidade
 * @property string|null $classificacao
 * @property string $nome_destinatario
 * @property string $telefone_destinatario 
 * @property string $celular_destinatario
 * @property string|null $observacoes
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $unidade_medida_comprimento
 * @property string|null $unidade_medida_peso
 * @property int|null $endereco_entrega_id
 * @property int|null $endereco_coleta_id
 *
 * @property \App\Model\Entity\Endereco $endereco_entrega
 * @property \App\Model\Entity\Endereco $endereco_coleta
 * @property \App\Model\Entity\Pedido[] $pedidos
 */
class Objeto extends Entity
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
        'altura' => true,
        'peso' => true,
        'largura' => true,
        'profundidade' => true,
        'classificacao' => true,
        'nome_destinatario' => true,
        'telefone_destinatario' => true,
        'celular_destinatario' => true,
        'observacoes' => true,
        'created' => true,
        'modified' => true,
        'unidade_medida_comprimento' => true,
        'unidade_medida_peso' => true,
        'endereco_entrega_id' => true,
        'endereco_coleta_id' => true,
        'endereco_entrega' => true,
        'endereco_coleta' => true,
        'pedidos' => true,
    ];
}
