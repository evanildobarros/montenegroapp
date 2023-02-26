<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TabelaPreco $tabelaPreco
 * @var array $modalidadesDistribuicao
 * @var array $entregaMeios
 * @var array $zonas
 */

use Cake\Utility\Hash;

?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Tabela de preços'), ['action' => 'index'], ['escape' => false, 'title' => __('Tabela de preços')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Atenção!</strong>
        <p class="m-0">
            O peso deve ser informado em <strong>gramas</strong>.
        </p>
        <p class="m-0">
            O tempo estimado deve ser informado em <strong>dias</strong>.
        </p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar tabela de preço') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($tabelaPreco, ['id' => 'form-tabela']); ?>
        <div class="card-body">
            <h5 class="titulo-cadastros">Dados gerais</h5>
            <div class="row">
                <?php
                echo $this->Form->control('nome', [
                    'required' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-5 col-xl-5',
                    ],
                ]);
                echo $this->Form->control('entrega_meio_id', [
                    'empty' => 'Selecione...',
                    'required' => true,
                    'options' => $entregaMeios,
                    'label' => 'Meio de entrega/coleta',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('modalidade_distribuicao', [
                    'label' => 'Modalidade de distribuição',
                    'required' => true,
                    'options' => $modalidadesDistribuicao,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                ?>
            </div>
            <h5 class="titulo-cadastros mt-2">Dados dos bairros</h5>
            <div class="row">
                <?php
                echo $this->Form->control('cidade_id', [
                    'empty' => 'Selecione...',
                    'value' => '',
                    'options' => [],
                    'required' => false,
                    'data-ajax-url' => \Cake\Routing\Router::url([
                        'controller' => 'Enderecos',
                        'action' => 'cidades',

                    ]),
                    'data-placeholder' => 'Selecione...',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    ],
                ]);
                ?>
                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                    <div class="row">
                        <?php
                        echo $this->Form->control('zonas_disponiveis', [
                            'type' => 'select',
                            'label' => 'Bairros disponíveis:',
                            'options' => [],
                            'multiple' => 'multiple',
                            'class' => 'notSelect2',
                            'style' => 'height: 250px;',
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        ?>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <?php
                            echo $this->Form->button("Adicionar Todos <i class='fas fa-angle-double-right'></i>", [
                                'id' => 'addAll',
                                'title' => 'Adicionar Todos',
                                'type' => 'button',
                                'class' => 'btn btn-default float-left',
                                'escapeTitle' => false,
                            ]);
                            echo $this->Form->button('Adicionar <i class="fas fa-angle-right"></i>', [
                                'id' => 'add',
                                'title' => 'Adicionar',
                                'type' => 'button',
                                'class' => 'btn btn-default float-right',
                                'escapeTitle' => false,
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                    <div class="row">
                        <?php
                        $zonasSelecionadas = [];
                        $zonasSelecionadasIds = [];
                        if (!empty($tabelaPreco->zonas)) {
                            $zonasSelecionadasIds = Hash::extract($tabelaPreco->zonas, '{n}.id');
                            Hash::map($tabelaPreco->zonas, '{n}', function ($zona) use (&$zonasSelecionadas) {
                                $zonasSelecionadas[$zona->id] = $zona->nome;

                                if (isset($zona->cidade->nome)) {
                                    $zonasSelecionadas[$zona->id] .=  ' (' . $zona->cidade->nome . '/' .
                                        $zona->cidade->estado->sigla . ')';
                                }
                            });
                        }
                        echo $this->Form->control('zonas._ids', [
                            'label' => 'Bairros selecionadas:',
                            'type' => 'select',
                            'required' => false,
                            'class' => 'notSelect2',
                            'multiple' => 'multiple',
                            'style' => 'height: 250px;',
                            'value' => $zonasSelecionadasIds,
                            'options' => $zonasSelecionadas,
                            'templateVars' => [
                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                            ],
                        ]);
                        ?>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <?php
                            echo $this->Form->button("<i class='fas fa-angle-double-left'></i> Remover Todos", [
                                'id' => 'removeAll',
                                'title' => 'Adicionar Todos',
                                'type' => 'button',
                                'class' => 'btn btn-default float-left',
                                'escapeTitle' => false,
                            ]);
                            echo $this->Form->button("<i class='fas fa-angle-left'></i> Remover", [
                                'id' => 'remove',
                                'title' => 'Adicionar',
                                'type' => 'button',
                                'class' => 'btn btn-default float-right',
                                'escapeTitle' => false,
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="titulo-cadastros mt-4">Dados dos preços</h5>
            <div class="row mt-3">
                <?php
                echo $this->Form->control('zonas_antigas._ids', [
                    'type' => 'select',
                    'value' => $zonasSelecionadasIds,
                    'options' => $zonasSelecionadas,
                    'required' => false,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12 hidden',
                    ],
                ]);
                ?>
                <div class="form-group col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-3">
                    <?php echo $this->cell('CadastroPesos', [$tabelaPreco])->render(); ?>
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
    $('#form-tabela').submit(function() {
        if ($(this).find('tr[data-quilo="false"]').length === 0) {
            alert('Adicione pelo menos uma faixa');
            return false;
        } else {
            $('#zonas-ids option').prop('selected', true);

            return true;
        }
    });

    $(document).ready(function() {
        const bairrosDisponiveis = $('#zonas-disponiveis');
        const cidadesSelecionadas = $('#zonas-ids');
        const campoCidadeId = $('#cidade-id');

        $('#form-tabela').submit(function() {
            if ($('#zonas-ids option').length === 0) {
                alert('Atenção! Selecione pelo menos um bairro');
                $('#zonas-ids').focus();

                return false;
            }
            if ($(this).find('tr[data-quilo="false"]').length === 0) {
                alert('Adicione pelo menos uma faixa');
                return false;
            } else {
                $('#zonas-ids option').prop('selected', true);

                return true;
            }
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
        campoCidadeId.change(function() {
            const cidade_id = $(this).val();

            const request = axios.get('<?= \Cake\Routing\Router::url(['controller' => 'Zonas', 'action' => 'bairrosPorCidade']) ?>', {
                params: {
                    cidade_id: cidade_id,
                },
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }
            });

            request
                .then(function(response) {
                    const bairro = response.data.results;

                    bairrosDisponiveis.empty();
                    $.each(bairro, function(id, nome) {
                        let bairroJaEscolhido = cidadesSelecionadas.find(`option[value=${id}]`).length;

                        if (bairroJaEscolhido === 0) {
                            bairrosDisponiveis.append($('<option>').attr('value', id).text(nome));
                        }
                    });
                    bairrosDisponiveis.trigger('change');
                })
                .catch(function(reason) {
                    console.log(reason);
                });
        });

        $('#add').click(function() {
            return !$('#zonas-disponiveis option:selected').remove().appendTo(cidadesSelecionadas);
        });
        $('#addAll').click(function() {
            return !$('#zonas-disponiveis option').remove().appendTo(cidadesSelecionadas);
        });
        $('#remove').click(function() {
            return !$('#zonas-ids option:selected').remove().appendTo(bairrosDisponiveis);
        });
        $('#removeAll').click(function() {
            return !$('#zonas-ids option').remove().appendTo(bairrosDisponiveis);
        });

        if (campoCidadeId.val() !== '') {
            campoCidadeId.trigger('change');
        }
    });
</script>