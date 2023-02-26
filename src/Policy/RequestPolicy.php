<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\PessoasTable;
use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Authorization\Policy\Result;
use Cake\Http\ServerRequest;

class RequestPolicy implements RequestPolicyInterface
{
    /**
     * Method to check if the request can be accessed
     *
     * @param \Authorization\IdentityInterface|null $identity Identity
     * @param \Cake\Http\ServerRequest $request Server Request
     * @return \Authorization\Policy\Result
     */
    public function canAccess(?IdentityInterface $identity, ServerRequest $request): Result
    {
        if ($request->getParam('prefix') === 'Api') {
            if (!empty($identity)) {
                $acessosTotais = [
                    'Pessoas/login',
                    'Pessoas/logout',
                    'Pessoas/esqueceusenha',
                    'Pessoas/edit',
                    'Pessoas/info',
                    'Pessoas/tipos',
                    'Pessoas/firebase',
                    'Enderecos/cep',
                    'Atualizacoes/rastrear',
                    'Notificacoes/info',
                    'Configs/linkAjuda',
                    'Configs/emailSuporte',
                    'Configs/sobre',
                    'Configs/mensagemDashboard',
                    'Configs/mensagemAvisoPedidos'
                ];

                $controller = $request->getParam('controller');
                $action = $request->getParam('action');
                $rota = "{$controller}/{$action}";

                switch ($identity->model) {
                    case PessoasTable::CLIENTE:
                        $acessosClientes = [
                            'Pessoas/reenviar',
                            'Pessoas/add',
                            'Pedidos/status',
                            'Pedidos/add',
                            'Pedidos/view',
                            'Pedidos/index',
                            'Pedidos/prazoEnvio',
                            'Pedidos/unidadesPesos',
                            'Pedidos/unidadesComprimento',
                            'Pedidos/modalidadesDistribuicao',
                            'Pedidos/classificar',
                            'Pedidos/recusado',
                            'Filiais/index',
                            'EntregaMeios/disponiveis',
                            'EntregaMeios/limites',
                            'EntregaMeios/meios',
                            'Configs/modalidadeDistribuicao',
                            'Configs/meioEntrega',
                            'Configs/meioColeta',
                            'Configs/filiais',
                            'Cartoes/getBin',
                        ];

                        $acessosPermitidos = array_merge($acessosTotais, $acessosClientes);

                        if (!in_array($rota, $acessosPermitidos)) {
                            return new Result(false, 'Permissão negada!');
                        }

                        break;
                    case PessoasTable::ENTREGADOR:
                        $acessosEntregadores = [
                            'Motivos/index',
                            'Rotas/index',
                            'Rotas/iniciar',
                            'Rotas/finalizar',
                            'RotaPedidos/entregar',
                            'RotaPedidos/view',
                            'Tentativas/add',
                        ];

                        $acessosPermitidos = array_merge($acessosTotais, $acessosEntregadores);

                        if (!in_array($rota, $acessosPermitidos)) {
                            return new Result(false, 'Permissão negada!');
                        }

                        break;
                    default:
                        return new Result(false, 'Permissão negada!');
                }
            }
        }

        return new Result(true);
    }
}
