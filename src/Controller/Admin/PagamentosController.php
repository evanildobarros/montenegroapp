<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Database\Expression\QueryExpression;

/**
 * Pagamentos Controller
 *
 * @property \App\Model\Table\PagamentosTable $Pagamentos
 * @method \App\Model\Entity\Pedido[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PagamentosController extends AppController
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
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->getRequest()->is('post');
        $conn = $this->Pagamentos->getConnection();

        try {
            $conn->begin();
            $data = $this->getRequest()->getData();

            $pedido = $this->Pagamentos->Pedidos->get($data['pedido_id']);

            /** @var \App\Model\Entity\Pagamento $pagamento */
            $pagamento = $this->Pagamentos
                ->find()
                ->contain([
                    'Pedidos' => [
                        'Pessoas',
                    ],
                ])
                ->where(function (QueryExpression $expression) use ($pedido) {
                    $expression
                        ->eq('Pagamentos.pedido_id', $pedido->id);

                    return $expression;
                })
                ->first();

            $novoPagamento = $this->Pagamentos->newEntity([
                'pedido_id' => $pedido->id,
                'status' => $data['status'],
                'comentario' => 'Manual',
            ]);

            if (!empty($pagamento)) {
                $novoPagamento->transaction_code = $pagamento->transaction_code;
                $novoPagamento->valor = $pagamento->valor;
            }

            $this->Pagamentos->saveOrFail($novoPagamento);

            $conn->commit();
            $this->Flash->success(__('O histórico do pagamento foi salvo com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O histórico do pagamento não pode ser salvo. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
    }
}
