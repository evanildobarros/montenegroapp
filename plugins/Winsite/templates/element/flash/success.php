<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 */
?>
<div class="alert alert-success alert-dismissible">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <i class="icon fas fa-check"></i>
    <?php echo h($message) ?>
</div>
