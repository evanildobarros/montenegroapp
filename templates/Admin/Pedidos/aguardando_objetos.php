<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido[]|\Cake\Collection\CollectionInterface $pedidos
 * @var boolean $isSearch
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
use \App\Model\Table\PedidosTable;
?>
<section class="pedidos index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Aguardando objetos') ?>
        </li>
    </ol>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        Modalidade de distribuição <strong><?= PedidosTable::MODALIDADE_DISTRIBUICAO[PedidosTable::ENTREGA] ?></strong>
        : o cliente envia o objeto para o centro de distribuição.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
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
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('previsao_entrega', [
                    'label' => 'Previsão de entrega',
                    'type' => 'date',
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
                    echo $this->Html->link('<i class="fas fa-eraser mr-1"></i>' . __('Limpar'), ['action' => 'aguardandoObjetos'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Limpar')]);
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
                <?= __('Aguardando objeto'); ?>
            </h3>
            <div class="btn-toolbar card-tools ml-auto"></div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="dataTable">
                            <thead>
                            <tr>
                                <th scope="col"><?= $this->Paginator->sort('id', 'Código') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('cliente_id') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('filial_id') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('prazo_envio', 'Prazo máximo chegada') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('previsao_entrega', 'Previsão entrega') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $dataBase = new \Cake\I18n\FrozenDate();
                            $dataBase = $dataBase->format('Y-m-d');
                            foreach ($pedidos as $pedido): ?>
                                <tr data-identifier="<?= $pedido->id ?>">
                                    <td class="text-center"><?= $this->Number->format($pedido->id) ?></td>
                                    <td><?= $pedido->has('pessoa') ? $this->Html->link($pedido->pessoa->nome, ['controller' => 'Pessoas', 'action' => 'view', $pedido->pessoa->id]) : '' ?></td>
                                    <td><?= $pedido->has('filial') ? $this->Html->link($pedido->filial->nome, ['controller' => 'Filiais', 'action' => 'view', $pedido->filial->id]) : '' ?></td>
                                    <td>
                                        <?php
                                        $prazoFormato = $pedido->prazo_envio->format('Y-m-d');
                                        if ($prazoFormato <= $dataBase) {
                                            echo $this->Html->tag('span', h($pedido->prazo_envio), [
                                                'class' => 'text-danger',
                                            ]);
                                        } else {
                                            echo h($pedido->prazo_envio);
                                        }
                                        ?>
                                    </td>
                                    <td><?= h($pedido->previsao_entrega) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <button type="button" class="btn btn-sm btn-success action-link"
                                                    data-ids="<?= $pedido->id ?>"
                                                    onclick="buttonClick($(this))" title="Receber objeto">
                                                <i class="fas fa-check-circle mr-1"></i>Receber
                                            </button>
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $pedido->id, '?' => ['redirect' => 'aguardandoObjetos']], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link', 'title' => 'Editar']) ?>
                                            <?= $this->Html->link("<i class='fas fa-eye'></i>", ['action' => 'view', $pedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-info action-link', 'title' => 'Visualizar']) ?>
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
<!-- Modal Receber -->
<div class="modal fade" id="receberModal" tabindex="-1" role="dialog" aria-labelledby="receberModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="corrigirModalLabel">Receber objeto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                echo $this->Form->create(null, [
                    'id' => 'formReceber',
                    'url' => Router::url([
                        'controller' => 'pedidos',
                        'action' => 'receberObjetos',
                    ]),
                ]);
                echo $this->Form->control('ids', [
                    'type' => 'hidden',
                ]);
                echo $this->Form->control('data_chegada', [
                    'type' => 'datetime',
                    'required' => true,
                    'value' => new \Cake\I18n\FrozenTime(),
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-success" id="saveModal" form="formReceber">Salvar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        const campoCliente = $('#cliente-id');
        const campoCidade = $('#cidade-id');

        $('#dataTable').tableButtons({
            multipleLinks: [
                new BotaoComponent('{identifier}', 'fas fa-check-circle mr-1', 'Receber vários', 'btn-success'),
            ]
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

    function buttonClick(button) {
        var ids = button.data('ids');
        $('#ids').val(ids);
        $('#receberModal').modal('show');
    }
</script>
