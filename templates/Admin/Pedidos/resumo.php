<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido[]|\Cake\Collection\CollectionInterface $pedidos
 * @var boolean $isSearch
 * @var array $status
 * @var array $clientes
 * @var array $cidades
 * @var array $classificacoes
 * @var array $unidades_medidas_comprimento
 * @var array $unidades_medidas_peso
 * @var array $filiais
 * @var array $entregaMeios
 * @var array $modalidadeDistribuicao
 */

use Cake\Routing\Router;

?>
<section class="pedidos index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Resumo dos pedidos') ?>
        </li>
    </ol>
    <?= $this->Form->create(null, ['valueSources' => 'query']); ?>
    <div class="card <?php echo !$isSearch ? 'collapsed-card' : null; ?>">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-<?php echo $isSearch ? 'minus' : 'plus'; ?>"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('id', [
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                    ],
                ]);
                echo $this->Form->control('status', [
                    'type' => 'select',
                    'options' => $status,
                    'empty' => 'Todos(a)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('cliente_id', [
                    'type' => 'select',
                    'options' => $clientes,
                    'empty' => 'Todos(a)',
                    'data-ajax-url' => Router::url([
                        'controller' => 'Clientes',
                        'action' => 'all',
                    ]),
                    'data-placeholder' => 'Selecione...',
                    'class' => 'form-control select2ajax',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                    ],
                ]);
                echo $this->Form->control('filial_id', [
                    'type' => 'select',
                    'options' => $filiais,
                    'empty' => 'Todos(a)',
                    'label' => 'Centro de distribuição',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('cidade_id', [
                    'label' => 'Cidade do centro de distribuição',
                    'type' => 'select',
                    'options' => $cidades,
                    'empty' => 'Todos(a)',
                    'data-ajax-url' => Router::url([
                        'controller' => 'Enderecos',
                        'action' => 'cidades',
                    ]),
                    'data-placeholder' => 'Selecione...',
                    'class' => 'form-control select2ajax',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('modalidade_distribuicao', [
                    'label' => 'Modalidade de distribuição',
                    'type' => 'select',
                    'options' => $modalidadeDistribuicao,
                    'empty' => 'Todos(a)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('entrega_meio_id', [
                    'label' => 'Meio de entrega',
                    'type' => 'select',
                    'options' => $entregaMeios,
                    'empty' => 'Todos(as)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('prazo_envio', [
                    'label' => 'Prazo máximo chegada',
                    'type' => 'date',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('previsao_entrega', [
                    'label' => 'Previsão de entrega',
                    'type' => 'date',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('data_entrega', [
                    'label' => 'Data entrega',
                    'type' => 'date',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('objeto_recebido', [
                    'type' => 'select',
                    'options' => [
                        0 => 'Não',
                        1 => 'Sim',
                    ],
                    'empty' => 'Todos(a)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('objeto_entregue', [
                    'type' => 'select',
                    'options' => [
                        0 => 'Não',
                        1 => 'Sim',
                    ],
                    'empty' => 'Todos(a)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fas fa-eraser mr-1"></i>' . __('Limpar'), ['action' => 'resumo'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Limpar')]);
                    echo $this->Form->button('<i class="fas fa-search mr-1"></i>' . __('Buscar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Buscar')]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">
                <?= __('Resumo dos pedidos'); ?>
            </h3>
            <div class="btn-toolbar card-tools ml-auto">
                <a href="<?= Router::url(['action' => 'add']) ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-plus mr-1"></i> Adicionar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="dataTable">
                            <thead>
                            <tr>
                                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('previsao_entrega', 'Previsão Entrega') ?></th>
                                <th scope="col" class="text-center"><?= $this->Paginator->sort('data_chegada', 'Objeto recebido') ?></th>
                                <th scope="col" class="text-center"><?= $this->Paginator->sort('data_entrega', 'Entregue') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr data-identifier="<?= $pedido->id ?>">
                                    <td class="text-center"><?= $this->Number->format($pedido->id) ?></td>
                                    <td><?= h($pedido->previsao_entrega); ?></td>
                                    <td class="text-center">
                                        <?php
                                        if (empty($pedido->data_chegada)) {
                                            echo "<i class='fas fa-times danger'></i>";
                                        } else {
                                            echo "<i class='fas fa-check success'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        if (empty($pedido->data_entrega)) {
                                            echo "<i class='fas fa-times danger'></i>";
                                        } else {
                                            echo "<i class='fas fa-check success'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td><?= h($pedido->status_formatado); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?= $this->Html->link("<i class='fas fa-sync-alt'></i>", ['controller' => 'atualizacoes', 'action' => 'index', $pedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-info action-link', 'title' => 'Atualizações']) ?>
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $pedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link', 'title' => 'Editar']) ?>
                                            <?= $this->Form->postLink(__("<i class='fa fa-trash'></i>"), ['action' => 'delete', $pedido->id], ['escape' => false, 'confirm' => __('Deseja mesmo Excluir ?'), 'class' => 'btn btn-sm btn-danger action-link', 'title' => 'Deletar']) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, exibindo {{current}} registro(s) de um total de {{count}}.')) ?>
                </div>
                <div class="col-sm-12 col-md-7">
                    <ul class="pagination float-right">
                        <?= $this->Paginator->first(__('Primeiro')) ?>
                        <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('Próximo') . ' >') ?>
                        <?= $this->Paginator->last(__('Último')) ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function () {
        const campoCliente = $('#cliente-id');
        const campoCidade = $('#cidade-id');

        var _csrf = '<?= $this->getRequest()->getAttribute('csrfToken') ?>';
        $('#dataTable').tableButtons({
            individualLinks: [
                new FormLink('<?= Router::url(['action' => 'delete'], true); ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf),
            ],
            multipleLinks: [
                new FormLink('<?= Router::url(['action' => 'deleteAll'], true); ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf)
            ]
        });

        $('.boolean-status').click(function () {
            var campoStatus = $(this);
            var field = campoStatus.parent().data('field');
            var id = campoStatus.parent().parent().data('identifier');
            axios.post('<?= Router::url(['action' => 'toggle']) ?>', {
                field: field,
                id: id,
                _csrfToken: _csrf
            }).then(function (response) {
                const data = response.data;

                if (data.pedido[field]) {
                    campoStatus
                        .removeClass('btn-danger')
                        .addClass('btn-success')
                        .text('Ativo');
                } else {
                    campoStatus
                        .removeClass('btn-success')
                        .addClass('btn-danger')
                        .text('Inativo');
                }
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
                data: function (params) {
                    return {
                        nome: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results, function (text, id) {
                            return {
                                text: text,
                                id: id
                            };
                        })
                    };
                }
            },
        });
        campoCidade.select2({
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
                data: function (params) {
                    return {
                        cidade: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results, function (text, id) {
                            return {
                                text: text,
                                id: id
                            };
                        })
                    };
                }
            },
        });
    });
</script>
