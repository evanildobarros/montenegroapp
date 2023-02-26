<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Endereco Entity
 *
 * @property int $id
 * @property int $cidade_id
 * @property string $cep
 * @property string $logradouro
 * @property string $numero
 * @property string $bairro
 * @property string|null $complemento
 * @property string|null $referencia
 * @property string|null $endereco_formatado
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Cidade $cidade
 * @property \App\Model\Entity\Objeto[] $objeto_entregas
 * @property \App\Model\Entity\Objeto[] $objeto_coletas
 */
class Endereco extends Entity
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
        'cidade_id' => true,
        'cep' => true,
        'logradouro' => true,
        'numero' => true,
        'bairro' => true,
        'complemento' => true,
        'referencia' => true,
        'created' => true,
        'modified' => true,
        'cidade' => true,
        'objeto_entregas' => true,
        'objeto_coletas' => true,
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'endereco_formatado',
    ];

    /**
     * @return string|null
     */
    protected function _getEnderecoFormatado(): ?string
    {
        $tmp = "{$this->logradouro}, {$this->numero} - {$this->bairro}";

        if (!empty($this->complemento)) {
            $tmp .= ", {$this->complemento}";
        }
        if (!empty($this->referencia)) {
            $tmp .= ", {$this->referencia}";
        }
        if (!empty($this->cidade)) {
            $tmp .= ", {$this->cidade->nome}";

            if (!empty($this->cidade->estado)) {
                $tmp .= "/{$this->cidade->estado->sigla}";
            }
        }
        $tmp .= ", {$this->cep}";

        return $tmp;
    }
}
