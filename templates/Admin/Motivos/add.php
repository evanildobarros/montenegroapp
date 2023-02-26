<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Motivo $motivo
 */
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Motivos'), ['action' => 'index'], ['escape' => false, 'title' => __('Motivos')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Adicionar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Adicionar Motivo') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($motivo); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('status', [
                    'checked' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('nome', [
                    'required' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-9 col-xl-9',
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
                    echo $this->Form->button('<i class="fa fa-save mr-1"></i>' . __('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Salvar')]);
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</section>
