<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pagamento $pagamento
 */
use \App\Model\Table\PagamentosTable;
?>

<div>
    <h1>Olá <?php echo $pagamento->pedido->pessoa->nome ?></h1>

    <p>
        Houve uma alteração no pagamento do pedido #<?php echo $pagamento->pedido_id ?>
    </p>

    <table>
        <tr>
            <td>Status:</td>
            <td><?php echo PagamentosTable::STATUS_TRANSACAO[$pagamento->status] ?></td>
        </tr>
        <tr>
            <td>Data:</td>
            <td><?php echo $pagamento->created ?></td>
        </tr>
    </table>
</div>

