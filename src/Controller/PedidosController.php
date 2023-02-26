<?php
declare(strict_types=1);

namespace App\Controller;

use App\Hashids\Hashids;
use App\Model\Entity\Pedido;
use App\Model\Table\PedidosTable;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Psr\Log\LogLevel;

/**
 * PedidosController
 *
 * @property \App\Model\Table\PedidosTable $Pedidos
 */
class PedidosController extends AppController
{
    /**
     * Rastrear method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function rastrear()
    {
        $pedidos = [];
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $data = $this->getRequest()->getData();

            try {
                $client = new Client();
                $response = $client->post(Configure::read('ReCaptcha.siteverify'), [
                    'secret' => Configure::read('ReCaptcha.secret_key'),
                    'response' => $data['token'],
                    'remoteip' => $this->getRequest()->clientIp(),
                ]);
                $resposta = json_decode($response->getBody()->getContents(), true);

                if ($resposta['success']) {
                    $pedidos = [];
                    $this->Pedidos
                        ->find()
                        ->contain([
                            'Pessoas',
                        ])
                        ->where(function (QueryExpression $expression) use ($data) {
                            $documento = str_replace(['.', '-', '/'], '', $data['documento']);
                            $orDocumento = $expression->or(function (QueryExpression $orExpression) use ($documento) {
                                return $orExpression
                                    ->eq('Pessoas.cpf', $documento)
                                    ->eq('Pessoas.cnpj', $documento);
                            });

                            if (ctype_digit($data['parametro'])) { //contém somente números?
                                $expression->eq('Pedidos.id', $data['parametro']);
                            } else {
                                $codigoDecode = Hashids::getInstance()->decode(strtoupper($data['parametro']));
                                $expression->eq('Pedidos.id', $codigoDecode[0]);
                            }

                            $expression->add($orDocumento);

                            return $expression;
                        })
                        ->each(function (Pedido $pedido) use (&$pedidos) {
                            $dataBase = new FrozenDate();
                            $dataAtual = new FrozenDate();

                            if (in_array($pedido->status, [PedidosTable::CANCELADO, PedidosTable::FINALIZADO])) {
                                $dataBase = $pedido->created->addMonth(2);
                            }

                            if ($dataBase->format('Y-m-d') >= $dataAtual->format('Y-m-d')) {
                                $ultimaAtualizacao = $this->Pedidos->Atualizacoes
                                    ->find()
                                    ->where(['pedido_id' => $pedido->id])
                                    ->orderDesc('data')
                                    ->first();

                                if (!empty($ultimaAtualizacao)) {
                                    $pedido->atualizacoes = [$ultimaAtualizacao];
                                    $pedidos[] = $pedido;
                                }
                            }
                        });
                } else {
                    $this->Flash->error(__('Houve um erro ao validar o reCAPTCHA! Por favor, tente novamente.'));
                }
            } catch (\Exception $e) {
                $this->log(
                    'Erro ao rastrear pedido pelo parâmetro informado: ' . json_encode($data),
                    LogLevel::ERROR,
                    ['scope' => ['rastreios']],
                );
                $this->log($e->getMessage(), LogLevel::ERROR, ['scope' => ['rastreios']]);
                $this->Flash->error(__('Erro ao rastrear pedido! Por favor tente novamente.'));
            }
        }

        $this->set(compact('pedidos'));
    }

    /**
     * atualizacoes method
     *
     * @return void|null
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function atualizacoes()
    {
        if ($this->getRequest()->is('ajax')) {
            $pedido_id = $this->getRequest()->getQuery('pedido_id');

            $results = $this->Pedidos->Atualizacoes
                ->find()
                ->where(function (QueryExpression $expression) use ($pedido_id) {
                    $expression
                        ->eq('Atualizacoes.pedido_id', $pedido_id);

                    return $expression;
                })
                ->orderDesc('Atualizacoes.data')
                ->toArray();

            $this->set(compact('results'));
            $this->set('_serialize', ['results']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
