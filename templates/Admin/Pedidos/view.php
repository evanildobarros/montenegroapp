<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido $pedido
 */

use \App\Hashids\Hashids;
use App\Model\Table\ObjetosTable;
use App\Model\Table\PagamentosTable;
use App\Model\Table\PedidosTable;
use Cake\Routing\Router;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Pedidos'), ['action' => 'index'], ['escape' => false, 'title' => __('Propostas')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Visualizar') ?>
        </li>
    </ol>
    <div class="alert alert-info" role="alert">
        Para gerar a etiqueta <a href="<?= Router::url(['controller' => 'pdf', 'action' => 'etiqueta', $pedido->id, '_ext' => 'pdf', 'prefix' => false]) ?>" class="color-padrao">Clique aqui</a>
    </div>
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pedido-tab" data-toggle="pill" href="#tabs-pedido" role="tab" aria-controls="tabs-pedido" aria-selected="true">
                        Pedido
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="objeto-tab" data-toggle="pill" href="#tabs-objeto" role="tab" aria-controls="tabs-objeto" aria-selected="false">
                        Objeto
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="atualizacoes-tab" data-toggle="pill" href="#tabs-atualizacoes" role="tab" aria-controls="tabs-atualizacoes" aria-selected="false">
                        Atualizações
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pagamento-tab" data-toggle="pill" href="#tabs-pagamento" role="tab" aria-controls="tabs-pagamento" aria-selected="false">
                        Pagamento
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tratativa-tab" data-toggle="pill" href="#tabs-tratativa" role="tab" aria-controls="tabs-tratativa" aria-selected="false">
                        Tratativas
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent">
                <!-- BEGIN PEDIDO -->
                <div class="tab-pane fade show active" id="tabs-pedido" role="tabpanel" aria-labelledby="pedido-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive">
                                <h4 class="mb-3">Pedido nº <?= h($pedido->id) ?></h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="destaque">Dados do pedido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th><?= __('Valor') ?></th>
                                            <td><?= $this->Number->currency($pedido->valor_total); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Status') ?></th>
                                            <td><?= PedidosTable::STATUS[$pedido->status]; ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Código Rastreio') ?></th>
                                            <td><?= Hashids::getInstance()->encode($pedido->id); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="w-30"><?= __('Cliente') ?></th>
                                            <td class="w-70"><?= $pedido->has('pessoa') ? $this->Html->link($pedido->pessoa->nome, ['controller' => 'Clientes', 'action' => 'edit', $pedido->pessoa->id]) : '' ?></td>
                                        </tr>
                                        <?php if (!empty($pedido->filial)) { ?>
                                            <tr>
                                                <th><?= __('Centro de distribuição que o objeto será entregue') ?></th>
                                                <td>
                                                    <?php
                                                    echo $this->Html->link(' #' . $pedido->filial->id, ['controller' => 'Filiais', 'action' => 'edit', $pedido->filial->id]);
                                                    echo $this->Text->autoParagraph(h($pedido->dados_filial));
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($pedido->meio_coleta) { ?>
                                            <tr>
                                                <th><?= __('Meio de coleta') ?></th>
                                                <td><?= $pedido->has('meio_coleta') ? $this->Html->link($pedido->meio_coleta . ' #' . $pedido->coleta_meio->id, ['controller' => 'EntregaMeios', 'action' => 'edit', $pedido->coleta_meio->id]) : '' ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th><?= __('Meio de entrega') ?></th>
                                            <td><?= $pedido->has('entrega_meio') ? $this->Html->link($pedido->meio_entrega . ' #' . $pedido->entrega_meio->id, ['controller' => 'EntregaMeios', 'action' => 'edit', $pedido->entrega_meio->id]) : '' ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Modalidade de distribuição') ?></th>
                                            <td><?= PedidosTable::MODALIDADE_DISTRIBUICAO[$pedido->modalidade_distribuicao]; ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Instruções') ?></th>
                                            <td><?= $this->Text->autoParagraph(h($pedido->instrucoes)); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Criado em') ?></th>
                                            <td><?= h($pedido->created) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Modificado em') ?></th>
                                            <td><?= h($pedido->modified) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="destaque">Datas e prazos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($pedido->modalidade_distribuicao === PedidosTable::ENTREGA) { ?>
                                            <tr>
                                                <th class="w-30"><?= __('Prazo envio') ?></th>
                                                <td class="w-70"><?= h($pedido->prazo_envio) ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($pedido->modalidade_distribuicao === PedidosTable::COLETA) { ?>
                                            <tr>
                                                <th class="w-30"><?= __('Previsão de coleta') ?></th>
                                                <td class="w-70"><?= h($pedido->previsao_coleta) ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th class="w-30"><?= __('Previsão da entrega') ?></th>
                                            <td class="w-70"><?= h($pedido->previsao_entrega) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Data chegada') ?></th>
                                            <td><?= h($pedido->data_chegada) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Data da postagem') ?></th>
                                            <td><?= h($pedido->data_postagem) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Data da entrega') ?></th>
                                            <td><?= h($pedido->data_entrega) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Nome Recebedor') ?></th>
                                            <td><?= h($pedido->nome_recebedor) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Documento Recebedor') ?></th>
                                            <td><?= h($pedido->documento_recebedor) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Comprovante') ?></th>
                                            <td>
                                                <?php if (!empty($pedido->comprovante)) { ?>
                                                    <?php echo $this->Html->link(
                                                        "<i class='fa fa-eye mr-1'></i> Visualizar",
                                                        '/files/Pedidos/comprovante/' . $pedido->comprovante,
                                                        [
                                                            'escape' => false,
                                                            'class' => 'btn btn-sm btn-info',
                                                            'title' => 'Visualizar',
                                                            'target' => '_blank',
                                                        ]
                                                    ) ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="destaque">Recebedor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th><?= __('Nome Recebedor') ?></th>
                                            <td><?= h($pedido->nome_recebedor) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Documento Recebedor') ?></th>
                                            <td><?= h($pedido->documento_recebedor) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Comprovante') ?></th>
                                            <td>
                                                <?php if (!empty($pedido->comprovante)) { ?>
                                                    <?php echo $this->Html->link(
                                                        "<i class='fa fa-eye mr-1'></i> Visualizar",
                                                        '/files/Pedidos/comprovante/' . $pedido->comprovante,
                                                        [
                                                            'escape' => false,
                                                            'class' => 'btn btn-sm btn-info',
                                                            'title' => 'Visualizar',
                                                            'target' => '_blank',
                                                        ]
                                                    ) ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="related mt-3">
                                <h4>
                                    <?= __('Rota Pedidos') ?>
                                </h4>
                                <?php if (!empty($pedido->rota_pedidos)) : ?>
                                    <div class="table-responsive">
                                        <table class="table-striped w-100">
                                            <tr>
                                                <th><?= __('Rota Id') ?></th>
                                                <th><?= __('Parada Id') ?></th>
                                                <th><?= __('Rota Status') ?></th>
                                                <th><?= __('Tipo') ?></th>
                                                <th><?= __('Ordem') ?></th>
                                                <th><?= __('Entregue') ?></th>
                                                <th><?= __('Criado em') ?></th>
                                                <th><?= __('Modificado em') ?></th>
                                                <th class="actions"><?= __('Ações') ?></th>
                                            </tr>
                                            <?php foreach ($pedido->rota_pedidos as $rotaPedidos) : ?>
                                                <tr>
                                                    <td class="text-center"><?= h($rotaPedidos->rota_id) ?></td>
                                                    <td class="text-center"><?= h($rotaPedidos->id) ?></td>
                                                    <td><?= h($rotaPedidos->rota->status_formatado) ?></td>
                                                    <td><?= h($rotaPedidos->tipo_formatado) ?></td>
                                                    <td class="text-center"><?= h($rotaPedidos->ordem) ?></td>
                                                    <td class="text-center"><?= ($rotaPedidos->entregue) ? 'Sim' : 'Não' ?></td>
                                                    <td><?= h($rotaPedidos->created) ?></td>
                                                    <td><?= h($rotaPedidos->modified) ?></td>
                                                    <td class="actions">
                                                        <?php
                                                        if ($pedido->status != PedidosTable::FINALIZADO) {
                                                            echo $this->Html->link(__('Editar'), ['controller' => 'RotaPedidos', 'action' => 'edit', $rotaPedidos->id], [
                                                                'class' => 'btn btn-sm btn-warning',
                                                            ]);
                                                        }
                                                        echo $this->Html->link("<i class='fas fa-eye'></i> Visualizar", ['controller' => 'RotaPedidos', 'action' => 'index', $rotaPedidos->rota_id], ['escape' => false, 'class' => 'btn btn-sm btn-info action-link', 'title' => 'Visualizar']);
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PEDIDO -->
                <!-- BEGIN OBJETO -->
                <div class="tab-pane fade" id="tabs-objeto" role="tabpanel" aria-labelledby="objeto-tab">
                    <div class="row">
                        <?php if ($pedido->has('objeto')) { ?>
                            <table class="table mt-3">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="destaque">Dados do objeto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="w-30"><?= __('Unidade de medida para comprimento') ?></th>
                                        <td>
                                            <?= h(ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO[$pedido->objeto->unidade_medida_comprimento]) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="w-30"><?= __('Altura') ?></th>
                                        <td><?= h($pedido->objeto->altura) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Largura') ?></th>
                                        <td><?= h($pedido->objeto->largura) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Profundidade') ?></th>
                                        <td><?= h($pedido->objeto->profundidade) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Classificação') ?></th>
                                        <td><?= h(ObjetosTable::CLASSIFICACAO[$pedido->objeto->classificacao]) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Unidade de medida para peso') ?></th>
                                        <td>
                                            <?= h(ObjetosTable::UNIDADE_MEDIDA_PESO[$pedido->objeto->unidade_medida_peso]) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Peso') ?></th>
                                        <td><?= h($pedido->objeto->peso) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Observações') ?></th>
                                        <td><?= $this->Text->autoParagraph(h($pedido->objeto->observacoes)); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if (!empty($pedido->objeto->endereco_coleta)) { ?>
                                <table class="table mt-3">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="destaque">Endereço de Coleta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="w-30"><?= __('CEP') ?></th>
                                            <td class="w-70"><?= h($pedido->objeto->endereco_coleta->cep) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Logradouro') ?></th>
                                            <td><?= h($pedido->objeto->endereco_coleta->logradouro) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Número') ?></th>
                                            <td><?= h($pedido->objeto->endereco_coleta->numero) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Bairro') ?></th>
                                            <td><?= h($pedido->objeto->endereco_coleta->bairro) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Complemento') ?></th>
                                            <td><?= h($pedido->objeto->endereco_coleta->complemento) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Referência') ?></th>
                                            <td><?= h($pedido->objeto->endereco_coleta->referencia) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Cidade/Estado') ?></th>
                                            <td>
                                                <?= h(
                                                    $pedido->objeto->endereco_coleta->cidade->nome . '/' .
                                                        $pedido->objeto->endereco_coleta->cidade->estado->sigla
                                                ) ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <?php if (!empty($pedido->objeto->endereco_entrega)) { ?>
                                <table class="table mt-3">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="destaque">Endereço de Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="w-30"><?= __('Nome destinatário') ?></th>
                                            <td class="w-70"><?= h($pedido->objeto->nome_destinatario) ?></td>
                                        </tr>
                                        <tr>
                                            <th class="w-30"><?= __('Telefone destinatário') ?></th>
                                            <td><?= h($pedido->objeto->telefone_destinatario) ?></td>
                                        </tr>
                                        <tr>
                                            <th class="w-30"><?= __('Celular destinatário') ?></th>
                                            <td><?= h($pedido->objeto->celular_destinatario) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('CEP') ?></th>
                                            <td><?= h($pedido->objeto->endereco_entrega->cep) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Logradouro') ?></th>
                                            <td><?= h($pedido->objeto->endereco_entrega->logradouro) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Número') ?></th>
                                            <td><?= h($pedido->objeto->endereco_entrega->numero) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Bairro') ?></th>
                                            <td><?= h($pedido->objeto->endereco_entrega->bairro) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Complemento') ?></th>
                                            <td><?= h($pedido->objeto->endereco_entrega->complemento) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Referência') ?></th>
                                            <td><?= h($pedido->objeto->endereco_entrega->referencia) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Cidade/Estado') ?></th>
                                            <td>
                                                <?= h(
                                                    $pedido->objeto->endereco_entrega->cidade->nome . '/' .
                                                        $pedido->objeto->endereco_entrega->cidade->estado->sigla
                                                ) ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <!-- END OBJETO -->
                <!-- BEGIN ATUALIZAÇÕES -->
                <div class="tab-pane fade" id="tabs-atualizacoes" role="tabpanel" aria-labelledby="atualizacoes-tab">
                    <?php if (!empty($pedido->atualizacoes)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th><?= __('TÍtulo') ?></th>
                                    <th style="width: 55%"><?= __('Descrição') ?></th>
                                    <th><?= __('Data') ?></th>
                                    <th class="actions"><?= __('Ações') ?></th>
                                </tr>
                                <?php foreach ($pedido->atualizacoes as $atualizacoes) : ?>
                                    <tr>
                                        <td><?= h($atualizacoes->titulo) ?></td>
                                        <td><?= h($atualizacoes->descricao) ?></td>
                                        <td><?= h($atualizacoes->data) ?></td>
                                        <td class="actions">
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['controller' => 'Atualizacoes', 'action' => 'edit', $atualizacoes->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link']) ?>
                                            <?= $this->Form->postLink(__("<i class='fa fa-trash'></i>"), ['controller' => 'Atualizacoes', 'action' => 'delete', $atualizacoes->id], ['escape' => false, 'confirm' => __('Deseja mesmo Excluir ?'), 'class' => 'btn btn-sm btn-danger action-link']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- END ATUALIZAÇÕES -->
                <!-- BEGIN PAGAMENTOS -->
                <div class="tab-pane fade" id="tabs-pagamento" role="tabpanel" aria-labelledby="pagamento-tab">
                    <?php if (!empty($pedido->pagamentos)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th><?= __('Status') ?></th>
                                    <th><?= __('Comentário') ?></th>
                                    <th><?= __('Criado em') ?></th>
                                    <th><?= __('Modificado em') ?></th>
                                </tr>
                                <?php foreach ($pedido->pagamentos as $pagamentos) : ?>
                                    <tr>
                                        <td><?= h($pagamentos->status_formatado) ?></td>
                                        <td><?= h($pagamentos->comentario) ?></td>
                                        <td><?= h($pagamentos->created) ?></td>
                                        <td><?= h($pagamentos->modified) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>
                    <div class="w-100 mt-4">
                        <?php if ($pedido->status != PedidosTable::FINALIZADO) { ?>
                            <button type="button" class="btn btn-sm btn-success w-100" data-ids="<?= $pedido->id ?>" onclick="buttonClick($(this))" title="Adicionar pagamento">
                                <i class="fas fa-plus mr-1"></i>Adicionar
                            </button>
                        <?php } ?>
                    </div>
                </div>
                <!-- END PAGAMENTOS -->
                <!-- BEGIN PEDIDO -->
                <div class="tab-pane fade" id="tabs-tratativa" role="tabpanel" aria-labelledby="tratativa-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th><?= __('Data tratativa coleta') ?></th>
                                            <td><?= h($pedido->data_tratativa_coleta); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Observações tratativa coleta') ?></th>
                                            <td><?= h($pedido->observacoes_tratativa_coleta); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Data tratativa entrega') ?></th>
                                            <td><?= h($pedido->data_tratativa_entrega); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Observações tratativa entrega') ?></th>
                                            <td><?= h($pedido->observacoes_tratativa_entrega); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PEDIDO -->
            </div>
        </div>
    </div>
</section>
<!-- Modal Pagamento -->
<div class="modal fade" id="pagamentoModal" tabindex="-1" role="dialog" aria-labelledby="pagamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="corrigirModalLabel">Adicionar pagamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                echo $this->Form->create(null, [
                    'id' => 'formPagamento',
                    'url' => Router::url([
                        'controller' => 'pagamentos',
                        'action' => 'add',
                    ]),
                ]);
                echo $this->Form->control('pedido_id', [
                    'type' => 'hidden',
                ]);
                echo $this->Form->control('status', [
                    'type' => 'select',
                    'required' => true,
                    'options' => PagamentosTable::STATUS_TRANSACAO_USER,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-success" id="saveModal" form="formPagamento">Salvar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function buttonClick(button) {
        var ids = button.data('ids');
        $('#pedido-id').val(ids);
        $('#pagamentoModal').modal('show');
    }
</script>