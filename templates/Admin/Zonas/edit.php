<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Zona $zona
 * @var array $cidadeSelecionada
 */
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Bairros'), ['action' => 'index'], ['escape' => false, 'title' => __('Bairros')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar Bairro') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($zona, ['id' => 'form-zona']); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('nome', [
                    'type' => 'text',
                    'class' => 'notCk',
                    'required' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('nome_abreviado', [
                    'type' => 'text',
                    'label' => 'Nome abreviado',
                    'class' => 'notCk',
                    'required' => false,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                    ],
                ]);
                echo $this->Form->control('cidade_id', [
                    'empty' => 'Selecione...',
                    'value' => $cidadeSelecionada['id'],
                    'options' => [$cidadeSelecionada['id'] => $cidadeSelecionada['text']],
                    'required' => true,
                    'data-ajax-url' => \Cake\Routing\Router::url([
                        'controller' => 'Enderecos',
                        'action' => 'cidades',

                    ]),
                    'data-placeholder' => 'Selecione...',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                    ],
                ]);
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="faixas">
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <input id="adicionar" type="button" class="btn btn-success" value="Adicionar">
                            </td>
                        </tr>
                    </tfoot>
                </table>
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
    $(document).ready(function() {
        let idx = 0;

        function novaFaixa(faixa) {
            const faixas = $('#faixas');

            const tr = $('<tr>');

            const inputId = $('<input>').attr({
                type: 'hidden',
                name: 'faixas[' + idx + '][id]',
                id: 'faixas-' + idx + '-id',
                value: faixa.id
            });
            const inputInicio = $('<input>').attr({
                type: 'text',
                name: 'faixas[' + idx + '][inicio]',
                placeholder: 'CEP Início',
                required: true,
                id: 'faixas-' + idx + '-inicio',
                class: 'form-control',
                value: faixa.inicio
            });
            const inputFim = $('<input>').attr({
                type: 'text',
                name: 'faixas[' + idx + '][fim]',
                placeholder: 'CEP Fim',
                required: true,
                id: 'faixas-' + idx + '-fim',
                class: 'form-control',
                value: faixa.fim
            });
            const inputRemover = $('<input>').attr({
                type: 'button',
                class: 'btn btn-danger',
                value: 'Remover'
            }).click(function() {
                tr.remove();
            });

            const tdInicio = $('<td>').append(inputId).append(inputInicio);
            const tdFim = $('<td>').append(inputFim);
            const tdAcoes = $('<td>').append(inputRemover);

            tr
                .append(tdInicio)
                .append(tdFim)
                .append(tdAcoes);

            faixas.append(tr);

            idx++;
        }

        <?php if ($zona->has('faixas')) { ?>
            <?php foreach ($zona->faixas as $faixa) { ?>
                novaFaixa({
                    id: <?= $faixa->id ?>,
                    inicio: '<?= $faixa->inicio ?>',
                    fim: '<?= $faixa->fim ?>',
                });
            <?php } ?>
        <?php } ?>

        $('#adicionar').click(function() {
            novaFaixa({
                id: null,
                inicio: null,
                fim: null,
                valor: null,
            });
        });
        $('#cidade-id').select2({
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
                data: function(params) {
                    return {
                        cidade: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.results, function(text, id) {
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