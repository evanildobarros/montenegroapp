<?php
/**
 * @var AppView $this
 */

use App\View\AppView;
use Cake\Routing\Router;

?>

<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <a href="<?= Router::url(['controller' => 'users', 'action' => 'dashboard']) ?>" class="nav-link">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <p><?= __('Dashboard') ?></p>
        </a>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-tools nav-icon"></i>
            <p>
                <?= __('Ferramentas') ?>
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Configs', 'action' => 'index']) ?>">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p><?= __('Configs') ?></p>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Groups', 'action' => 'index']) ?>">
                        <i class="fas fa-users nav-icon"></i>
                        <p><?= __('Grupos') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Groups', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item user-panel">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Users', 'action' => 'index']) ?>">
                        <i class="fas fa-user nav-icon"></i>
                        <p><?= __('Usuários') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Users', 'action' => 'add']) ?>" class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-users-cog nav-icon"></i>
            <p>
                <?= __('Cadastros') ?>
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Clientes', 'action' => 'index']) ?>">
                        <i class="fas fa-users nav-icon"></i>
                        <p><?= __('Clientes') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Clientes', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Entregadores', 'action' => 'index']) ?>">
                        <i class="fas fa-dolly nav-icon"></i>
                        <p><?= __('Entregadores') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Entregadores', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item user-panel">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Filiais', 'action' => 'index']) ?>">
                        <i class="fas fa-building nav-icon"></i>
                        <p><?= __('Centros de distribuição') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Filiais', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'EntregaMeios', 'action' => 'index']) ?>">
                        <i class="fas fa-parachute-box nav-icon"></i>
                        <p><?= __('Meios') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'EntregaMeios', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item user-panel">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Motivos', 'action' => 'index']) ?>">
                        <i class="far fa-comment-dots nav-icon"></i>
                        <p><?= __('Motivos') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Motivos', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Zonas', 'action' => 'index']) ?>">
                        <i class="fas fa-map-signs nav-icon"></i>
                        <p><?= __('Bairros') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Zonas', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'TabelaPrecos', 'action' => 'index']) ?>">
                        <i class="fas fa-tag nav-icon"></i>
                        <p><?= __('Tabela de preços') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'TabelaPrecos', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
        </ul>
    </li>
    <li class="nav-item">
        <div class="nav-link w-100 d-flex align-items-center">
            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'index']) ?>">
                <i class="fas fa-boxes nav-icon"></i>
                <p><?= __('Pedidos') ?></p>
            </a>
            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'add']) ?>"
               class="ml-auto icon-add">
                <i class="fas fa-plus-circle fa-lg text-success"></i>
            </a>
        </div>
    </li>
    <li class="nav-item ">
        <div class="nav-link w-100 d-flex align-items-center">
            <a href="<?= Router::url(['controller' => 'Rotas', 'action' => 'index']) ?>">
                <i class="fas fa-map-marker-alt nav-icon"></i>
                <p><?= __('Rotas') ?></p>
            </a>
            <a href="<?= Router::url(['controller' => 'Rotas', 'action' => 'add']) ?>" class="ml-auto icon-add">
                <i class="fas fa-plus-circle fa-lg text-success"></i>
            </a>
        </div>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-dollar-sign nav-icon"></i>
            <p>
                <?= __('Financeiro') ?>
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <div class="nav-link w-100 d-flex align-items-center">
                    <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'financeiro']) ?>">
                        <i class="fas fa-concierge-bell nav-icon"></i>
                        <p><?= __('Pedidos') ?></p>
                    </a>
                    <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'add']) ?>"
                       class="ml-auto icon-add">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </a>
                </div>
            </li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt nav-icon"></i>
            <p>
                <?= __('Relatórios') ?>
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-search-location nav-icon"></i>
                    <p>
                        <?= __('Tratativas') ?>
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'tratativasTodas']) ?>">
                                <i class="fas fa-align-justify nav-icon"></i>
                                <p><?= __('Todas') ?></p>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'tratativasAndamento']) ?>">
                                <i class="fas fa-history nav-icon"></i>
                                <p><?= __('Em andamento') ?></p>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-boxes nav-icon"></i>
                    <p>
                        <?= __('Pedidos') ?>
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'resumo']) ?>">
                                <i class="fas fa-box-open nav-icon"></i>
                                <p><?= __('Resumo') ?></p>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'aguardandoObjetos']) ?>">
                                <i class="fas fa-archive nav-icon"></i>
                                <p><?= __('Aguardando objeto') ?></p>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item user-panel">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'aguardandoColetas']) ?>">
                                <i class="fas fa-people-carry nav-icon"></i>
                                <p><?= __('Aguardando coleta') ?></p>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item user-panel">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'aguardandoEntregas']) ?>">
                                <i class="fas fa-truck-loading nav-icon"></i>
                                <p><?= __('Aguardando entrega') ?></p>
                            </a>
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'add']) ?>"
                               class="ml-auto icon-add">
                                <i class="fas fa-plus-circle fa-lg text-success"></i>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Pedidos', 'action' => 'aguardandoFinalizarRotas']) ?>">
                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                <p><?= __('Aguardando finalizar rota') ?></p>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-dolly nav-icon"></i>
                    <p>
                        <?= __('Entregadores') ?>
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <div class="nav-link w-100 d-flex align-items-center">
                            <a href="<?= Router::url(['controller' => 'Entregadores', 'action' => 'entregas']) ?>">
                                <i class="fas fa-box-open nav-icon"></i>
                                <p><?= __('Entregas/Coletas') ?></p>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
