<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pessoa $pessoa
 * @var \App\Model\Entity\Email $email
 */

use Cake\Routing\Router;
$url = \Cake\Core\Configure::read('App.fullBaseUrl');
?>

<div>
    <h1>Olá, <?php echo h($pessoa->nome); ?>!</h1>

    <p>
        Para ativar sua conta, <a href="<?php echo $url . Router::url(['controller' => 'pessoas', 'action' => 'ativar', $pessoa->token_ativacao, 'plugin' => false, 'prefix' => false]); ?>">clique aqui</a>.
    </p>
</div>

<img src="<?php echo Router::url(['controller' => 'emails', 'action' => 'info', $email->id], true); ?>" alt="img"/>
