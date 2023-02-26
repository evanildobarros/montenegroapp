<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RotaPedido[]|\Cake\Collection\CollectionInterface $rotaPedidos
 * @var \App\Model\Entity\Rota|\Cake\Collection\CollectionInterface $rota
 * @var boolean $isSearch
 * @var int $limite_tentativas
 */

use App\Model\Table\RotaPedidosTable;
use Cake\Routing\Router;

?>
<section class="rotaPedidos index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Paradas') ?>
        </li>
    </ol>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Limite de tentativas:</strong> O limite de tentativas configurado é <?= h($limite_tentativas); ?>.
        Para alterar acesse <?= $this->Html->link(
            'Ferramentas > Configs',
            Router::url(['controller' => 'Configs', 'action' => 'index']),
            ['class' => 'color-padrao'],
        ); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="alert alert-info" role="alert">
        Tentativas da rota #<?= $rota->id ?>, entregador #<?= $rota->pessoa->id ?> - <?= $rota->pessoa->nome ?>
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
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('pedido_id', [
                    'type' => 'text',
                    'label' => 'Id do pedido',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('entregue', [
                    'type' => 'select',
                    'options' => [
                        1 => 'Sim',
                        0 => 'Não',
                    ],
                    'empty' => 'Todos (as)',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('tipo', [
                    'type' => 'select',
                    'options' => RotaPedidosTable::TIPOS,
                    'empty' => 'Todos (as)',
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
                <?= __('Paradas'); ?>
            </h3>
            <div class="btn-toolbar card-tools ml-auto">
                <a href="<?= Router::url(['action' => 'ordenar', $rota->id]) ?>"
                   class="btn btn-sm btn-purple mr-2">
                    <i class="fas fa-sort-amount-up-alt mr-1"></i> Ordenar
                </a>
                <a href="<?= Router::url(['controller' => 'rotas', 'action' => 'edit', $rota->id]) ?>"
                   class="btn btn-sm btn-warning mr-2">
                    <i class="fas fa-pencil-alt mr-1"></i> Editar Rota
                </a>
                <a href="<?= Router::url(['controller' => 'rotaPedidos', 'action' => 'add', $rota->id]) ?>"
                   class="btn btn-sm btn-success">
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
                                <th scope="col"><?= $this->Paginator->sort('pedido_id', 'Pedido') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('tipo') ?></th>
                                <th scope="col" class="text-center"><?= $this->Paginator->sort('ordem') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('entregue') ?></th>
                                <th scope="col" class="text-center"><?= $this->Paginator->sort('tentativas') ?></th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rotaPedidos as $rotaPedido): ?>
                                <tr data-identifier="<?= $rotaPedido->id ?>">
                                    <td class="text-center"><?= $this->Number->format($rotaPedido->id) ?></td>
                                    <td>
                                        <?php
                                        if ($rotaPedido->has('pedido')) {
                                            $texto = '';
                                            if ($rotaPedido->tipo === RotaPedidosTable::COLETA) {
                                                $texto = $this->Text->truncate(h($rotaPedido->pedido->objeto->endereco_coleta->endereco_formatado), 100, ['exact' => false]);
                                            } else {
                                                $texto = $this->Text->truncate(h($rotaPedido->pedido->objeto->endereco_entrega->endereco_formatado), 100, ['exact' => false]);
                                            }

                                            echo $this->Html->link("#{$rotaPedido->pedido->id} - {$texto}", ['controller' => 'Pedidos', 'action' => 'view', $rotaPedido->pedido->id]);
                                        }
                                        ?>
                                    </td>
                                    <td><?= h($rotaPedido->tipo_formatado) ?></td>
                                    <td class="text-center"><?= $this->Number->format($rotaPedido->ordem) ?></td>
                                    <td>
                                        <?= ($rotaPedido->entregue) ? '<span class="boolean-status btn-sm btn-success">Sim</span>' : '<span class="boolean-status btn-sm btn-danger">Não</span>'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $tentativas = count($rotaPedido->tentativas);
                                        if ($tentativas >= $limite_tentativas) {
                                            echo $this->Html->tag('span', $tentativas, [
                                                'class' => 'danger',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => 'Limite atingindo',
                                            ]);
                                        } else {
                                            echo $this->Html->tag('span', $tentativas, [
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => 'Limite ainda não atingindo',
                                            ]);
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <?php if (!$rotaPedido->entregue) { ?>
                                                <button type="button" class="btn btn-sm btn-success action-link"
                                                        data-ids="<?= $rotaPedido->id ?>"
                                                        onclick="buttonClick($(this))" title="Entregar objeto">
                                                    <i class="fas fa-check-circle mr-1"></i>Entregar
                                                </button>
                                            <?php } ?>
                                            <?= $this->Html->link("<i class='fas fa-street-view'></i> Tentativas", ['controller' => 'tentativas', 'action' => 'index', $rotaPedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-info action-link', 'title' => 'Tentativas']) ?>
                                            <?= $this->Html->link("<i class='fas fa-pencil-alt'></i>", ['action' => 'edit', $rotaPedido->id], ['escape' => false, 'class' => 'btn btn-sm btn-warning action-link']) ?>
                                            <?= $this->Form->postLink(__("<i class='fa fa-trash'></i>"), ['action' => 'delete', $rotaPedido->id], ['escape' => false, 'confirm' => __('Deseja mesmo Excluir ?'), 'class' => 'btn btn-sm btn-danger action-link']) ?>
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
<!-- Modal Entregar -->
<div class="modal fade" id="entregarModal" tabindex="-1" role="dialog" aria-labelledby="entregarModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="corrigirModalLabel">Entregar objeto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                echo $this->Form->create(null, [
                    'id' => 'formReceber',
                    'url' => Router::url([
                        'controller' => 'rotaPedidos',
                        'action' => 'entregar',
                    ]),
                    'type' => 'file',
                ]);
                echo $this->Form->control('ids', [
                    'type' => 'hidden',
                ]);
                echo $this->Form->control('nome_recebedor', [
                    'type' => 'text',
                    'value' => '',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('documento_recebedor', [
                    'type' => 'text',
                    'value' => '',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('comprovante', [
                    'type' => 'file',
                    'value' => '',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('data', [
                    'type' => 'datetime',
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
        var _csrf = '<?= $this->getRequest()->getAttribute('csrfToken') ?>';
        $('#dataTable').tableButtons({
            individualLinks: [
                new FormLink('<?= Router::url(['action' => 'delete'], true); ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf),
            ],
            multipleLinks: [
                new FormLink('<?= Router::url(['action' => 'deleteAll'], true); ?>/{identifier}', 'fa fa-fw fa-trash mr-1', 'Excluir', _csrf),
                new BotaoComponent('{identifier}', 'fas fa-check-circle mr-1', 'Entregar vários', 'btn-success'),
            ]
        });
    });

    function buttonClick(button) {
        var ids = button.data('ids');
        $('#ids').val(ids);
        $('#entregarModal').modal('show');
    }
</script>
