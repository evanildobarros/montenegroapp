<?php
/**
 * @var \App\View\AppView $this
 */
?>

<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Início') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header border">
            <h3 class="card-title">
                <?= __('Bem vindo'); ?>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                </div>
            </div>
        </div>
    </div>
</section>
