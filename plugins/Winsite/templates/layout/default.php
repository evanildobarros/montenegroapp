<?php
/**
 * @var AppView $this
 * @var array $theme
 */

use App\View\AppView;
use Cake\Routing\Router;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" id="favicon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex,nofollow"/>
    <title><?= h($theme['title']) . ' | ' . __('Painel Administrativo'); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?= $this->AssetMix->css('app'); ?>
    <?= $this->fetch('css'); ?>

    <?= $this->AssetMix->script('app', ['defer' => false]); ?>
</head>
<body class="sidebar-mini layout-fixed layout-navbar-fixed overlay">
<script>
    $(document).ready(function () {
        $("input:text:eq(0):visible").focus();

        var urlComIndex = '<?php echo Router::url(['controller' => $this->getRequest()->getParam('controller'), 'action' => $this->getRequest()->getParam('action'), 'prefix' => $this->getRequest()->getParam('prefix')]) ?>';
        var urlSemIndex = '<?php echo Router::url(['controller' => $this->getRequest()->getParam('controller'), 'action' => 'index', 'prefix' => $this->getRequest()->getParam('prefix')]) ?>';

        var a = $('.nav-sidebar a[href="' + urlComIndex + '"]');
        if (a.length === 0) {
            a = $('.nav-sidebar a[href="' + urlSemIndex + '"]');
        }
        a.closest('.nav-link').addClass('active').closest('.has-treeview').find('a:first').addClass('active');

    });

    window.onbeforeunload = function () {
        $('body').addClass('overlay');
    };
    window.onload = function () {
        $('body').removeClass('overlay');
    };
</script>
<div class="wrapper">
    <?= $this->element('nav-top'); ?>
    <?= $this->element('aside-main-sidebar'); ?>

    <div class="content-wrapper">
        <?php echo $this->Flash->render(); ?>
        <?php echo $this->fetch('content'); ?>
    </div>
    <?php echo $this->element('footer'); ?>
    <?php echo $this->element('aside-control-sidebar'); ?>
</div>
</body>
</html>
