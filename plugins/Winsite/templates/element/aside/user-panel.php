<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Routing\Router;

?>

<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        <img src="/winsite/img/user.png" class="img-circle elevation-2" alt="Imagem de Usuário">
    </div>
    <div class="info">
        <?php if (!empty($this->getRequest()->getSession()->read('Auth.User.nome'))) { ?>
            <a href="<?= Router::url(['controller' => 'users', 'action' => 'edit', $this->getRequest()->getSession()->read('Auth.User.id')]) ?>"
               class="d-block">
                <?php echo h($this->getRequest()->getSession()->read('Auth.User.nome')); ?>
            </a>
        <?php } else { ?>
            <a href="#" class="d-block">Bem-vindo</a>
        <?php } ?>
    </div>
</div>
