<?php
/**
 * @var \App\View\AppView $this
 */

$reCAPTCHA_site_key = \Cake\Core\Configure::read('ReCaptcha.site_key');
?>
<h3>Login</h3>

<?= $this->Form->create(null, ['id' => 'form-login']) ?>
<div class="row">
    <?php
    echo $this->Form->control('token', [
        'type' => 'hidden',
        'value' => '',
    ]);
    echo $this->Form->control('username', [
        'type' => 'email',
        'label' => false,
        'placeholder' => __('Email'),
        'templateVars' => ['classContainer' => 'col-12'],
    ]);
    echo $this->Form->control('password', [
        'type' => 'password',
        'label' => false,
        'placeholder' => __('Senha'),
        'templateVars' => ['classContainer' => 'col-12'],
    ]);
    ?>
</div>
<div class="row">
    <div class="col-8">
        <?= $this->Html->link(__('Esqueci minha senha'), ['controller' => 'users', 'action' => 'recoverpassword'], ['escape' => false, 'title' => 'Esqueci minha senha']); ?>
    </div>
    <div class="col-4">
        <?= $this->Form->button(__('Entrar'), ['type' => 'submit', 'class' => 'btn btn-primary btn-block', 'escape' => false, 'title' => __('Entrar')]); ?>
    </div>
</div>
<?= $this->Form->end() ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $reCAPTCHA_site_key ?>"></script>
<script type="text/javascript">
    $('#form-login').submit(function (e) {
        if ($('#token').val() == '') {
            e.preventDefault();

            grecaptcha.ready(function () {
                grecaptcha.execute('<?php echo $reCAPTCHA_site_key ?>', {action: 'submit'}).then(function (token) {
                    $('#token').val(token);
                    $('#form-login').submit();
                });
            });
        }
    });
</script>
