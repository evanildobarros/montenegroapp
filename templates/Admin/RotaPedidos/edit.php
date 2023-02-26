<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RotaPedido $rotaPedido
 * @var array $rotas
 * @var array $pedidos
 */

use App\Model\Table\PedidosTable;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Rota Pedidos'), ['action' => 'index'], ['escape' => false, 'title' => __('Rota Pedidos')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="alert alert-secondary" role="alert">
        <p class="m-0">
            Criado em <?= h($rotaPedido->created); ?>
        </p>
        <p>
            Modificado em <?= h($rotaPedido->created); ?>
        </p>
        <?php
        if ($rotaPedido->pedido->modalidade_distribuicao == PedidosTable::COLETA) { ?>
            <p class="m-0">Coletado em <?= h($rotaPedido->pedido->data_chegada); ?></p>
        <?php } ?>
        <p class="m-0"> Entregue em
            <?php
            if (empty($rotaPedido->pedido->data_entrega)) {
                echo ': pedido não entregue';
            } else {
                echo h($rotaPedido->pedido->data_entrega);
            }
            ?>
        </p>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar Parada') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($rotaPedido); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('pedido_id', [
                    'options' => $pedidos,
                    'disabled' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('rota_id', [
                    'options' => $rotas,
                    'label' => [
                        'text' => 'Rota',
                        'tooltip' => '#Id - Entregador [Data de saída]',
                    ],
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
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index', $rotaPedido->rota_id], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
                    echo $this->Form->button('<i class="fa fa-save mr-1"></i>' . __('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Salvar')]);
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</section>
