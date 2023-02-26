<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\PessoasTable;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Client;

/**
 * PessoasController
 *
 * @property \App\Model\Table\PessoasTable $Pessoas
 */
class PessoasController extends AppController
{
    /**
     * Ativa a pessoa
     *
     * @param string|null $tokenAtivacao Token enviado no email do pessoa
     * @return void
     */
    public function ativar(?string $tokenAtivacao = null): void
    {
        $pessoa = $this->Pessoas
            ->find()
            ->where(function (QueryExpression $expression) use ($tokenAtivacao) {
                return $expression->eq('Pessoas.token_ativacao', $tokenAtivacao);
            })
            ->firstOrFail();

        $pessoa = $this->Pessoas->patchEntity($pessoa, [
            'token_ativacao' => null,
            'status' => PessoasTable::ATIVO,
        ]);

        $pessoa = $this->Pessoas->saveOrFail($pessoa);

        $this->set(compact('pessoa'));
        $this->set('_serialize', 'pessoa');
    }

    /**
     * Redefine a senha
     *
     * @param string|null $token Token enviado no email do pessoa
     * @return \Cake\Http\Response|null|void
     */
    public function redefinir(?string $token = null)
    {
        $pessoa = $this->Pessoas
            ->find()
            ->where(function (QueryExpression $expression) use ($token) {
                return $expression->eq('Pessoas.token', $token);
            })
            ->firstOrFail();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $conn = $this->Pessoas->getConnection();
            $data = $this->request->getData();

            try {
                $client = new Client();
                $response = $client->post(Configure::read('ReCaptcha.siteverify'), [
                    'secret' => Configure::read('ReCaptcha.secret_key'),
                    'response' => $data['token'],
                    'remoteip' => $this->getRequest()->clientIp(),
                ]);
                $resposta = json_decode($response->getBody()->getContents(), true);

                if ($resposta['success']) {
                    $conn->begin();
                    $pessoa = $this->Pessoas->patchEntity($pessoa, $data);
                    $pessoa->token = null;
                    $this->Pessoas->saveOrFail($pessoa);
                    $conn->commit();

                    return $this->redirect(['action' => 'senhaAlterada']);
                } else {
                    $this->Flash->error(__('Houve um erro ao validar o reCAPTCHA! Por favor, tente novamente.'));
                }
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage(), 'error');
                $this->Flash->error(__('Não foi possível alterar sua senha. Por favor, tente novamente.'));
            }
        }

        $this->set(compact('pessoa'));
        $this->set('_serialize', 'pessoa');
    }

    /**
     * senhaAlterada method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function senhaAlterada()
    {
    }
}
