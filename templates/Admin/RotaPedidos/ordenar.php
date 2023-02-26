<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rota $rota
 * @var \App\Model\Entity\Pedido $pedido
 */

use App\Model\Table\RotaPedidosTable;
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Rotas'), ['action' => 'index'], ['escape' => false, 'title' => __('Rotas')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Ordenar paradas') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Ordenar paradas') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($rota, ['id' => 'form-rotas']); ?>
        <div class="card-body">
            <div class="ordenacao">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <h5>Defina a ordem das paradas da rota #<?= h($rota->id) ?>:</h5>
                        <div id="sortable" class="list-group">
                            <?php foreach ($rota->rota_pedidos as $rota_pedido) {

                                ?>
                                <div class="list-group-item">
                                    <div id="pedido-<?= $rota_pedido->pedido->id ?>" class="rota-pedidos-list">
                                        <?php echo $this->Form->control("rota_pedidos.{$rota_pedido->id}.id", ['type' => 'hidden', 'class' => 'input-pedido-id', 'value' => $rota_pedido->id]); ?>
                                        <?php echo $this->Form->control("rota_pedidos.{$rota_pedido->id}.pedido_id", ['type' => 'hidden', 'class' => 'input-pedido-id', 'value' => $rota_pedido->pedido->id]); ?>
                                        <?php echo $this->Form->control("rota_pedidos.{$rota_pedido->id}.ordem", ['type' => 'hidden', 'class' => 'ordem']); ?>
                                        <?php echo $this->Form->control("rota_pedidos.{$rota_pedido->id}.tipo", ['type' => 'hidden', 'class' => 'tema', 'value' => $rota_pedido->tipo]); ?>
                                        <div class="item">
                                            <p>
                                                <span class="cor-padrao"><?= h($rota_pedido->ordem) . ' - '; ?></span>
                                                <?php
                                                $modalidadeDistribuicao = RotaPedidosTable::TIPOS[$rota_pedido->tipo];

                                                if ($rota_pedido->tipo === RotaPedidosTable::COLETA) {
                                                    $texto = $rota_pedido->pedido->objeto->endereco_coleta->endereco_formatado;
                                                } else {
                                                    $texto = $rota_pedido->pedido->objeto->endereco_entrega->endereco_formatado;
                                                }

                                                echo h("#{$rota_pedido->pedido_id} {$texto} [{$modalidadeDistribuicao}]");
                                                ?>
                                            </p>
                                            <div class="btn-toolbar ml-auto">
                                                <button type="button" class="btn btn-sm btn-transparent">
                                                    <i class="fas fa-arrows-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index', $rota->id], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
                    echo $this->Form->button('<i class="fa fa-save mr-1"></i>' . __('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Salvar')]);
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</section>
<script>
    $(document).ready(function () {
        var sortable = document.getElementById('sortable');

        definirOrdem();

        new Sortable(sortable, {
            group: 'shared',
            ghostClass: 'blue-background',
            sort: true,
            animation: 150,
            onUpdate: function (/**Event*/ evt) {
                definirOrdem();
            },
            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
                definirOrdem();
            },
            onRemove: function (/**Event*/evt) {

            },
        });
    });

    function definirOrdem() {
        var ordem = 1;

        $('#sortable div.list-group-item').each(function () {
            let rotaPedidos = $(this).find(".rota-pedidos-list");

            rotaPedidos.find('input.ordem').val(ordem);
            rotaPedidos.find('span').text(ordem + ' - ');
            ordem++;
        });
    }
</script>
