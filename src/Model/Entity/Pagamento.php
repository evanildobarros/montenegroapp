<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Table\PagamentosTable;
use Cake\ORM\Entity;

/**
 * Pagamento Entity
 *
 * @property int $id
 * @property int $pedido_id
 * @property string|null $transaction_code
 * @property string|null $comentario
 * @property int $status
 * @property string $status_formatado
 * @property string|null $created
 * @property string|null $modified
 * @property float $valor
 *
 * @property \App\Model\Entity\Pedido $pedido
 */
class Pagamento extends Entity
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
        'pedido_id' => true,
        'transaction_code' => true,
        'comentario' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'valor' => true,
        'pedido' => true,
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'status_formatado',
    ];

    /**
     * @return string|null
     */
    protected function _getStatusFormatado(): string
    {
        $tmp = $this->status;
        $status = PagamentosTable::STATUS_TRANSACAO;

        if (isset($status[$tmp])) {
            return $status[$tmp];
        }

        return '';
    }
}
