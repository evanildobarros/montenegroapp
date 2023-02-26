<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RotaPedido[] $rotaPedidos
 * @var \App\Model\Entity\Rota $rota
 * @var array $pedidos
 */
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
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Definir rota de entrega') ?>
            </h3>
        </div>
        <?php echo $this->Form->create(); ?>
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                <strong>Atenção!</strong> Marque sim para aquelas coletas em que o entregador fará a entrega na
                mesma rota.
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tr>
                        <th class="hidden"><?= __('Id') ?></th>
                        <th class="w-10"><?= __('Pedido Id') ?></th>
                        <th><?= __('Endereço coleta') ?></th>
                        <th><?= __('Endereço entrega') ?></th>
                        <th class="w-25"><?= __('Mesma rota para entrega?') ?></th>
                    </tr>
                    <?php foreach ($rotaPedidos as $idx => $rotaPedido) : ?>
                        <tr>
                            <td class="hidden">
                                <?php
                                echo $this->Form->control("rota_pedidos[{$idx}][id]", [
                                    'label' => false,
                                    'type' => 'hidden',
                                    'value' => $rotaPedido->id,
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                    ],
                                ]);
                                ?>
                            </td>
                            <td class="w-10">
                                <?php
                                echo h($rotaPedido->pedido_id);
                                echo $this->Form->control("rota_pedidos[{$idx}][pedido_id]", [
                                    'label' => false,
                                    'type' => 'hidden',
                                    'value' => $rotaPedido->pedido_id,
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                    ],
                                ]);
                                ?>
                            </td>
                            <td><?= h($rotaPedido->pedido->objeto->endereco_coleta->endereco_formatado) ?></td>
                            <td><?= h($rotaPedido->pedido->objeto->endereco_entrega->endereco_formatado) ?></td>
                            <td class="w-25">
                                <?php
                                echo $this->Form->control("rota_pedidos[{$idx}][mesma_rota]", [
                                    'label' => false,
                                    'type' => 'checkbox',
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                    ],
                                ]);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
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
