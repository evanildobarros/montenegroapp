<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Atualizacao $atualizacao
 */

?>

<div>
    <h1>Olá!</h1>

    <p>
        Houve uma atualização no pedido #<?php echo $atualizacao->pedido_id ?>
    </p>

    <table>
        <tr>
            <td>Título:</td>
            <td><?php echo $atualizacao->titulo ?></td>
        </tr>
        <?php if ($atualizacao->descricao) { ?>
            <tr>
                <td>Descrição:</td>
                <td><?php echo $atualizacao->descricao ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td>Data:</td>
            <td><?php echo $atualizacao->data ?></td>
        </tr>
    </table>
</div>

