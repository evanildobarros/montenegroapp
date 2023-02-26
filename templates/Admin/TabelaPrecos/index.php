<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TabelaPreco[]|\Cake\Collection\CollectionInterface $tabelaPrecos
 * @var boolean $isSearch
 * @var array $entregaMeios
 */

use App\Model\Table\TabelaPrecosTable;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Text;

?>
<section class="tabelaPrecos index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Tabela Preços') ?>
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
                echo $this->Form->control('modalidade_distribuicao', [
                    'label' => 'Modalidade de distribuição',
                    'empty' => 'Todos(a)',
                    'options' => TabelaPrecosTable::MODALIDADE_DISTRIBUICAO,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('entrega_meio_id', [
                    'label' => 'Meio de entrega/coleta',
                    'type' => 'select',
                    'options' => $entregaMeios,
                    'empty' => 'Todos(a)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-7 col-xl-7',
                    ],
                ]);
                echo $this->Form->control('nome', [
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
                <?= __('Tabela Preços'); ?>
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
                                <th scope="col"><?= $this->Paginator->sort('nome') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('modalidade_distribuicao', 'Distribuição') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('entrega_meio_id', 'Meios') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('zonas', 'Bairros') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('created', 'Criado em') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('modified', 'Modificado em') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($tabelaPrecos as $tabelaPreco): ?>
                                <tr data-identifier="<?= $tabelaPreco->id ?>">
                                    <td class="text-center"><?= $this->Number->format($tabelaPreco->id) ?></td>
                                    <td><?= h($tabelaPreco->nome) ?></td>
                                    <td><?= TabelaPrecosTable::MODALIDADE_DISTRIBUICAO[$tabelaPreco->modalidade_distribuicao]; ?></td>
                                    <td><?= $tabelaPreco->has('entrega_meio') ? $this->Html->link($tabelaPreco->entrega_meio->nome, ['controller' => 'EntregaMeios', 'action' => 'edit', $tabelaPreco->entrega_meio->id]) : '' ?></td>
                                    <td>
                                        <?php
                                        if ($tabelaPreco->zonas) {
                                            $zonas = Text::toList(Hash::extract($tabelaPreco->zonas, '{n}.nome'), ', ');
                                            echo $zonas;
                                        }
                                        ?>
                                    </td>
                                    <td><?= h($tabelaPreco->created) ?></td>
                                    <td><?= h($tabelaPreco->modified) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $tabelaPreco->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link']) ?>
                                            <?= $this->Form->postLink(__("<i class='fa fa-trash'></i>"), ['action' => 'delete', $tabelaPreco->id], ['escape' => false, 'confirm' => __('Deseja mesmo Excluir ?'), 'class' => 'btn btn-sm btn-danger action-link']) ?>
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

                if (data.tabelaPreco[field]) {
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
    });
</script>
