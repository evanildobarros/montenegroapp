<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array $groups
 */
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Usuários'), ['action' => 'index'], ['escape' => false, 'title' => __('Usuários')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Adicionar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Adicionar Usuário') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($user); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('status', [
                    'checked' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-2',
                    ],
                ]);
                echo $this->Html->tag('div', '', ['class' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12']);
                echo $this->Form->control('nome', [
                    'required' => true,
                ]);
                echo $this->Form->control('username', [
                    'label' => 'E-mail',
                    'type' => 'email',
                    'required' => true,
                ]);
                echo $this->Form->control('group_id', [
                    'options' => $groups,
                    'required' => true,
                    'label' => 'Grupo',
                ]);
                echo $this->Form->control('password', [
                    'label' => 'Senha',
                    'required' => true,
                ]);
                echo $this->Form->control('password_confirm', [
                    'type' => 'password',
                    'label' => 'Confirme sua senha',
                    'required' => true,
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
