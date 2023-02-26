<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pessoa $pessoa
 * @var array $tipos
 * @var array $status
 * @var array $cidade
 * @var string $tipo_selecionado
 */

use App\Model\Table\PessoasTable;
use Cake\Routing\Router;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Pessoas'), ['action' => 'index'], ['escape' => false, 'title' => __('Pessoas')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar Entregador') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($pessoa); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('status', [
                    'required' => true,
                    'options' => $status,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('tipo', [
                    'required' => true,
                    'type' => 'radio',
                    'options' => $tipos,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('nome', [
                    'required' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-9 col-xl-9',
                    ],
                ]);
                echo $this->Form->control('data_nascimento', [
                    'label' => 'Data de nascimento',
                    'empty' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('cpf', [
                    'label' => 'CPF',
                    'data-inputmask' => "'mask': '999.999.999-99'",
                ]);
                echo $this->Form->control('cnpj', [
                    'label' => 'CNPJ',
                    'data-inputmask' => "'mask': '99.999.999/9999-99'",
                ]);
                echo $this->Form->control('telefone', [
                    'type' => 'phone',
                ]);
                echo $this->Form->control('celular', [
                    'required' => true,
                    'type' => 'phone',
                ]);
                echo $this->Form->control('nome_representante', [
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-5 col-xl-5',
                    ],
                ]);
                echo $this->Form->control('celular_representante', [
                    'type' => 'phone',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Form->control('email_representante', [
                    'type' => 'email',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                ?>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="block mt-3">
                        <div class="block-header">
                            <h5 class="block-title">Dados de acesso ao aplicativo</h5>
                        </div>
                        <div class="block-body">
                            <div class="row">
                                <?php
                                echo $this->Form->control('email', [
                                    'required' => true,
                                ]);
                                echo $this->Form->control('senha', [
                                    'required' => false,
                                    'value' => '',
                                    'type' => 'password',
                                ]);
                                echo $this->Form->control('senha_confirm', [
                                    'required' => false,
                                    'type' => 'password',
                                    'label' => 'Confirmar senha',
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="block">
                        <div class="block-header">
                            <h5 class="block-title">Dados do endereço</h5>
                        </div>
                        <div class="block-body">
                            <div class="row">
                                <?php
                                echo $this->Form->control('endereco.cep', [
                                    'label' => 'CEP',
                                    'data-inputmask' => "'mask': '99999-999'",
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.logradouro', [
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.numero', [
                                    'label' => 'Número',
                                    'templateVars' => [
                                        'classContainer' => 'col-sm-12 col-md-12 col-lg-2 col-xl-2',
                                    ],
                                ]);
                                echo $this->Form->control('endereco.bairro', [
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
                                    'empty' => 'Selecione...',
                                    'options' => $cidade,
                                    'required' => true,
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
                            <h5 class="block-title">Dados de contrato</h5>
                        </div>
                        <div class="block-body">
                            <div class="row">
                                <?php
                                echo $this->Form->control('quantidade_entregas', [
                                    'type' => 'int',
                                    'label' => [
                                        'text' => 'Quantidade limite de entregas/coletas por dia',
                                        'tooltip' => 'Deixe em branco para entregas/coletas sem limite',
                                    ],
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
        const campoTipo = $('input[name="tipo"]');
        const campoDatanascimento = $('input[name="data_nascimento"]');
        const campoCpf = $('#cpf');
        const campoCnpj = $('#cnpj');
        const campoCep = $('#endereco-cep');
        const campoCidade = $('#endereco-cidade-id');
        const campoNomeRepresentante = $('#nome-representante');
        const campoCelularRepresentante = $('#celular-representante');
        const campoEmailRepresentante = $('#email-representante');
        const fisica = '<?= PessoasTable::FISICA ?>';
        const juridica = '<?= PessoasTable::JURIDICA ?>';

        function toggleTipo() {
            switch ($('input[name="tipo"]:checked').val()) {
                case fisica:
                    campoCpf.closest('.form-group').show();
                    campoCnpj.val('').closest('.form-group').hide();
                    campoNomeRepresentante.val('').closest('.form-group').hide();
                    campoCelularRepresentante.val('').closest('.form-group').hide();
                    campoEmailRepresentante.val('').closest('.form-group').hide();
                    campoDatanascimento.closest('.form-group').show();
                    break;
                case juridica:
                    campoCpf.val('').closest('.form-group').hide();
                    campoCnpj.closest('.form-group').show();
                    campoNomeRepresentante.closest('.form-group').show();
                    campoCelularRepresentante.closest('.form-group').show();
                    campoEmailRepresentante.closest('.form-group').show();
                    campoDatanascimento.val('').closest('.form-group').hide();
                    break;
            }
        }

        campoTipo.change(function () {
            toggleTipo();
        });

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

        toggleTipo();
    });
</script>
