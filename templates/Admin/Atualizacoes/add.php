<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Atualizacao $atualizacao
 * @var array $pedido
 * @var int|null $pedido_id
 * @var int|null $pedido_selecionado
 */

use Cake\Routing\Router;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Atualizações'), ['action' => 'index'], ['escape' => false, 'title' => __('Atualizações')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Adicionar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Adicionar Atualização') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($atualizacao); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('pedido_id', [
                    'label' => 'Id do pedido',
                    'required' => true,
                    'empty' => 'Selecione...',
                    'value' => $pedido_selecionado,
                    'options' => $pedido,
                    'data-ajax-url' => Router::url([
                        'controller' => 'Pedidos',
                        'action' => 'all',
                    ]),
                    'data-placeholder' => 'Selecione...',
                    'class' => 'form-control select2ajax',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                    ],
                ]);
                echo $this->Form->control('data', [
                    'required' => true,
                ]);
                echo $this->Form->control('titulo', [
                    'required' => true,
                    'label' => 'Título',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('descricao', [
                    'required' => false,
                    'label' => 'Descrição',
                    'class' => 'notCk',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <div class="input-group">
                    <?php
                    echo $this->Html->link('<i class="fa fa-ban mr-1"></i>' . __('Cancelar'), ['action' => 'index', $pedido_id], ['class' => 'btn btn-default mr-2', 'escape' => false, 'title' => __('Cancelar')]);
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
        const campoPedido = $('#pedido-id');

        campoPedido.select2({
            placeholder: {
                id: '-1',
                text: 'Selecione...'
            },
            language: 'pt-BR',
            minimumInputLength: 1,
            delay: 250,
            cache: true,
            allowClear: true,
            ajax: {
                dataType: 'json',
                data: function (params) {
                    return {
                        pedido_id: params.term
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
    });
</script>
