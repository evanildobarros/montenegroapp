<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pessoa $pessoa
 */

use \Cake\Core\Configure;
?>
<div class="site-conteudo">
    <div class="text-center">
        <img src="<?= Configure::read('Theme.logo.caminho'); ?>" alt="<?= Configure::read('Theme.logo.title'); ?>" class="img-logo">
    </div>
    <div class="card">
        <div class="card-body">
            <h4><?= __('Senha alterada!') ?></h4>
            <p>
                <?= __('Sua senha foi alterada com sucesso!') ?>
            </p>
        </div>
    </div>
</div>
