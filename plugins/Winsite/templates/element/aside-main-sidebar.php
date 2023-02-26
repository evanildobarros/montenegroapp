<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/" class="brand-link navbar-info">
        <img src="/img/icone.png" alt="<?= Configure::read('Theme.title') ?>" class="brand-image">
        <span class="brand-text font-weight-bold"><?= Configure::read('Theme.title') ?></span>
    </a>

    <div class="sidebar">
        <?= $this->element('aside/user-panel') ?>

        <nav class="mt-2">
            <?= $this->element('aside/sidebar-menu-' . strtolower($this->getRequest()->getParam('prefix'))) ?>
        </nav>
    </div>
</aside>
