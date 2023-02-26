<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rota $rota
 * @var \App\Model\Entity\Pedido $pedido
 * @var array $pessoas
 * @var array $status
 * @var array $pedidos_sem_rotas
 * @var array $pedidos_selecionados
 */

use Cake\Routing\Router;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Rotas'), ['action' => 'index'], ['escape' => false, 'title' => __('Rotas')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Adicionar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar Rota') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($rota, ['id' => 'form-rotas']); ?>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="dates mb-4">
                        <p>Criado em <?= h($rota->created); ?></p>
                        <p>Modificado em <?= h($rota->modified); ?></p>
                    </div>
                </div>
                <?php
                echo $this->Form->control('status', [
                    'required' => true,
                    'options' => $status,
                    'empty' => 'Selecione...',
                ]);
                echo $this->Form->control('entregador_id', [
                    'label' => [
                        'text' => 'Entregador',
                        'tooltip' => 'A busca somente trará os entregadores ativos',
                    ],
                    'required' => true,
                    'empty' => 'Selecione...',
                    'options' => $pessoas,
                    'data-ajax-url' => Router::url([
                        'controller' => 'Entregadores',
                        'action' => 'ativos',
                    ]),
                    'data-placeholder' => 'Selecione...',
                    'class' => 'form-control select2ajax',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-9 col-xl-9',
                    ],
                ]);
                echo $this->Form->control('data_saida', [
                    'label' => 'Data de saída',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                ?>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="block mt-3">
                        <div class="block-header">
                            <h5 class="block-title">Paradas</h5>
                        </div>
                        <div class="block-body">
                            <div class="row">
                                <ul>
                                    <?php foreach ($rota->rota_pedidos as $rota_pedido) { ?>
                                        <li>
                                            #<?= $rota_pedido->pedido_id; ?> [<?= $rota_pedido->tipo_formatado; ?>]
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
                    echo $this->Form->button('<i class="fa fa-save mr-1"></i>' . __('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'escapeTitle' => false, 'title' => __('Salvar')]);
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</section>
<script>
    $(document).ready(function () {
        const campoEntregador = $('#entregador-id');
        const campoDataPedido = $('input[name="data_pedido"]');
        const campoPrevisaoEntrega = $('input[name="previsao_entrega"]');
        const campoCodigoPedido = $('#codigo-pedido');
        const pedidosSemRota = $('#pedidos-sem-rota');
        const rotaPedidos = $('#rota-pedidos-ids');

        campoEntregador.select2({
            placeholder: {
                id: '-1',
                text: 'Selecione...'
            },
            language: 'pt-BR',
            minimumInputLength: 2,
            delay: 250,
            cache: true,
            allowClear: true,
            ajax: {
                dataType: 'json',
                data: function (params) {
                    return {
                        nome: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results, function (text, id) {
                            return {
                                text: text,
                                id: id
                            };
                        })
                    };
                }
            },
        });

        $('#form-rotas').submit(function () {
            $('#rota-pedidos-ids option').prop('selected', true);
        });

        function buscarParadas() {
            $('body').addClass('overlay');

            const dataPedido = campoDataPedido.val();
            const previsaoEntrega = campoPrevisaoEntrega.val();
            const codigoPedido = campoCodigoPedido.val();

            const request = axios.get('<?= Router::url(['controller' => 'Rotas', 'action' => 'getPedidosSemRotas']) ?>', {
                params: {
                    data_pedido: dataPedido,
                    previsao_entrega: previsaoEntrega,
                    codigo: codigoPedido,
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            request
                .then(function (response) {
                    const dataPedidos = response.data.pedidos;

                    pedidosSemRota.empty();
                    $.each(dataPedidos, function (id, nome) {
                        let pedidoJaEscolhido = rotaPedidos.find(`option[value=${id}]`).length;

                        if (pedidoJaEscolhido === 0) {
                            pedidosSemRota.append($('<option>').attr('value', id).text(nome));
                        }
                    });
                    pedidosSemRota.trigger('change');

                    $('body').removeClass('overlay');
                })
                .catch(function (reason) {
                    $('body').removeClass('overlay');
                    console.log(reason);
                });
        }

        $('#btn-buscar-pedidos').click(function () {
            buscarParadas();
        });

        if (campoDataPedido.val() !== '') {
            campoDataPedido.trigger('change');
        }

        $('#add').click(function () {
            return !$('#pedidos-sem-rota option:selected').remove().appendTo(rotaPedidos);
        });
        $('#addAll').click(function () {
            return !$('#pedidos-sem-rota option').remove().appendTo(rotaPedidos);
        });
        $('#remove').click(function () {
            return !$('#rota-pedidos-ids option:selected').remove().appendTo(pedidosSemRota);
        });
        $('#removeAll').click(function () {
            return !$('#rota-pedidos-ids option').remove().appendTo(pedidosSemRota);
        });
    });
</script>
