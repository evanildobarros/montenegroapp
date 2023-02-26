<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Config[]|\Cake\Collection\CollectionInterface $configs
 * @var boolean $isSearch
 * @var array $bancos
 */
?>
<section class="configs index content">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <?= $this->Html->link(__('Painel administrativo'), ['controller' => 'users', 'action' => 'dashboard'], ['escape' => false, 'title' => __('Painel Administrativo')]); ?>
        </li>
        <li class="breadcrumb-item active">
            <?= __('Configs') ?>
        </li>
    </ol>
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-three-site-tab" data-toggle="pill" href="#custom-tabs-three-site" role="tab" aria-controls="custom-tabs-three-site" aria-selected="true">Dados gerais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-objetos-tab" data-toggle="pill" href="#custom-tabs-three-objetos" role="tab" aria-controls="custom-tabs-three-objetos" aria-selected="false">Objetos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-mensagens-de-ajuda-tab" data-toggle="pill" href="#custom-tabs-three-mensagens-de-ajuda" role="tab" aria-controls="custom-tabs-three-mensagens-de-ajuda" aria-selected="false">Mensagens de ajuda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-termos-tab" data-toggle="pill" href="#custom-tabs-three-termos" role="tab" aria-controls="custom-tabs-three-termos" aria-selected="false">Políticas e termos</a>
                </li>
            </ul>
        </div>
        <?php echo $this->Form->create(null, ['type' => 'file']); ?>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent">
                <div class="tab-pane fade show active" id="custom-tabs-three-site" role="tabpanel" aria-labelledby="custom-tabs-three-site-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Entregador</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        $i = 1;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'quantidade_entregas',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Quantidade de entregas',
                                                'tooltip' => 'Padrão de quantidade de entregas realizadas por entregador',
                                            ],
                                            'value' => ($configs['quantidade_entregas']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Pedido</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'quantidade_tentativas',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Quantidade de tentativas',
                                                'tooltip' => 'Quantidade de tentativas que o entregador tem ' .
                                                    'para tentar entregar o pedido',
                                            ],
                                            'value' => ($configs['quantidade_tentativas']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'prazo_envio',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'required' => true,
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Prazo em dias para entrega do objeto',
                                                'tooltip' => 'Quando a modalidade de distribuição for Entrega qual ' .
                                                    'será o prazo em dias para o cliente deixar o objeto no centro ' .
                                                    'de distribuição',
                                            ],
                                            'value' => ($configs['prazo_envio']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'email_pedidos',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'email',
                                            'required' => true,
                                            'label' => [
                                                'text' => 'Email novos pedidos',
                                                'tooltip' => 'Email que receberá os avisos de novos pedidos',
                                            ],
                                            'value' => ($configs['email_pedidos']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'email_rotas',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'email',
                                            'required' => true,
                                            'label' => [
                                                'text' => 'Email atualização de rota',
                                                'tooltip' => 'Email que receberá os avisos de atualizações das rotas',
                                            ],
                                            'value' => ($configs['email_rotas']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Rotas</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'rota_iniciar_automatico',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'checkbox',
                                            'label' => [
                                                'text' => 'Iniciar rota automaticamente',
                                                'tooltip' => 'Iniciar a rota automaticamente quando a data de ' .
                                                    'saída for Hoje',
                                            ],
                                            'checked' => $configs['rota_iniciar_automatico'] ?? false,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'rota_finalizar_automatico',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'checkbox',
                                            'label' => [
                                                'text' => 'Finalizar rota automaticamente',
                                                'tooltip' => 'Finalizar a rota automaticamente quando chegar o ' .
                                                    'fim do dia',
                                            ],
                                            'checked' => $configs['rota_finalizar_automatico'] ?? false,
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Suporte</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'email_suporte',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'email',
                                            'label' => [
                                                'text' => 'E-mail para suporte do aplicativo',
                                                'tooltip' => 'Este email irá aparecer na tela "Mais" na opção "Não recebeu seu pedido?"',
                                            ],
                                            'value' => ($configs['email_suporte']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-three-objetos" role="tabpanel" aria-labelledby="custom-tabs-three-objetos-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Classificação para objeto Pequeno</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'pequeno_altura_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Altura máxima (cm)',
                                            ],
                                            'value' => $configs['pequeno_altura_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'pequeno_largura_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Largura máxima (cm)',
                                            ],
                                            'value' => $configs['pequeno_largura_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'pequeno_profundidade_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Comprimento máxima (cm)',
                                            ],
                                            'value' => $configs['pequeno_profundidade_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Classificação para objeto Médio</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'medio_altura_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Altura máxima (cm)',
                                            ],
                                            'value' => $configs['medio_altura_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'medio_largura_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Largura máxima (cm)',
                                            ],
                                            'value' => $configs['medio_largura_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'medio_profundidade_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Comprimento máxima (cm)',
                                            ],
                                            'value' => $configs['medio_profundidade_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Classificação para objeto Grande</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'grande_altura_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Altura máxima (cm)',
                                            ],
                                            'value' => $configs['grande_altura_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'grande_largura_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Largura máxima (cm)',
                                            ],
                                            'value' => $configs['grande_largura_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'grande_profundidade_maxima',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'number',
                                            'label' => [
                                                'text' => 'Comprimento máxima (cm)',
                                            ],
                                            'value' => $configs['grande_profundidade_maxima'],
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-4 col-xl-4',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-three-mensagens-de-ajuda" role="tabpanel" aria-labelledby="custom-tabs-three-mensagens-de-ajuda-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Configurações > Ajuda (APP)</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'link_ajuda',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'url',
                                            'label' => [
                                                'text' => 'Link para uma página de ajuda',
                                                'tooltip' => 'Este link irá aparecer para o usuário no aplicativo',
                                            ],
                                            'value' => ($configs['link_ajuda']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Configurações > Sobre (APP)</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'telefone_sobre',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'phone',
                                            'label' => [
                                                'text' => 'Telefone que aparecerá no APP',
                                                'tooltip' => 'Este telefone irá aparecer para o usuário no aplicativo na página "sobre"',
                                            ],
                                            'value' => ($configs['telefone_sobre']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-5 col-xl-5',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'email_sobre',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'email',
                                            'label' => [
                                                'text' => 'Email de contato que aparecerá no APP',
                                                'tooltip' => 'Este email irá aparecer para o usuário no aplicativo na página "sobre"',
                                            ],
                                            'value' => ($configs['email_sobre']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-7 col-xl-7',
                                            ],
                                        ]);
                                        $i++;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="block">
                                <div class="block-header">
                                    <h5 class="block-title">Pedido</h5>
                                </div>
                                <div class="block-body">
                                    <div class="row">
                                        <?php

                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'mensagem_dashboard',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'label' => [
                                                'text' => 'Mensagem para a Dashboard',
                                                'tooltip' => 'Texto de ajuda que apareça para o cliente na dashboard ',
                                            ],
                                            'value' => ($configs['mensagem_dashboard']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'mensagem_modalidade_distribuicao',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'label' => [
                                                'text' => 'Modalidade de distribuição',
                                                'tooltip' => 'Texto de ajuda que apareça para o cliente no campo ' .
                                                    'modalidade de distribuição',
                                            ],
                                            'value' => ($configs['mensagem_modalidade_distribuicao']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'mensagem_aviso_pedidos',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'label' => [
                                                'text' => 'Avisos sobre os pedidos',
                                                'tooltip' => 'Texto de ajuda que apareça para o cliente ao ' .
                                                    'requisitar uma nova entrega ',
                                            ],
                                            'value' => ($configs['mensagem_aviso_pedidos']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'mensagem_meio_coleta',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'label' => [
                                                'text' => 'Meio de coleta',
                                                'tooltip' => 'Texto de ajuda que apareça para o cliente no campo ' .
                                                    'meio de coleta',
                                            ],
                                            'value' => ($configs['mensagem_meio_coleta']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'mensagem_meio_entrega',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'label' => [
                                                'text' => 'Meio de entrega',
                                                'tooltip' => 'Texto de ajuda que apareça para o cliente no campo ' .
                                                    'meio de entrega',
                                            ],
                                            'value' => ($configs['mensagem_meio_entrega']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'mensagem_filiais',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'class' => 'notCk',
                                            'label' => [
                                                'text' => 'Centros de distribuição',
                                                'tooltip' => 'Texto de ajuda que apareça para o cliente no campo ' .
                                                    'centro de distribuições',
                                            ],
                                            'value' => ($configs['mensagem_filiais']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-three-termos" role="tabpanel" aria-labelledby="custom-tabs-three-mensagens-de-ajuda-tab">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="block">
                                <div class="block-body">
                                    <div class="row">
                                        <?php
                                        //=====================================================================
                                        echo $this->Form->control("configs.{$i}.parametro", [
                                            'value' => 'termos',
                                            'type' => 'hidden',
                                        ]);
                                        echo $this->Form->control("configs.{$i}.valor", [
                                            'type' => 'textarea',
                                            'label' => 'Texto para a página de Políticas e termos',
                                            'value' => ($configs['termos']) ?? '',
                                            'templateVars' => [
                                                'classContainer' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                                            ],
                                        ]);
                                        $i++;
                                        ?>
                                    </div>
                                </div>
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