<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\NotFoundException;
use Canducci\Cep\CepRequest;
use JansenFelipe\Utils\Utils;

/**
 * Class EnderecosController
 *
 * @property \App\Model\Table\EnderecosTable $Enderecos
 */
class EnderecosController extends AppController
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
     * Cep method
     * Busca o endereço pelo CEP informado
     *
     * @param string $cep CEP
     * @return void
     * @throws \Exception
     */
    public function cep(string $cep)
    {
        $this->getRequest()->allowMethod('get');
        $cepRequest = new \Canducci\Cep\Cep(new CepRequest());

        $cepResponse = $cepRequest->find(Utils::unmask($cep));
        $endereco = $cepResponse->getCepModel();

        if (!$cepResponse->isOk()) {
            throw new NotFoundException('CEP não encontrado!');
        }

        $cidade = $this->Enderecos->Cidades
            ->find()
            ->contain([
                'Estados',
            ])
            ->where(function (QueryExpression $expression) use ($endereco) {
                $expression->eq('Cidades.ibge', $endereco->getIbge());

                return $expression;
            })
            ->first();

        $endereco->cidade = $cidade;

        $success = true;
        $this->set(compact('success', 'endereco'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'endereco']);
    }
}
