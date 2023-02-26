<?php

declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Class ConfigsController
 *
 * @property \App\Model\Table\ConfigsTable $Configs
 */
class ConfigsController extends AppController
{
    /**
     * Initialize controller
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['cep']);
    }
    /**
     * MensagemDashboard method
     *
     * @return void
     */

    public function mensagemDashboard()
    {
        $this->getRequest()->allowMethod('get');

        $mensagemAjuda = $this->Configs->parametro('mensagem_dashboard');

        $results = [
            'success' => true,
            'data' => $mensagemAjuda,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }


    /**
     * ModalidadeDistribuicao method
     *
     * @return void
     */
    public function modalidadeDistribuicao()
    {
        $this->getRequest()->allowMethod('get');

        $mensagemAjuda = $this->Configs->parametro('mensagem_modalidade_distribuicao');

        $results = [
            'success' => true,
            'data' => $mensagemAjuda,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }

    /**
     * MeioEntrega method
     *
     * @return void
     */
    public function meioEntrega()
    {
        $this->getRequest()->allowMethod('get');

        $mensagemAjuda = $this->Configs->parametro('mensagem_meio_entrega');

        $results = [
            'success' => true,
            'data' => $mensagemAjuda,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }
    /**
     * mensagemAvisoPedidos method
     *
     * @return void
     */

    public function mensagemAvisoPedidos()
    {
        $this->getRequest()->allowMethod('get');

        $mensagemAjuda = $this->Configs->parametro('mensagem_aviso_pedidos');

        $results = [
            'success' => true,
            'data' => $mensagemAjuda,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }


    /**
     * MeioColeta method
     *
     * @return void
     */
    public function meioColeta()
    {
        $this->getRequest()->allowMethod('get');

        $mensagemAjuda = $this->Configs->parametro('mensagem_meio_coleta');

        $results = [
            'success' => true,
            'data' => $mensagemAjuda,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }

    /**
     * MeioColeta method
     *
     * @return void
     */
    public function filiais()
    {
        $this->getRequest()->allowMethod('get');

        $mensagemAjuda = $this->Configs->parametro('mensagem_filiais');

        $results = [
            'success' => true,
            'data' => $mensagemAjuda,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }

    /**
     * ajuda method
     *
     * @return void
     */
    public function linkAjuda()
    {
        $this->getRequest()->allowMethod('get');

        $link = $this->Configs->parametro('link_ajuda');

        $results = [
            'success' => true,
            'data' => $link,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }

    /**
     * Retorna o e-mail de suporte do aplicativo
     *
     * @return void
     */
    public function emailSuporte()
    {
        $this->getRequest()->allowMethod('get');

        $link = $this->Configs->parametro('email_suporte');

        $results = [
            'success' => true,
            'data' => $link,
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }

    /**
     * Sobre method
     *
     * @return void
     */
    public function sobre()
    {
        $this->getRequest()->allowMethod('get');

        $results = [
            'success' => true,
            'data' => [
                'telefone' => $this->Configs->parametro('telefone_sobre'),
                'email' => $this->Configs->parametro('email_sobre'),

            ],
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }
}
