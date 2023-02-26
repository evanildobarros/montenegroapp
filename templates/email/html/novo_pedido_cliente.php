<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido $pedido
 */

?>

<div>
    <h1>Olá, <?php echo h($pedido->pessoa->nome) ?>!</h1>

    <p>
        Obrigado por comprar conosco!
    </p>

</div>

