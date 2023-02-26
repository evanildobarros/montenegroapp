<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tentativa[]|\Cake\Collection\CollectionInterface $tentativas
 * @var \App\Model\Entity\RotaPedido|\Cake\Collection\CollectionInterface $rota_pedido
 * @var boolean $isSearch
 */

use Cake\Routing\Router;

?>
<section class="tentativas index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Tentativas') ?>
        </li>
    </ol>
    <div class="alert alert-info" role="alert">
        Tentativas da rota #<?= $rota_pedido->rota_id ?>, parada #<?= $rota_pedido->id ?>, entregador #<?= $rota_pedido->rota->pessoa->id ?> - <?= $rota_pedido->rota->pessoa->nome ?>
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
                echo $this->Form->control('data', [
                    'type' => 'datetime',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('nome_motivo', [
                    'label' => 'Motivo',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-7 col-xl-7',
                    ],
                ]);
                echo $this->Form->control('observacoes', [
                    'label' => 'Observações',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fas fa-eraser mr-1"></i>' . __('Limpar'), ['action' => 'index', $rota_pedido->id], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Limpar')]);
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
                <?= __('Tentativas'); ?>
            </h3>
            <div class="btn-toolbar card-tools ml-auto">
                <a href="<?= Router::url(['controller' => 'RotaPedidos', 'action' => 'index', $rota_pedido->rota_id]) ?>" class="btn btn-sm btn-purple mr-2">
                    <i class="fas fa-road mr-1"></i> Paradas
                </a>
                <a href="<?= Router::url(['action' => 'add', $rota_pedido->id]) ?>" class="btn btn-sm btn-success">
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
                                <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('nome_motivo', 'Motivo') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('created', 'Criado em') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('modified', 'Modificado em') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($tentativas as $tentativa): ?>
                                <tr data-identifier="<?= $tentativa->id ?>">
                                    <td class="text-center"><?= $this->Number->format($tentativa->id) ?></td>
                                    <td><?= h($tentativa->data) ?></td>
                                    <td><?= $this->Html->link(h($tentativa->nome_motivo), ['controller' => 'Motivos', 'action' => 'edit', $tentativa->motivo->id]) ?></td>
                                    <td><?= h($tentativa->created) ?></td>
                                    <td><?= h($tentativa->modified) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $tentativa->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link']) ?>
                                            <?= $this->Form->postLink(__("<i class='fa fa-trash'></i>"), ['action' => 'delete', $tentativa->id], ['escape' => false, 'confirm' => __('Deseja mesmo Excluir ?'), 'class' => 'btn btn-sm btn-danger action-link']) ?>
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
        var _csrf = '<?= $this->getRequest()->getAttribute('csrfToken') ?>';
        $('#dataTable').tableButtons({
            individualLinks: [
                new FormLink('<?= Router::url(['action' => 'delete'], true) ; ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf),
            ],
            multipleLinks: [
                new FormLink('<?= Router::url(['action' => 'deleteAll'], true) ; ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf)
            ]
        });
    });
</script>
