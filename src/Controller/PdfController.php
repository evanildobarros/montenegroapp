<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * PdfController
 *
 * @property \App\Model\Table\PedidosTable $Pedidos
 */
class PdfController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadModel('Pedidos');
        $this->viewBuilder()->setLayout('pdf/default');
    }

    /**
     * etiqueta method
     *
     * @param string|int $pedido_id Id do pedido
     * @return \Cake\Http\Response|null|void
     */
    public function etiqueta($pedido_id)
    {
        $pedido = $this->Pedidos->get($pedido_id, [
            'contain' => [
                'Objetos' => [
                    'EnderecoEntregas' => [
                        'Cidades' => [
                            'Estados',
                        ],
                    ],
                    'EnderecoColetas' => [
                        'Cidades' => [
                            'joinType' => 'LEFT',
                            'Estados' => [
                                'joinType' => 'LEFT',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->layout = 'etiqueta';

        $this->set(compact('pedido'));
        $this->set('_serialize', ['pedido']);
    }
}
