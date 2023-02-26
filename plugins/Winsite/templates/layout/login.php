<?php
/**
 * @var \App\View\AppView $this
 * @var array $theme
 */

use Cake\Core\Configure;

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $theme['title'] . ' | Painel Administrativo'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <?= $this->AssetMix->css('app'); ?>
    <?= $this->AssetMix->script('app', ['defer' => false]); ?>
</head>
<body class="hold-transition login-page flex-wrap">
<div class="col-12">
    <div class="container">
        <div class="login-box" style="margin: 10% auto">
            <div class="login-logo">
                <?php echo $this->Html->link("<img src=\"" . Configure::read('Theme.logo.caminho') . "\" alt=\"" . Configure::read('Theme.title') . "\" class=\"w-100 pt-5 pb-4\">", '/', ['escape' => false, 'title' => $theme['title']]); ?>
            </div>
            <?php echo $this->Flash->render(); ?>
            <?php echo $this->Flash->render('auth'); ?>
            <div class="card">
                <div class="card-body login-card-body">
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        </div>
        <div class="page-header border-bottom border-white">
            <h2 class="text-center pt-4">Precisa de Ajuda?</h2>
        </div>
        <p class="lead text-center mt-2">
            <a href="https://suporte.winsite.com.br/" target="_blank" title="Entrar em Contato com Suporte">
                Clique aqui e entre em contato com nosso suporte.
            </a>
        </p>
        <div class="page-header border-bottom border-white"></div>
        <p class="text-center texto-login mt-3">
            Av. Londrina - Zona II, Sala 1 3500, Umuarama - PR, 87502-250 - (44) 3056-1499 / (44) 9 9908-8263
        </p>
        <p class="text-center">
            &copy;&nbsp;
            <a href="https://winsite.com.br/" class="link-winsite" title="Winsite Agência Web" target="_blank">
                Winsite Agência Web
            </a> <?= date('Y') ?>. Todos os diretiros reservados
        </p>
    </div>
</div>
</body>
</html>
