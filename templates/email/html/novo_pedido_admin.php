<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido $pedido
 */

?>

<div>
    <h1>Olá!</h1>

    <p>
        O cliente <?php echo h($pedido->pessoa->nome) ?> realizou um novo pedido.
    </p>

</div>

