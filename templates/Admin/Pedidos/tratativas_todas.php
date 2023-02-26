<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido[]|\Cake\Collection\CollectionInterface $pedidos
 * @var boolean $isSearch
 * @var array $status
 * @var int $limite_tentativas
 */

use \App\Model\Table\RotaPedidosTable;
use \Cake\Routing\Router;
?>
<section class="pedidos index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Todos os pedidos com tratativas') ?>
        </li>
    </ol>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Orientações:</strong> Listagem de <strong>todos</strong> pedidos que atingiram o limite de tentativas de coleta ou
        de entrega. Atualmente o limite configurado é <?= h($limite_tentativas); ?>.
        Para alterar acesse <?= $this->Html->link(
            'Ferramentas > Configs',
            Router::url(['controller' => 'Configs', 'action' => 'index']),
            ['class' => 'color-padrao'],
        ); ?>
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
                    'label' => 'Pedido ID',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                    ],
                ]);
                echo $this->Form->control('rota_id', [
                    'label' => 'Rota ID',
                    'type' => 'text',
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
                echo $this->Form->control('parada_tipo', [
                    'label' => 'Tipo',
                    'type' => 'select',
                    'options' => RotaPedidosTable::TIPOS,
                    'empty' => 'Todos(a)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fas fa-eraser mr-1"></i>' . __('Limpar'), ['action' => 'index'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Limpar')]);
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
                <?= __('Todos os pedidos com tratativas'); ?>
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
                                <th scope="col"
                                    class="text-center"><?= $this->Paginator->sort('id', 'Pedido ID') ?></th>
                                <th scope="col"
                                    class="text-center"><?= $this->Paginator->sort('RotaPedidos.rota_id', 'Rota ID') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('RotaPedidos.tipo', 'Tipo') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                                <th scope="col" class="text-center"><?= $this->Paginator->sort('resolvido') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr data-identifier="<?= $pedido->id ?>">
                                    <td class="text-center"><?= $this->Number->format($pedido->id) ?></td>
                                    <td class="text-center"><?= $this->Number->format($pedido['RotaPedidos']['rota_id']) ?></td>
                                    <td><?= RotaPedidosTable::TIPOS[$pedido['RotaPedidos']['tipo']] ?></td>
                                    <td><?= $pedido->status_formatado ?></td>
                                    <td class="text-center">
                                        <?php
                                        switch ($pedido['RotaPedidos']['tipo']) {
                                            case RotaPedidosTable::COLETA:
                                                if (empty($pedido->data_tratativa_coleta)) {
                                                    echo "<i class='fas fa-times danger'></i>";
                                                } else {
                                                    echo "<i class='fas fa-check success'></i>";
                                                }
                                                break;
                                            case RotaPedidosTable::ENTREGA:
                                                if (empty($pedido->data_tratativa_entrega)) {
                                                    echo "<i class='fas fa-times danger'></i>";
                                                } else {
                                                    echo "<i class='fas fa-check success'></i>";
                                                }
                                                break;
                                            default:
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?= $this->Html->link("<i class='fas fa-eye'></i>", ['action' => 'view', $pedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-info action-link', 'title' => 'Visualizar']) ?>
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $pedido->id, '?' => ['redirect' => 'tratativasTodas']], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link', 'title' => 'Editar']) ?>
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
