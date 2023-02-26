<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pedido[] $pedidos
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$reCAPTCHA_site_key = Configure::read('ReCaptcha.site_key');
?>
<div class="site-conteudo">
    <div class="cabecalho">
        <div class="text-center">
            <img src="<?= Configure::read('Theme.logo.caminho'); ?>" alt="<?= Configure::read('Theme.logo.title'); ?>" class="img-logo">
        </div>
        <div class="card">
            <div class="card-body">
                <?= $this->Form->create(null, ['id' => 'form-rastrear']) ?>
                <h3>
                    Para acompanhar seu pedido, digite seu CPF/CNPJ e o código do pedido ou código de
                    rastreio
                </h3>
                <div class="row justify-content-end">
                    <?php
                    echo $this->Form->control('token', [
                        'type' => 'hidden',
                        'value' => '',
                    ]);
                    echo $this->Form->control('documento', [
                        'required' => true,
                        'type' => 'text',
                        'label' => false,
                        'placeholder' => __('Digite aqui o CPF ou CNPJ'),
                        'templateVars' => ['classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6'],
                    ]);
                    echo $this->Form->control('parametro', [
                        'required' => true,
                        'type' => 'text',
                        'label' => false,
                        'placeholder' => __('Cód. do pedido ou cód. de rastreio'),
                        'templateVars' => ['classContainer' => 'col-sm-12 col-md-12 col-lg-6 col-xl-6'],
                    ]);
                    ?>
                    <p class="informacao col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        * Os pedidos ficam disponíveis por até 60 dias após ocorrida a entrega.
                    </p>
                    <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                        <button type="submit" class="btn btn-success w-100 g-recaptcha">
                            <i class="fas fa-paper-plane"></i> Rastrear
                        </button>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
    <?php if (empty(!$this->getRequest()->getData())) { ?>
        <div class="corpo">
            <div class="card">
                <div class="card-body">
                    <?php if (empty($pedidos)) { ?>
                        <p>Nenhum pedido encontrado com os dados informados!</p>
                    <?php } else { ?>
                        <h3>Clique em visualizar para acompanhar o rastreio</h3>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th><?= __('Nº Pedido') ?></th>
                                    <th><?= __('Data') ?></th>
                                    <th><?= __('Título') ?></th>
                                    <th><?= __('Ações') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo h($pedido->id);
                                            ?>
                                        </td>
                                        <td>
                                            <?= h($pedido->atualizacoes[0]->data) ?>
                                        </td>
                                        <td>
                                            <?= h($pedido->atualizacoes[0]->titulo) ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info"
                                                    data-ids="<?= $pedido->id ?>"
                                                    onclick="buttonClick($(this))" title="Ver mais">
                                                <i class="fas fa-eye mr-1 mr-1"></i>Ver mais
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $reCAPTCHA_site_key ?>"></script>
<script type="text/javascript">
    $('#form-rastrear').submit(function (e) {
        $('body').addClass('overlay');
        if ($('#token').val() == '') {
            e.preventDefault();

            grecaptcha.ready(function () {
                grecaptcha.execute('<?php echo $reCAPTCHA_site_key ?>', {action: 'submit'}).then(function (token) {
                    $('#token').val(token);
                    $('#form-rastrear').submit();
                });
            });
        }
        $('body').removeClass('overlay');
    });

    function buttonClick(button) {
        $('body').addClass('overlay');
        var id = button.data('ids');

        const request = axios.get('<?= Router::url(['controller' => 'Pedidos', 'action' => 'atualizacoes']) ?>', {
            params: {
                pedido_id: id,
            },
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            }
        });
        request
            .then(function (response) {
                // Atualizações encontradas
                const atualizacoes = response.data.results;

                // Remove a table de pedidos
                const divItens = $('div.corpo');
                divItens.find('table').remove();

                // Constrói uma nova table com as atualizações
                const table = $('<table>').addClass('table table-sm table-striped');

                //table thead
                const thead = $('<thead>');
                const trThead = $('<tr>');
                const thData = $('<th>').text('Data');
                const thTitulo = $('<th>').text('Título');
                const thDescricao = $('<th>').text('Descrição');

                trThead
                    .append(thData)
                    .append(thTitulo)
                    .append(thDescricao);

                thead.append(trThead);

                //table tbody
                const tbody = $('<tbody>');

                $.each(atualizacoes, function (idx, atualizacao) {
                    let trTbody = $('<tr>');
                    let tdData = $('<td>').text(new Date(atualizacao.data).toLocaleString());
                    let tdTitulo = $('<td>').text(atualizacao.titulo);
                    let tdDescricao = $('<td>').text(atualizacao.descricao);

                    trTbody
                        .append(tdData)
                        .append(tdTitulo)
                        .append(tdDescricao);

                    tbody.append(trTbody);
                });

                table
                    .append(thead)
                    .append(tbody);

                // Adiciona a nova table no código
                divItens.find('.table-responsive').append(table);
                divItens.find('h3').text('Rastreamento detalhado do pedido nº ' + id);

                $('body').removeClass('overlay');
            })
            .catch(function (reason) {
                $('body').removeClass('overlay');
                console.log(reason);
            });
    }
</script>
