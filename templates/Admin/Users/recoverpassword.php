<?php
/**
 * @var \App\View\AppView $this
 */
?>
<h3><?= __('Recuperar senha') ?></h3>
<?= $this->Form->create() ?>
<div class="row">
    <?= $this->Form->control('username', ['type' => 'email', 'label' => false, 'placeholder' => __('Email'), 'templateVars' => ['classContainer' => 'col-12']]) ?>
</div>
<div class="row">
    <div class="col-12">
        <?= $this->Form->button(__('Requisitar nova senha'), ['type' => 'submit', 'class' => 'btn btn-primary btn-block', 'escape' => false, 'title' => __('Requisitar nova senha')]); ?>
    </div>
</div>
<?= $this->Form->end() ?>
