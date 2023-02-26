<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tentativa $tentativa
 * @var array $motivos
 * @var array $rotaPedidos
 * @var int|null $rota_pedido_id
 * @var int|null $motivo_selecionado
 */

use Cake\Routing\Router;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Tentativas'), ['action' => 'index'], ['escape' => false, 'title' => __('Tentativas')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="alert alert-warning" role="alert">
        <strong>Atenção!</strong> As alterações desta tentantiva não irá modificar as atualizações do pedido.
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar Tentativa') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($tentativa); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('data', [
                    'required' => true,
                    'empty' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('motivo_id', [
                    'empty' => 'Selecione...',
                    'required' => true,
                    'options' => $motivos,
                    'value' => $motivo_selecionado,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-9 col-xl-9',
                    ],
                ]);
                echo $this->Form->control('observacoes', [
                    'label' => 'Observações',
                    'required' => false,
                    'class' => 'notCk',
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
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index', $tentativa->rota_pedido_id], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
                    echo $this->Form->button('<i class="fa fa-save mr-1"></i>' . __('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Salvar')]);
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</section>
</section>
