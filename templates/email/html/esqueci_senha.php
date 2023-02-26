<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pessoa $pessoa
 */

use Cake\Routing\Router;

?>

<div>
    <h1>Olá, <?php echo h($pessoa->nome); ?>!</h1>

    <p>
        Para redefinir sua senha, <a href="<?php echo Router::url(['controller' => 'pessoas', 'action' => 'redefinir', $pessoa->token, 'plugin' => false, 'prefix' => false], true); ?>">clique aqui</a>.
    </p>
</div>
