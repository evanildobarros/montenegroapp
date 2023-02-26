<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Table\PessoasTable;
use Authentication\IdentityInterface as AuthenticationIdentity;
use Authorization\AuthorizationServiceInterface;
use Authorization\IdentityInterface as AuthorizationIdentity;
use Authorization\Policy\ResultInterface;
use Cake\ORM\Entity;

/**
 * Pessoa Entity
 *
 * @property int $id
 * @property string $model
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property string $senha_confirm
 * @property string|null $token
 * @property string|null $token_ativacao
 * @property \Cake\I18n\FrozenDate|null $data_nascimento
 * @property string|null $cpf
 * @property string|null $cnpj
 * @property string|null $telefone
 * @property string $celular
 * @property int|null $endereco_id
 * @property string|null $nome_representante
 * @property string|null $celular_representante
 * @property string|null $email_representante
 * @property string $status
 * @property string $status_formatado
 * @property int|null $quantidade_entregas
 * @property string|null $valor_fixo_pedidos
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $tipo
 *
 * @property \App\Model\Entity\Endereco $endereco
 * @property \App\Model\Entity\Rota[] $rotas
 * @property \App\Model\Entity\Pedido[] $pedidos
 * @property \App\Model\Entity\Notificacao[] $notificacaos
 * @property \App\Model\Entity\Dispositivo[] $dispositivos
 */
class Pessoa extends Entity implements AuthenticationIdentity, AuthorizationIdentity
{
    /**
     * @var \Authorization\AuthorizationServiceInterface
     */
    private $authorization;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'model' => true,
        'nome' => true,
        'email' => true,
        'senha' => true,
        'senha_confirm' => true,
        'token' => true,
        'token_ativacao' => true,
        'data_nascimento' => true,
        'cpf' => true,
        'cnpj' => true,
        'telefone' => true,
        'celular' => true,
        'endereco_id' => true,
        'nome_representante' => true,
        'celular_representante' => true,
        'email_representante' => true,
        'status' => true,
        'quantidade_entregas' => true,
        'valor_fixo_pedidos' => true,
        'created' => true,
        'modified' => true,
        'tipo' => true,
        'endereco' => true,
        'rotas' => true,
        'pedidos' => true,
        'notificacoes' => true,
        'dispositivos' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token',
        'token_ativacao',
        'senha',
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'status_formatado',
    ];

    /**
     * @return string|null
     */
    protected function _getStatusFormatado(): ?string
    {
        $tmp = $this->status;
        $status = PessoasTable::STATUS;

        if (isset($status[$tmp])) {
            return $status[$tmp];
        }

        return $tmp;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalData()
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function can($action, $resource): bool
    {
        return $this->authorization->can($this, $action, $resource);
    }

    /**
     * @inheritDoc
     */
    public function canResult($action, $resource): ResultInterface
    {
        return $this->authorization->canResult($this, $action, $resource);
    }

    /**
     * @inheritDoc
     */
    public function applyScope($action, $resource)
    {
        return $this->authorization->applyScope($this, $action, $resource);
    }

    /**
     * Setter to be used by the middleware.
     *
     * @param \Authorization\AuthorizationServiceInterface $service AuthorizationService
     * @return $this
     */
    public function setAuthorization(AuthorizationServiceInterface $service)
    {
        $this->authorization = $service;

        return $this;
    }
}
