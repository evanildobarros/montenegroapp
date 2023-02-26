<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido $pedido
 * @var array $clientes
 * @var array $modalidadeDistribuicao
 * @var array $filiais
 * @var array $entregaMeios
 * @var array $classificacoes
 * @var array $unidades_medidas_peso
 * @var array $unidades_medidas_comprimento
 * @var array $cidades_entregas
 * @var array $cidades_coletas
 * @var array $prazoEnvio
 * @var array $status
 * @var string $redirect
 */

use Cake\Routing\Router;
use \App\Model\Table\PedidosTable;
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Pedidos'), ['action' => 'index'], ['escape' => false, 'title' => __('Pedidos')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pedido-tab" data-toggle="pill" href="#tabs-pedido" role="tab" aria-controls="tabs-pedido" aria-selected="true">Pedido</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="objeto-tab" data-toggle="pill" href="#tabs-objeto" role="tab" aria-controls="tabs-objeto" aria-selected="false">Objeto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="valor-prazo-tab" data-toggle="pill" href="#tabs-valor-prazo" role="tab" aria-controls="tabs-valor-prazo" aria-selected="false">Valores e Prazos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pagamento-tab" data-toggle="pill" href="#tabs-pagamento" role="tab" aria-controls="tabs-pagamento" aria-selected="false">Pagamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tratativas-tab" data-toggle="pill" href="#tabs-tratativas" role="tab" aria-controls="tabs-tratativas" aria-selected="false">Tratativas</a>
                </li>
            </ul>
        </div>
        <?php echo $this->Form->create($pedido, ['type' => 'file']); ?>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent">
                <!-- BEGIN PEDIDO -->
                <div class="tab-pane fade show active" id="tabs-pedido" role="tabpanel" aria-labelledby="pedido-tab">
                    <div class="row">
                        <?php
                        echo $this->Form->control('status', [
                            'required' => true,
                            'options' => $status,
                        ]);
                        echo $this->Html->tag('div', '', [
                            'class' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                        ]);
                        echo $this->Form->control('cliente_id', [
                            'required' => true,
                            'empty' => 'Selecione...',
                            'options' => $clientes,
                            'data-ajax-url' => Router::url([
                                'controller' => 'Clientes',
                                'action' => 'ativos',
                            ]),
                            'data-placeholder' => 'Selecione...',
                            'class' => 'form-control select2ajax',
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        echo $this->Form->control('modalidade_distribuicao', [
                            'label' => 'Modalidade de distribuição',
                            'required' => true,
                            'empty' => 'Selecione...',
                            'options' => $modalidadeDistribuicao,
                        ]);
                        echo $this->Form->control('filial_id', [
                            'label' => 'Centro de distribuição que o objeto será entregue',
                            'required' => false,
                            'empty' => 'Selecione...',
                            'options' => $filiais,
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                            ],
                        ]);
                        echo $this->Form->control('instrucoes', [
                            'label' => 'Instruções',
                            'type' => 'textarea',
                            'class' => 'notCk',
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        ?>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block mt-3">
                                <div class="block-header">
                                    <h5 class="block-title">Datas</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('data_chegada', [
                                            'label' => [
                                                'text' => 'Data da chegada do objeto',
                                                'tooltip' => 'Se for ENTREGA é a data de chegada no centro de distribuição. Se for ' .
                                                    'COLETA é a data de coleta',
                                            ],
                                            'empty' => true,
                                            'required' => false,
                                        ]);
                                        echo $this->Form->control('data_postagem', [
                                            'label' => [
                                                'text' => 'Data da postagem',
                                                'tooltip' => 'Data que o objeto saiu para a ENTREGA',
                                            ],
                                            'empty' => true,
                                            'required' => false,
                                        ]);
                                        echo $this->Form->control('data_entrega', [
                                            'label' => 'Data da entrega',
                                            'empty' => true,
                                            'required' => false,
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block mt-3">
                                <div class="block-header">
                                    <h5 class="block-title">
                                        Dados da entrega <span>(Pessoa que recebeu o objeto)</span>
                                    </h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('nome_recebedor', [
                                            'required' => false,
                                            'label' => 'Nome',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                                            ],
                                        ]);
                                        echo $this->Form->control('documento_recebedor', [
                                            'required' => false,
                                            'type' => 'text',
                                            'label' => 'Documento',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        echo $this->Form->control('comprovante', [
                                            'required' => false,
                                            'type' => 'file',
                                            'label' => 'Comprovante',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-5 col-xl-5',
                                            ],
                                        ]);
                                        if (!empty($pedido->comprovante)) {
                                        ?>
                                            <div class="form-group col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                <?php
                                                echo $this->Form->label('label-button', '&nbsp;', [
                                                    'escape' => false,
                                                ]);
                                                echo $this->Html->link(
                                                    "<i class='fas fa-eye mr-1'></i>Visualizar comprovante",
                                                    $pedido->comprovante_url,
                                                    [
                                                        'escape' => false,
                                                        'target' => '_blank',
                                                        'class' => 'btn btn-info w-100',
                                                    ],
                                                );
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PEDIDO -->
                <!-- BEGIN OBJETO -->
                <div class="tab-pane fade" id="tabs-objeto" role="tabpanel" aria-labelledby="objeto-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block mt-3">
                                <div class="block-header">
                                    <h5 class="block-title">Dados gerais do objeto</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Html->tag('div', '', [
                                            'class' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                        ]);
                                        echo $this->Form->control('objeto.unidade_medida_comprimento', [
                                            'label' => 'UN',
                                            'type' => 'select',
                                            'options' => $unidades_medidas_comprimento,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.altura', [
                                            'type' => 'float',
                                            'decimals' => '3',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.largura', [
                                            'type' => 'float',
                                            'decimals' => '3',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.profundidade', [
                                            'type' => 'float',
                                            'decimals' => '3',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.unidade_medida_peso', [
                                            'label' => 'UN',
                                            'type' => 'select',
                                            'options' => $unidades_medidas_peso,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.peso', [
                                            'type' => 'float',
                                            'decimals' => '3',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Html->tag('div', '', [
                                            'class' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                        ]);
                                        ?>
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 background-cinza alert alert-secondary">
                                            <div class="row">
                                                <?php
                                                echo $this->Form->control('objeto.classificacao', [
                                                    'label' => 'Classificação',
                                                    'type' => 'select',
                                                    'empty' => 'Selecione...',
                                                    'options' => $classificacoes,
                                                    'templateVars' => [
                                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                                        'after' => $this->Html->tag(
                                                            'span',
                                                            '* Preencha a altura, largura e profundidade para receber a sugestão de classificação.',
                                                            [
                                                                'id' => 'span-classificacao',
                                                                'class' => 'help-block',
                                                                'style' => 'font-size: 0.8rem;',
                                                            ]
                                                        ),
                                                    ],
                                                ]);
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        echo $this->Form->control('objeto.observacoes', [
                                            'label' => 'Observações',
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="endereco-coleta" class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Endereço de coleta</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('objeto.endereco_coleta.cep', [
                                            'required' => false,
                                            'label' => 'CEP',
                                            'data-inputmask' => "'mask': '99999-999'",
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_coleta.logradouro', [
                                            'required' => false,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_coleta.numero', [
                                            'required' => false,
                                            'label' => 'Número',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_coleta.bairro', [
                                            'required' => false,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_coleta.complemento', [
                                            'required' => false,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_coleta.referencia', [
                                            'required' => false,
                                            'label' => 'Referência',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_coleta.cidade_id', [
                                            'required' => false,
                                            'empty' => 'Selecione...',
                                            'options' => $cidades_coletas,
                                            'data-ajax-url' => Router::url([
                                                'controller' => 'Enderecos',
                                                'action' => 'cidades',
                                            ]),
                                            'data-placeholder' => 'Selecione...',
                                            'class' => 'form-control select2ajax',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Endereço de entrega</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('objeto.endereco_entrega.cep', [
                                            'label' => 'CEP',
                                            'data-inputmask' => "'mask': '99999-999'",
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_entrega.logradouro', [
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_entrega.numero', [
                                            'label' => 'Número',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_entrega.bairro', [
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_entrega.complemento', [
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_entrega.referencia', [
                                            'label' => 'Referência',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.endereco_entrega.cidade_id', [
                                            'empty' => 'Selecione...',
                                            'required' => true,
                                            'options' => $cidades_entregas,
                                            'data-ajax-url' => Router::url([
                                                'controller' => 'Enderecos',
                                                'action' => 'cidades',
                                            ]),
                                            'data-placeholder' => 'Selecione...',
                                            'class' => 'form-control select2ajax',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">
                                        Dados do dentinatário
                                        <span>(Pessoa que irá receber o objeto)</span>
                                    </h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('objeto.nome_destinatario', [
                                            'required' => true,
                                            'label' => 'Nome',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.celular_destinatario', [
                                            'required' => true,
                                            'type' => 'phone',
                                            'label' => 'Celular',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);
                                        echo $this->Form->control('objeto.telefone_destinatario', [
                                            'required' => false,
                                            'type' => 'phone',
                                            'label' => 'Telefone',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                                            ],
                                        ]);

                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END OBJETO -->
                <!-- BEGIN VALORES E PRAZOS -->
                <div class="tab-pane fade" id="tabs-valor-prazo" role="tabpanel" aria-labelledby="valor-prazo-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div id="alert-warning-valores-prazos" class="alert alert-warning" role="alert">
                                <p>
                                    Preencha os dados da aba <strong>Pedidos</strong>, <strong>Objetos</strong>, em
                                    seguida clique em <strong>Simular</strong> para que os meios de entrega e/ou
                                    coleta, o valor total e os prazos sejam calculados.
                                </p>
                                <button type="button" id="btn-simular" class="btn btn-info w-30">Simular</button>
                            </div>
                        </div>
                        <div id="sugestoes" class="col-sm-12 col-md-12 col-lg-12 col-xl-12"></div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block mt-3">
                                <div class="block-header">
                                    <h5 class="block-title">Meios</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('coleta_meio_id', [
                                            'label' => 'Meio de coleta',
                                            'required' => false,
                                            'empty' => 'Selecione...',
                                            'options' => $entregaMeios,
                                        ]);
                                        echo $this->Form->control('entrega_meio_id', [
                                            'label' => 'Meio de entrega',
                                            'required' => true,
                                            'empty' => 'Selecione...',
                                            'options' => $entregaMeios,
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Prazos</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('prazo_envio', [
                                            'required' => false,
                                            'label' => [
                                                'text' => 'Prazo de envio',
                                                'tooltip' => 'Prazo que o cliente tem para deixar o objeto no centro de ' .
                                                    'distribuição. O valor é calculado somando a Data de hoje + valor definido ' .
                                                    'nas configurações.',
                                            ],
                                            'value' => $prazoEnvio,
                                        ]);
                                        echo $this->Form->control('previsao_coleta', [
                                            'label' => 'Previsão de coleta',
                                            'empty' => true,
                                            'required' => false,
                                        ]);
                                        echo $this->Form->control('previsao_entrega', [
                                            'label' => 'Previsão de entrega',
                                            'empty' => true,
                                            'required' => true,
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 background-cinza alert alert-secondary">
                            <div class="row">
                                <?php
                                echo $this->Form->control('valor_total', [
                                    'type' => 'monetary',
                                    'required' => true,
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END VALORES E PRAZOS -->
                <!-- BEGIN PAGAMENTOS -->
                <div class="tab-pane fade" id="tabs-pagamento" role="tabpanel" aria-labelledby="pagamento-tab">
                    <?php if (!empty($pedido->pagamentos)) : ?>
                        <div class="table-responsive">
                            <table>
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
                        <div class="alert alert-warning" role="alert">
                            Para forçar um novo histórico de pagamento acesse a visualização do pedido > aba pagamentos: <?= $this->Html->link(__('Clique aqui para acessar'), ['action' => 'view', $pedido->id], ['class' => 'color-padrao']) ?>
                        </div>
                    </div>
                </div>
                <!-- END PAGAMENTOS -->
                <!-- BEGIN TRATATIVAS -->
                <div class="tab-pane fade" id="tabs-tratativas" role="tabpanel" aria-labelledby="tratativas-tab">
                    <div class="row">
                        <?php
                        echo $this->Form->control('data_tratativa_coleta', [
                            'type' => 'datetime',
                            'required' => false,
                            'options' => $status,
                        ]);
                        echo $this->Form->control('observacoes_tratativa_coleta', [
                            'label' => 'Observações tratativas coleta',
                            'type' => 'textarea',
                            'class' => 'notCk',
                            'required' => false,
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        echo $this->Form->control('data_tratativa_entrega', [
                            'type' => 'datetime',
                            'required' => false,
                            'options' => $status,
                        ]);
                        echo $this->Form->control('observacoes_tratativa_entrega', [
                            'label' => 'Observações tratativas entrega',
                            'type' => 'textarea',
                            'class' => 'notCk',
                            'required' => false,
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <!-- END TRATATIVAS -->
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => (empty($redirect) ? 'index' : $redirect)], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
                    echo $this->Form->button('<i class="fa fa-save mr-1"></i>' . __('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Salvar')]);
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</section>
<script>
    $(document).ready(function() {
        const campoCepEntrega = $('#objeto-endereco-entrega-cep');
        const campoCepColeta = $('#objeto-endereco-coleta-cep');
        const campoCidadeEntrega = $('#objeto-endereco-entrega-cidade-id');
        const campoCidadeColeta = $('#objeto-endereco-coleta-cidade-id');
        const campoModalidadeDistribuicao = $('#modalidade-distribuicao');
        const campoFilial = $('#filial-id');
        const campoPrazoEnvio = $('input[name="prazo_envio"]');
        const campoCliente = $('#cliente-id');
        const campoMeioColeta = $('#coleta-meio-id');
        const campoPrevisaoColeta = $('#previsao-coleta');
        const modalidadeDistribuicaoEntrega = '<?php echo PedidosTable::ENTREGA ?>';
        const campoAltura = $('#objeto-altura');
        const campoLargura = $('#objeto-largura');
        const campoProfundidade = $('#objeto-profundidade');
        const campoUnComprimento = $('#objeto-unidade-medida-comprimento');
        const campoPeso = $('#objeto-peso');

        function toggleDistribuicao() {
            if (campoModalidadeDistribuicao.val() === modalidadeDistribuicaoEntrega) {
                campoFilial
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required')
                    .show();

                campoPrazoEnvio
                    .val('<?= $prazoEnvio ?>')
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required')
                    .show();

                $('#objeto-endereco-coleta-cep')
                    .val('')
                    .attr('required', false)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-logradouro')
                    .val('')
                    .attr('required', false)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-numero')
                    .val('')
                    .attr('required', false)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-bairro')
                    .val('')
                    .attr('required', false)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-cidade-id')
                    .val('')
                    .empty()
                    .attr('required', false)
                    .closest('.form-group')
                    .addClass('required');

                campoMeioColeta
                    .val('')
                    .attr('required', false)
                    .trigger('change')
                    .closest('.form-group')
                    .removeClass('required')
                    .hide();

                campoPrevisaoColeta
                    .val('')
                    .empty()
                    .attr('required', false)
                    .closest('.form-group')
                    .removeClass('required')
                    .hide();

                $('div#endereco-coleta').hide();
            } else {
                campoFilial
                    .val('')
                    .attr('required', false)
                    .closest('.form-group')
                    .removeClass('required')
                    .hide();

                campoPrazoEnvio
                    .val('')
                    .attr('required', false)
                    .closest('.form-group')
                    .removeClass('required')
                    .hide();

                $('#objeto-endereco-coleta-cep')
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-logradouro')
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-numero')
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-bairro')
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required');

                $('#objeto-endereco-coleta-cidade-id')
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required');

                campoMeioColeta
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required')
                    .show();

                campoPrevisaoColeta
                    .attr('required', true)
                    .closest('.form-group')
                    .addClass('required')
                    .show();

                $('div#endereco-coleta').show();
            }
        }

        /**
         * Função utilizada para mostrar aviso de campo não preenchido
         * e remover o aviso já existente.
         *
         * @param {string} mensagem mensagem a ser exibida
         * @param {boolean} mostrar mostra ou não a mensagem
         */
        function mostrarAviso(mensagem, mostrar = true) {
            const divAlert = $('#alert-warning-valores-prazos');
            const pAviso = $('p#aviso-botao-simular');
            if (pAviso.length > 0) {
                divAlert.find(pAviso).remove();
            }

            if (mostrar) {
                let aviso = `<p id='aviso-botao-simular' class='text-danger mb-0 mt-1'><strong>Atenção!</strong> ${mensagem}</p>`;

                divAlert.append(aviso);
            }
        }

        function meiosEntrega() {
            const body = $('body');
            const modalidade = campoModalidadeDistribuicao.val();
            const coletaCidadeId = campoCidadeColeta.val();
            const entregaCidadeId = campoCidadeEntrega.val();
            const unPeso = $('#objeto-unidade-medida-peso').val();
            const peso = campoPeso.val();
            const unComprimento = $('#objeto-unidade-medida-comprimento').val();
            const altura = campoAltura.val();
            const largura = campoLargura.val();
            const profundidade = campoProfundidade.val();
            const cliente_id = campoCliente.val();
            const cepEntrega = campoCepEntrega.val();
            const cepColeta = campoCepColeta.val();

            if (modalidade == '') {
                mostrarAviso('Preencha o campo Modalidade de Distribuição');
            } else if (peso == '') {
                mostrarAviso('Preencha o campo Peso');
            } else if (altura == '') {
                mostrarAviso('Preencha o campo Altura');
            } else if (largura == '') {
                mostrarAviso('Preencha o campo Largura');
            } else if (profundidade == '') {
                mostrarAviso('Preencha o campo Profundidade');
            } else if (cliente_id == '') {
                mostrarAviso('Preencha o campo Cliente');
            } else if (cepEntrega == '') {
                mostrarAviso('Preencha o campo Cep de entrega');
            } else if (modalidade == '<?= PedidosTable::COLETA ?>' && cepColeta == '') {
                mostrarAviso('Preencha o campo Cep de coleta');
            } else {
                body.addClass('overlay');
                mostrarAviso('', false);

                const request = axios.get('<?= Router::url(['controller' => 'EntregaMeios', 'action' => 'disponiveis']) ?>', {
                    params: {
                        modalidade_distribuicao: modalidade,
                        coleta_cidade_id: coletaCidadeId,
                        entrega_cidade_id: entregaCidadeId,
                        unidade_medida_peso: unPeso,
                        peso: peso,
                        unidade_medida_comprimento: unComprimento,
                        altura: altura,
                        largura: largura,
                        profundidade: profundidade,
                        cliente_id: cliente_id,
                        cep_entrega: cepEntrega,
                        cep_coleta: cepColeta,
                    },
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    }
                });
                request
                    .then(function(response) {
                        const divSugestoes = $('div#sugestoes');
                        const coletaMeios = response.data.meios_coleta;
                        const entregaMeios = response.data.meios_entrega;

                        const divAviso = $('div#aviso-meios-entrega');
                        if (divAviso.length > 0) {
                            divSugestoes.find(divAviso).remove();
                        }

                        const div = $('<div>')
                            .attr({
                                'role': 'alert',
                                'id': 'aviso-meios-entrega',
                            })
                            .addClass('alert alert-info');

                        const ul = $('<ul>');

                        if (modalidade !== modalidadeDistribuicaoEntrega) {
                            coletaMeios.forEach(function(meio) {
                                let li = $('<li>').text('#' + meio.id + ' ' + meio.nome + ' (' + meio.tempo_estimado +
                                    ' dias), ' + meio.valor);

                                ul.append(li);
                            });
                        }

                        entregaMeios.forEach(function(meio) {
                            let li = $('<li>').text('#' + meio.id + ' ' + meio.nome + ' (' + meio.tempo_estimado +
                                ' dias), ' + meio.valor);

                            ul.append(li);
                        });

                        div
                            .append("Os meios disponíveis para este objeto conforme a tabela de preços e o meios são: ")
                            .append(ul);

                        divSugestoes.append(div);
                        body.removeClass('overlay');
                    })
                    .catch(function(reason) {
                        console.log(reason);
                        body.removeClass('overlay');
                    });
            }
        }

        function classificar() {
            const altura = campoAltura.val();
            const largura = campoLargura.val();
            const profundidade = campoProfundidade.val();
            const unidadeMedida = campoUnComprimento.val();

            if (altura !== '' && largura !== '' && profundidade !== '' && unidadeMedida !== '') {
                const request = axios.get('<?= Router::url(['action' => 'classificar']) ?>', {
                    params: {
                        altura: altura,
                        largura: largura,
                        profundidade: profundidade,
                        unidade_medida_comprimento: unidadeMedida,
                    },
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    }
                });
                request
                    .then(function(response) {
                        const classificacao = response.data;

                        $('#span-classificacao').text('A classificação indicada é: ' + classificacao);
                    })
                    .catch(function(reason) {
                        console.log(reason);
                    });
            }
        }

        campoModalidadeDistribuicao.change(function() {
            toggleDistribuicao();
        });

        campoCepEntrega.blur(function() {
            const cep = campoCepEntrega.val();

            const request = axios.get('<?= Router::url(['controller' => 'Enderecos', 'action' => 'cep']) ?>', {
                params: {
                    cep: cep
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            request
                .then(function(response) {
                    const endereco = response.data.endereco;

                    $('#objeto-endereco-entrega-logradouro').val(endereco.logradouro);
                    $('#objeto-endereco-entrega-bairro').val(endereco.bairro);

                    const campoCidadeEntrega = $('#objeto-endereco-entrega-cidade-id');
                    campoCidadeEntrega.empty();
                    campoCidadeEntrega.append(
                        $('<option>')
                        .attr('value', endereco.cidade_id)
                        .text(endereco.localidade + '/' + endereco.uf)
                    );

                    campoCidadeEntrega.trigger('select2.change');
                })
                .catch(function(reason) {
                    console.log(reason);
                });
        });
        campoCepColeta.blur(function() {
            const cep = campoCepColeta.val();

            const request = axios.get('<?= Router::url(['controller' => 'Enderecos', 'action' => 'cep']) ?>', {
                params: {
                    cep: cep
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            request
                .then(function(response) {
                    const endereco = response.data.endereco;

                    $('#objeto-endereco-coleta-logradouro').val(endereco.logradouro);
                    $('#objeto-endereco-coleta-bairro').val(endereco.bairro);

                    const campoCidadeColeta = $('#objeto-endereco-coleta-cidade-id');
                    campoCidadeColeta.empty();
                    campoCidadeColeta.append(
                        $('<option>')
                        .attr('value', endereco.cidade_id)
                        .text(endereco.localidade + '/' + endereco.uf)
                    );

                    campoCidadeColeta.trigger('select2.change');
                })
                .catch(function(reason) {
                    console.log(reason);
                });
        });

        campoCliente.select2({
            placeholder: {
                id: '-1',
                text: 'Selecione...'
            },
            language: 'pt-BR',
            minimumInputLength: 2,
            delay: 250,
            cache: true,
            allowClear: true,
            ajax: {
                dataType: 'json',
                data: function(params) {
                    return {
                        nome: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.results, function(text, id) {
                            return {
                                text: text,
                                id: id
                            };
                        })
                    };
                }
            },
        });
        campoCidadeEntrega.select2({
            placeholder: {
                id: '-1',
                text: 'Selecione...'
            },
            language: 'pt-BR',
            minimumInputLength: 2,
            delay: 250,
            cache: true,
            allowClear: true,
            ajax: {
                dataType: 'json',
                data: function(params) {
                    return {
                        cidade: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.results, function(text, id) {
                            return {
                                text: text,
                                id: id
                            };
                        })
                    };
                }
            },
        });
        campoCidadeColeta.select2({
            placeholder: {
                id: '-1',
                text: 'Selecione...'
            },
            language: 'pt-BR',
            minimumInputLength: 2,
            delay: 250,
            cache: true,
            allowClear: true,
            ajax: {
                dataType: 'json',
                data: function(params) {
                    return {
                        cidade: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.results, function(text, id) {
                            return {
                                text: text,
                                id: id
                            };
                        })
                    };
                }
            },
        });

        $('#btn-simular').click(function() {
            meiosEntrega();
        });
        campoAltura.blur(function() {
            classificar();
        });
        campoLargura.blur(function() {
            classificar();
        });
        campoProfundidade.blur(function() {
            classificar();
        });
        campoUnComprimento.change(function() {
            classificar();
        });

        toggleDistribuicao();
        classificar();
    })
</script>