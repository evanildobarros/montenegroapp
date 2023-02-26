<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\NotFoundException;
use Canducci\Cep\CepRequest;
use JansenFelipe\Utils\Utils;

/**
 * Enderecos Controller
 *
 * @property \App\Model\Table\CidadesTable $Cidades
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Cidade[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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
        $this->loadModel('Cidades');
        parent::initialize();
    }

    /**
     * Cep method
     * Busca os dados do endereço a partir do CEP informado
     *
     * @return void
     * @throws \Exception
     */
    public function cep()
    {
        $cep = new \Canducci\Cep\Cep(new CepRequest());
        $endereco = $cep->find(Utils::unmask($this->getRequest()->getQuery('cep')));
        $endereco = $endereco->getCepModel();

        if (empty($endereco)) {
            throw new NotFoundException('CEP não encontrado!');
        }

        $cidade = $this->Cidades
            ->find()
            ->where(function (QueryExpression $expression) use ($endereco) {
                $expression->eq('Cidades.ibge', $endereco->getIbge());

                return $expression;
            })
            ->first();

        $endereco->cidade_id = $cidade->id;
        $endereco->estado_id = $cidade->estado_id;

        $this->set(compact('endereco'));
        $this->viewBuilder()->setOption('serialize', ['endereco' => 'endereco']);
    }

    /**
     * Cidades method
     * Busca as cidades conforme os parametros informados
     *
     * @return void
     */
    public function cidades()
    {
        $this->getRequest()->allowMethod('ajax');
        $cidade_id = $this->getRequest()->getQuery('cidade_id');
        $param = $this->getRequest()->getQuery('cidade');

        $cidades = $this->Cidades
            ->listaCidades()
            ->where(function (QueryExpression $expression) use ($cidade_id, $param) {
                if (!empty($cidade_id)) {
                    $expression->eq('Cidades.id', $cidade_id);
                } else {
                    $expression->like('Cidades.nome', '%' . $param . '%');
                }

                return $expression;
            })
            ->limit(20);

        $this->set(compact('cidades'));
        $this->viewBuilder()->setOption('serialize', ['results' => 'cidades']);
    }

    /**
     * CidadesPorEstado method
     * Busca as cidades conforme o estado informado
     *
     * @return void
     */
    public function cidadesPorEstado()
    {
        $this->getRequest()->allowMethod('ajax');
        $estado_id = $this->getRequest()->getQuery('estado_id');

        $cidades = $this->Cidades
            ->listaCidades()
            ->where(function (QueryExpression $expression) use ($estado_id) {

                $expression->eq('Cidades.estado_id', $estado_id);

                return $expression;
            });

        $this->set(compact('cidades'));
        $this->viewBuilder()->setOption('serialize', ['results' => 'cidades']);
    }
}
