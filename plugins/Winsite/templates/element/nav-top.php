<?php
/**
 * @var AppView $this
 */

use App\View\AppView;
use Cake\Routing\Router;

?>

<nav class="main-header navbar navbar-expand navbar-dark navbar-info">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto ">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-cogs"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="dropdown-item">
                    <div class="row">
                        <a class="row nav-top align-items-center"
                           href="<?= Router::url(['controller' => 'configs', 'action' => 'index']) ?>">
                            <i class="fas fa-cogs mr-2"></i>
                            <p><?= __('Configurações') ?></p>
                        </a>
                    </div>
                </li>
                <li class="dropdown-divider"></li>
                <li class="dropdown-item">
                    <div class="row">
                        <a class="row nav-top align-items-center"
                           href="<?= Router::url(['controller' => 'groups', 'action' => 'index']) ?>">
                            <i class="fas fa-users nav-icon"></i>
                            <p><?= __('Grupos') ?></p>
                        </a>
                        <a href="<?= Router::url(['controller' => 'groups', 'action' => 'add']) ?>"
                           class="ml-auto icon-add nav-top-plus">
                            <i class="fas fa-plus-circle fa-lg text-success"></i>
                        </a>
                    </div>
                </li>
                <li class="dropdown-divider"></li>
                <li class="dropdown-item">
                    <div class="row">
                        <a class="row nav-top align-items-center"
                           href="<?= Router::url(['controller' => 'users', 'action' => 'index']) ?>">
                            <i class="fas fa-user nav-icon"></i>
                            <p><?= __('Usuários') ?></p>
                        </a>
                        <a href="<?= Router::url(['controller' => 'users', 'action' => 'add']) ?>"
                           class="ml-auto icon-add nav-top-plus">
                            <i class="fas fa-plus-circle fa-lg text-success"></i>
                        </a>
                    </div>
                </li>
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" href="<?= Router::url(['controller' => 'users', 'action' => 'logout']); ?>">
                <i class="fas fa-power-off"></i>
                Sair
            </a>
        </li>

    </ul>
</nav>
