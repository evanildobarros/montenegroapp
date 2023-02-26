<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pessoa[]|\Cake\Collection\CollectionInterface $pessoas
 * @var boolean $isSearch
 * @var \Cake\I18n\FrozenDate $data_inicio
 * @var \Cake\I18n\FrozenDate $data_fim
 */

?>
<section class="pessoas index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Entregadores') ?>
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
                    'label' => 'Entregador ID',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                    ],
                ]);
                echo $this->Form->control('nome', [
                    'label' => 'Nome do entregador',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-10 col-xl-10',
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fas fa-eraser mr-1"></i>' . __('Limpar'), ['action' => 'entregas'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Limpar')]);
                    echo $this->Form->button('<i class="fas fa-search mr-1"></i>' . __('Buscar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Buscar')]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card data-filtros">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-sm-12 col-md-12 col-lg-5 col-xl-5 row">
                    <?php
                    echo $this->Form->label('data_inicio', 'Data ínicio:', [
                        'class' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3 col-form-label',
                    ]);
                    echo $this->Form->control('data_inicio', [
                        'type' => 'date',
                        'label' => false,
                        'required' => true,
                        'value' => $data_inicio,
                        'templateVars' => [
                            'classContainer' => 'col-sm-12 col-md-12 col-lg-9 col-xl-9',
                        ],
                    ]);
                    ?>
                </div>
                <div class="form-group col-sm-12 col-md-12 col-lg-5 col-xl-5 row">
                    <?php
                    echo $this->Form->label('data_fim', 'Data Fim:', [
                        'class' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3 col-form-label jus',
                    ]);
                    echo $this->Form->control('data_fim', [
                        'type' => 'date',
                        'label' => false,
                        'required' => true,
                        'value' => $data_fim,
                        'templateVars' => [
                            'classContainer' => 'col-sm-12 col-md-12 col-lg-9 col-xl-9',
                        ],
                    ]);
                    ?>
                </div>
                <div class="form-group col-sm-12 col-md-12 col-lg-2 col-xl-2">
                    <div class="d-flex justify-content-end">
                        <?php echo $this->Form->button(__('Buscar'), ['type' => 'submit', 'class' => 'btn btn-success w-100', 'escape' => false, 'title' => __('Buscar')]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">
                <?= __('Entregas/Coletas'); ?>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="dataTable">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center"><?= $this->Paginator->sort('id') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('nome') ?></th>
                                <th scope="col"
                                    class="text-center"><?= $this->Paginator->sort('entregas_a_fazer', 'A fazer') ?></th>
                                <th scope="col"
                                    class="text-center"><?= $this->Paginator->sort('tentativas') ?></th>
                                <th scope="col"
                                    class="text-center"><?= $this->Paginator->sort('entregas_feitas', 'Feitas') ?></th>
                                <th scope="col"
                                    class="text-center"><?= $this->Paginator->sort('quantidade_entregas', 'Limite diário') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pessoas as $pessoa): ?>
                                <tr data-identifier="<?= $pessoa->id ?>">
                                    <td class="text-center"><?= $this->Number->format($pessoa->id) ?></td>
                                    <td><?= h($pessoa->nome) ?></td>
                                    <td class="text-center">
                                        <?= h((int)$pessoa->total - (int)$pessoa->feitas) ?>
                                    </td>
                                    <td class="text-center"><?= h($pessoa->tentativas) ?></td>
                                    <td class="text-center"><?= h($pessoa->feitas) ?></td>
                                    <td class="text-center"><?= h($pessoa->quantidade_entregas) ?></td>
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
        $('#cidade-id').select2({
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
