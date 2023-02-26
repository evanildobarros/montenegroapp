<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rota $rota
 */

?>

<div>
    <h1>Olá!</h1>

    <p>
        Houve uma alteração no status da rota #<?php echo $rota->id ?>
    </p>

    <table>
        <tr>
            <td>Entregador:</td>
            <td>#<?php echo $rota->pessoa->id ?> - <?php echo $rota->pessoa->nome ?></td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><?php echo $rota->status_formatado ?></td>
        </tr>
    </table>
</div>

