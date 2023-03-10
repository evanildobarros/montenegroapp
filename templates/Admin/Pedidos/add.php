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
 */

use App\Model\Table\PagamentosTable;
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
            <?php echo __('Adicionar') ?>
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
            </ul>
        </div>
        <?php echo $this->Form->create($pedido, ['type' => 'file']); ?>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent">
                <!-- BEGIN PEDIDO -->
                <div class="tab-pane fade show active" id="tabs-pedido" role="tabpanel" aria-labelledby="pedido-tab">
                    <div class="row">
                        <?php
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
                            'label' => 'Modalidade de distribui????o',
                            'required' => true,
                            'empty' => 'Selecione...',
                            'options' => $modalidadeDistribuicao,
                        ]);
                        echo $this->Form->control('filial_id', [
                            'label' => 'Centro de distribui????o que o objeto ser?? entregue',
                            'required' => false,
                            'empty' => 'Selecione...',
                            'options' => $filiais,
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                            ],
                        ]);
                        echo $this->Form->control('instrucoes', [
                            'label' => 'Instru????es',
                            'type' => 'textarea',
                            'class' => 'notCk',
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        ?>
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
                                                    'label' => 'Classifica????o',
                                                    'type' => 'select',
                                                    'empty' => 'Selecione...',
                                                    'options' => $classificacoes,
                                                    'templateVars' => [
                                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                                        'after' => $this->Html->tag(
                                                            'span',
                                                            '* Preencha a altura, largura e profundidade para receber a sugest??o de classifica????o.',
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
                                            'label' => 'Observa????es',
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
                                    <h5 class="block-title">Endere??o de coleta</h5>
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
                                            'label' => 'N??mero',
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
                                            'label' => 'Refer??ncia',
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
                                    <h5 class="block-title">Endere??o de entrega</h5>
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
                                            'label' => 'N??mero',
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
                                            'label' => 'Refer??ncia',
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
                                        Dados do dentinat??rio
                                        <span>(Pessoa que ir?? receber o objeto)</span>
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
                                                    'distribui????o. O valor ?? calculado somando a Data de hoje + valor definido ' .
                                                    'nas configura????es.',
                                            ],
                                            'value' => $prazoEnvio,
                                        ]);
                                        echo $this->Form->control('previsao_coleta', [
                                            'label' => 'Previs??o de coleta',
                                            'empty' => true,
                                            'required' => false,
                                        ]);
                                        echo $this->Form->control('previsao_entrega', [
                                            'label' => 'Previs??o de entrega',
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
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block mt-3">
                                <div class="block-header">
                                    <h5 class="block-title">Pagamentos</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control('pagamentos.0.comentario', [
                                            'label' => 'Coment??rio',
                                            'value' => 'Manual',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control('pagamentos.0.status', [
                                            'label' => 'Status',
                                            'type' => 'select',
                                            'required' => true,
                                            'options' => PagamentosTable::STATUS_TRANSACAO_USER,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END VALORES E PRAZOS -->
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
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
        const body = $('body');

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
         * Fun????o utilizada para mostrar aviso de campo n??o preenchido
         * e remover o aviso j?? existente.
         *
         * @param {string} mensagem mensagem a ser exibida
         * @param {boolean} mostrar mostra ou n??o a mensagem
         */
        function mostrarAviso(mensagem, mostrar = true) {
            const divAlert = $('#alert-warning-valores-prazos');
            const pAviso = $('p#aviso-botao-simular');
            if (pAviso.length > 0) {
                divAlert.find(pAviso).remove();
            }

            if (mostrar) {
                let aviso = `<p id='aviso-botao-simular' class='text-danger mb-0 mt-1'><strong>Aten????o!</strong> ${mensagem}</p>`;

                divAlert.append(aviso);
            }
        }

        function meiosEntrega() {
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
            const cidadeId = campoCidadeId.val();

            if (modalidade == '') {
                mostrarAviso('Preencha o campo Modalidade de Distribui????o');
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
                        cidade_id: cidadeId
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
                            .append("Os meios dispon??veis para este objeto conforme a tabela de pre??os e o meios s??o: ")
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

                        $('#span-classificacao').text('A classifica????o indicada ??: ' + classificacao);
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
            body.addClass('overlay');
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
                    body.removeClass('overlay');
                })
                .catch(function(reason) {
                    console.log(reason);
                    body.removeClass('overlay');
                });
        });
        campoCepColeta.blur(function() {
            body.addClass('overlay');
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
                    body.removeClass('overlay');
                })
                .catch(function(reason) {
                    console.log(reason);
                    body.removeClass('overlay');
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