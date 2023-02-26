<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Table\PedidosTable;
use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Pedido Entity
 *
 * @property int $id
 * @property int $cliente_id
 * @property int $objeto_id
 * @property int|null $filial_id
 * @property int $entrega_modalidade_id
 * @property int $entrega_meio_id
 * @property int $coleta_meio_id
 * @property string|null $instrucoes
 * @property string|null $observacoes_tratativa_coleta
 * @property string|null $observacoes_tratativa_entrega
 * @property string|null $etapa
 * @property string $modalidade_entrega
 * @property string $modalidade_distribuicao
 * @property string $meio_entrega
 * @property string $meio_coleta
 * @property float $valor_total
 * @property string $status
 * @property string $status_formatado
 * @property \Cake\I18n\FrozenDate|null $prazo_envio
 * @property \Cake\I18n\FrozenDate|null $previsao_entrega
 * @property \Cake\I18n\FrozenDate|null $previsao_coleta
 * @property \Cake\I18n\FrozenTime|null $data_chegada
 * @property \Cake\I18n\FrozenTime|null $data_postagem
 * @property \Cake\I18n\FrozenTime|null $data_entrega
 * @property \Cake\I18n\FrozenTime|null $data_tratativa_coleta
 * @property \Cake\I18n\FrozenTime|null $data_tratativa_entrega
 * @property string|null $comprovante
 * @property string|null $comprovante_url
 * @property string|null $nome_recebedor
 * @property string|null $documento_recebedor
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $dados_filial
 *
 * @property \App\Model\Entity\Pessoa $pessoa
 * @property \App\Model\Entity\Objeto $objeto
 * @property \App\Model\Entity\Filial $filial
 * @property \App\Model\Entity\EntregaMeio $entrega_meio
 * @property \App\Model\Entity\EntregaMeio $coleta_meio
 * @property \App\Model\Entity\Atualizacao[] $atualizacoes
 * @property \App\Model\Entity\Pagamento[] $pagamentos
 * @property \App\Model\Entity\RotaPedido[] $rota_pedidos
 */
class Pedido extends Entity
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
        'cliente_id' => true,
        'objeto_id' => true,
        'filial_id' => true,
        'entrega_meio_id' => true,
        'coleta_meio_id' => true,
        'observacoes_tratativa_coleta' => true,
        'observacoes_tratativa_entrega' => true,
        'instrucoes' => true,
        'modalidade_distribuicao' => true,
        'meio_entrega' => true,
        'valor_total' => true,
        'status' => true,
        'prazo_envio' => true,
        'previsao_entrega' => true,
        'previsao_coleta' => true,
        'data_chegada' => true,
        'data_tratativa_coleta' => true,
        'data_tratativa_entrega' => true,
        'data_postagem' => true,
        'data_entrega' => true,
        'comprovante' => true,
        'nome_recebedor' => true,
        'documento_recebedor' => true,
        'created' => true,
        'modified' => true,
        'dados_filial' => true,
        'pessoa' => true,
        'objeto' => true,
        'filial' => true,
        'entrega_modalidade' => true,
        'entrega_meio' => true,
        'coleta_meio' => true,
        'atualizacoes' => true,
        'pagamentos' => true,
        'rota_pedidos' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token',
        'etapa',
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'status_formatado',
        'etapa',
        'comprovante_url',
    ];

    /**
     * @return string|null
     */
    protected function _getStatusFormatado(): ?string
    {
        $tmp = $this->status;
        $status = PedidosTable::STATUS;

        if (isset($status[$tmp])) {
            return $status[$tmp];
        }

        return $tmp;
    }

    /**
     * @return string|null
     */
    protected function _getEtapa(): ?string
    {
        $modalidades = PedidosTable::MODALIDADE_DISTRIBUICAO;
        //$etapa = $this->modalidade_distribuicao;
        $etapa = '';

        if (empty($this->data_chegada) && $this->modalidade_distribuicao === PedidosTable::COLETA) {
            $etapa = PedidosTable::COLETA;
        }
        if (empty($this->data_chegada) && $this->modalidade_distribuicao === PedidosTable::ENTREGA) {
            $etapa = PedidosTable::ENTREGA;
        }
        if (!empty($this->data_chegada) && empty($this->data_entrega)) {
            $etapa = PedidosTable::ENTREGA;
        }

        return $etapa;
    }

    /**
     * @return string|null
     */
    protected function _getComprovanteUrl()
    {
        $url = null;
        if (!empty($this->comprovante) && is_string($this->comprovante)) {
            $url = Configure::read('App.fullBaseUrl') . '/files/Pedidos/comprovante/' . $this->comprovante;
        }

        return $url;
    }
}
