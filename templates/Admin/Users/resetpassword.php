<?php
/**
 * @var \App\View\AppView $this
 */
?>
<h3><?= __('Alterar senha') ?></h3>
<?php echo $this->Form->create() ?>
<div class="row">
    <?php
    echo $this->Form->control('password', ['type' => 'password', 'label' => false, 'placeholder' => __('Senha'), 'templateVars' => ['classContainer' => 'col-12']]);
    echo $this->Form->control('password_confirm', ['type' => 'password', 'label' => false, 'placeholder' => __('Confirmar senha'), 'templateVars' => ['classContainer' => 'col-12']]);
    ?>
</div>
<div class="row">
    <div class="col-12">
        <?= $this->Form->button(__('Alterar senha'), ['type' => 'submit', 'class' => 'btn btn-primary btn-block', 'escape' => false, 'title' => __('Alterar senha')]); ?>
    </div>
</div>
<?= $this->Form->end() ?>
