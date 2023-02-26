<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Filial $filial
 * @var array $cidades
 */

use Cake\Routing\Router;
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Centro de distribuição'), ['action' => 'index'], ['escape' => false, 'title' => __('Centro de distribuição')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Adicionar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Adicionar Centro de distribuição') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($filial); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('status', [
                    'checked' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                    ],
                ]);
                echo $this->Html->tag('div', '', ['class' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12']);
                echo $this->Form->control('nome', [
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                echo $this->Form->control('horario_atendimento', [
                    'type' => 'textarea',
                    'label' => [
                        'text' => 'Horário de atendimento',
                        'tooltip' => 'Horário que o cliente pode deixar o objeto a ser entregue',
                    ],
                    'class' => 'notCk',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                ?>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="block mt-3">
                        <div class="block-header">
                            <h5 class="block-title">Dados do endereço</h5>
                        </div>
                        <div class="block-body">
                            <div class="row">
                                <?php
                                echo $this->Form->control('endereco.cep', [
                                    'required' => true,
                                    'label' => 'CEP',
                                    'data-inputmask' => "'mask': '99999-999'",
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.logradouro', [
                                    'required' => true,
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.numero', [
                                    'required' => true,
                                    'label' => 'Número',
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.bairro', [
                                    'required' => true,
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.complemento', [
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.referencia', [
                                    'label' => 'Referência',
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.cidade_id', [
                                    'required' => true,
                                    'empty' => 'Selecione...',
                                    'options' => $cidades,
                                    'data-ajax-url' => Router::url([
                                        'controller' => 'Enderecos',
                                        'action' => 'cidades',
                                    ]),
                                    'data-placeholder' => 'Selecione...',
                                    'class' => 'form-control select2ajax',
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="block">
                        <div class="block-header">
                            <h5 class="block-title">Dados adicionais</h5>
                        </div>
                        <div class="block-body">
                            <div class="row">
                                <?php
                                echo $this->Form->control('observacoes', [
                                    'label' => 'Observações',
                                    'type' => 'textarea',
                                    'class' => 'notCk',
                                ]);
                                ?>
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
        const campoCep = $('#endereco-cep');
        const campoCidade = $('#endereco-cidade-id');

        campoCep.blur(function () {
            const cep = campoCep.val();

            const request = axios.get('<?= Router::url(['controller' => 'Enderecos', 'action' => 'cep']) ?>', {
                params: {
                    cep: cep
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            request
                .then(function (response) {
                    const endereco = response.data.endereco;

                    $('#endereco-logradouro').val(endereco.logradouro);
                    $('#endereco-bairro').val(endereco.bairro);

                    const campoCidade = $('#endereco-cidade-id');
                    campoCidade.empty();
                    campoCidade.append(
                        $('<option>')
                            .attr('value', endereco.cidade_id)
                            .text(endereco.localidade + '/' + endereco.uf)
                    );

                    campoCidade.trigger('select2.change');
                })
                .catch(function (reason) {
                    console.log(reason);
                });
        });
        campoCidade.select2({
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
                        cidade: params.term
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
