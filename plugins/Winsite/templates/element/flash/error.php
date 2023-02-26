<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 */
?>
<div class="alert alert-danger alert-dismissible">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <i class="icon fas fa-exclamation-triangle"></i>
    <?= h($message) ?>
</div>
