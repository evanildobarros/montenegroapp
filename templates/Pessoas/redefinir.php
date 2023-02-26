<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pessoa $pessoa
 */

use Cake\Core\Configure;

$reCAPTCHA_site_key = Configure::read('ReCaptcha.site_key');
?>
<div class="site-conteudo">
    <div class="corpo">
        <div class="text-center">
            <img src="<?= Configure::read('Theme.logo.caminho'); ?>" alt="<?= Configure::read('Theme.logo.title'); ?>" class="img-logo">
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="form-group"><?= __('Alterar senha') ?></h4>
                <?php echo $this->Form->create($pessoa) ?>
                <div class="row justify-content-end">
                    <?php
                    echo $this->Form->control('token', [
                        'type' => 'hidden',
                        'value' => '',
                    ]);
                    echo $this->Form->control('senha', [
                        'required' => true,
                        'type' => 'password',
                        'label' => false,
                        'value' => '',
                        'placeholder' => __('Senha'),
                        'templateVars' => [
                            'classContainer' => 'col-12',
                        ],
                    ]);
                    echo $this->Form->control('senha_confirm', [
                        'required' => true,
                        'type' => 'password',
                        'label' => false,
                        'value' => '',
                        'placeholder' => __('Confirmar senha'),
                        'templateVars' => [
                            'classContainer' => 'col-12',
                        ],
                    ]);
                    ?>
                    <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                        <?= $this->Form->button(__('Alterar senha'), ['type' => 'submit', 'class' => 'btn btn-primary btn-block', 'escape' => false, 'title' => __('Alterar senha')]); ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $reCAPTCHA_site_key ?>"></script>
<script>
    $(document).ready(function () {
        const form = $('form');
        const senha = $('#senha');
        const senhaConfirmacao = $('#senha-confirm');

        function verificarSenhas() {
            return senha.val() === senhaConfirmacao.val();
        }

        form.submit(function (e) {

            if (!verificarSenhas()) {
                alert('As senhas não conferem!');
                senha.focus();
                return false;
            }

            return true;
        });
    });
    $('form').submit(function (e) {
        $('body').addClass('overlay');
        if ($('#token').val() == '') {
            e.preventDefault();

            grecaptcha.ready(function () {
                grecaptcha.execute('<?php echo $reCAPTCHA_site_key ?>', {action: 'submit'}).then(function (token) {
                    $('#token').val(token);
                    $('form').submit();
                });
            });
        }
        $('body').removeClass('overlay');
    });
</script>
