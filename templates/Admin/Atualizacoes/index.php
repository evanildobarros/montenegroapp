<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Atualizacao[]|\Cake\Collection\CollectionInterface $atualizacoes
 * @var \App\Model\Entity\Pedido|\Cake\Collection\CollectionInterface $pedido
 * @var boolean $isSearch
 */

use Cake\Routing\Router;

?>
<section class="atualizacoes index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Atualizações') ?>
        </li>
    </ol>
    <div class="alert alert-info" role="alert">
        Atualizações do pedido #<?= $pedido->id ?>.
        <p class="m-0">
            <strong>Atenção!</strong> Estas atualizações são visíveis na tela de rastramento do pedido e na tela
            do cliente de visualização do pedido.
        </p>
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
                    'type' => 'date',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('titulo', [
                    'label' => 'Título',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-7 col-xl-7',
                    ],
                ]);
                echo $this->Form->control('descricao', [
                    'label' => 'Descrição',
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
                    echo $this->Html->link('<i class="fas fa-eraser mr-1"></i>' . __('Limpar'), ['action' => 'index', $pedido->id], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Limpar')]);
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
                <?= __('Atualizações'); ?>
            </h3>
            <div class="btn-toolbar card-tools ml-auto">
                <a href="<?= Router::url(['action' => 'add', $pedido->id]) ?>" class="btn btn-sm btn-success">
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
                                <th scope="col"><?= $this->Paginator->sort('titulo', 'Título') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('descricao', 'Descrição') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($atualizacoes as $atualizacao): ?>
                                <tr data-identifier="<?= $atualizacao->id ?>">
                                    <td class="text-center"><?= $this->Number->format($atualizacao->id) ?></td>
                                    <td><?= $this->Text->truncate(h($atualizacao->titulo), 50, ['exact' => false]) ?></td>
                                    <td>
                                        <?php
                                        if (!empty($atualizacao->descricao)) {
                                            echo $this->Text->truncate(h($atualizacao->descricao), 60, ['exact' => false]);
                                        }
                                        ?>
                                    </td>
                                    <td><?= h($atualizacao->data) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $atualizacao->id, $pedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link']) ?>
                                            <?= $this->Form->postLink(__("<i class='fa fa-trash'></i>"), ['action' => 'delete', $atualizacao->id], ['escape' => false, 'confirm' => __('Deseja mesmo Excluir ?'), 'class' => 'btn btn-sm btn-danger action-link']) ?>
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
                new FormLink('<?= Router::url(['action' => 'delete'], true); ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf),
            ],
            multipleLinks: [
                new FormLink('<?= Router::url(['action' => 'deleteAll'], true); ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf)
            ]
        });
    });
</script>
