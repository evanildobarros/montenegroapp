<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EntregaMeio $entregaMeio
 */
?>
<section class="content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item">
            <?php echo $this->Html->link(__('Meios de entrega/coleta'), ['action' => 'index'], ['escape' => false, 'title' => __('Meios de entrega/coleta')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?php echo __('Editar') ?>
        </li>
    </ol>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Atenção!</strong>
        <p class="m-0">
            Se o meio de entrega não possui limites máximos, deixe os campos Altura, Largura e Profundidade vazios.
        </p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php echo __('Editar Meios de entrega/coleta') ?>
            </h3>
        </div>
        <?php echo $this->Form->create($entregaMeio); ?>
        <div class="card-body">
            <div class="row">
                <?php
                echo $this->Form->control('status', [
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-3 col-xl-3',
                    ],
                ]);
                echo $this->Html->tag('div', '', ['class' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12']);
                echo $this->Form->control('nome', [
                    'required' => true,
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-8 col-xl-8',
                    ],
                ]);
                echo $this->Form->control('icone', [
                    'required' => false,
                    'label' => [
                        'text' => 'Ícone',
                        'tooltip' => 'Selecione um item da lista',
                    ],
                    'class' => 'icp icp-opts',
                    'autocomplete' => 'off',
                    'templateVars' => [
                        'classContainer' => 'col-xs-12 col-sm-12 col-md-4 col-lg-4',
                    ],
                ]);
                echo $this->Form->control('altura_maxima', [
                    'required' => false,
                    'label' => [
                        'text' => 'Altura máxima (cm)',
                        'tooltip' => 'Deixe em branco caso o meio não possua limite máximo',
                    ],
                    'type' => 'number',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('largura_maxima', [
                    'required' => false,
                    'label' => [
                        'text' => 'Largura máxima (cm)',
                        'tooltip' => 'Deixe em branco caso o meio não possua limite máximo',
                    ],
                    'type' => 'number',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                echo $this->Form->control('profundidade_maxima', [
                    'required' => false,
                    'label' => [
                        'text' => 'Profundidade máxima (cm)',
                        'tooltip' => 'Deixe em branco caso o meio não possua limite máximo',
                    ],
                    'type' => 'number',
                    'templateVars' => [
                        'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                    ],
                ]);
                ?>
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
