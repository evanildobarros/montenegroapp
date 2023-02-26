<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Inicial extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('atualizacaoes')
            ->addColumn('pedido_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('cidades')
            ->addColumn('estado_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('ibge', 'char', [
                'default' => null,
                'limit' => 7,
                'null' => false,
            ])
            ->addColumn('latitude', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 10,
                'scale' => 8,
            ])
            ->addColumn('longitude', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 11,
                'scale' => 8,
            ])
            ->addColumn('população', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('configs')
            ->addColumn('parametro', 'string', [
                'comment' => 'Dias para o cliente deixar o produto no centro de distribuição
Quantidade de tentativas de entrega que o entregador pode realizar',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('valor', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('entrega_meios')
            ->addColumn('nome', 'string', [
                'comment' => 'Moto, Carro, Caminhão, etc;',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('peso_limite', 'decimal', [
                'comment' => 'Moto carrega até 30KG',
                'default' => null,
                'null' => false,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('entrega_modalidades')
            ->addColumn('nome', 'string', [
                'comment' => 'Porta a porta;
',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('entrega_tentativas')
            ->addColumn('pedido_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('entregador_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('motivo_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome_motivo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('observacoes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('estados')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('sigla', 'char', [
                'default' => null,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('populacao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('ibge', 'char', [
                'default' => null,
                'limit' => 2,
                'null' => false,
            ])
            ->create();

        $this->table('filiais')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('horario_atendimento', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('cep', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('logradouro', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('numero', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('bairro', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('complemento', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('referencia', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('observacoes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('groups')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('painel', 'string', [
                'comment' => 'admin => Admin',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('motivos')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('notificacoes')
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data_envio', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('remetente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('destinatario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('lida', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('objetos')
            ->addColumn('altura', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('peso', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('largura', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('profundidade', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('classificacao', 'string', [
                'comment' => 'Pequeno
Médio
Grande',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('data_postagem', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('nome_destinatario', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('telefone_destinatario', 'string', [
                'default' => null,
                'limit' => 65,
                'null' => false,
            ])
            ->addColumn('cep', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('logradouro', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('numero', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('bairro', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('complemento', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('referencia', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('observacoes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('pagamentos')
            ->addColumn('pedido_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('transaction_code', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('comentario', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('modified', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('pedidos')
            ->addColumn('cliente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('objeto_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('filial_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('entrega_modalidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('entrega_meio_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('codigo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('codigo_rastreio', 'string', [
                'comment' => 'Gerar um hash a partir do ID',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('observacoes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modalidade_entrega', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('modalidade_distribuicao', 'string', [
                'comment' => 'Coleta, Entrega (centro de distribuicao)',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('meio_entrega', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('valor_total', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 9,
                'scale' => 2,
            ])
            ->addColumn('status', 'string', [
                'comment' => 'novo, para entrega, entregue, cancelado
',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('token', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('bandeira', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('bin', 'string', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('ultimo_quatro_digitos', 'string', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('liberado_envio', 'boolean', [
                'comment' => 'Se filial_id for NULL, e pagamento aprovado marcar TRUE
Se filial_id tiver valor quer dizer que o cliente vai deixar o produto no centro
de distribuição, então deixar esse campo como FALSE e fazer uma tela
no painel de \"PEDIDOS ESPERANDO ENTREGA\", e o user vai manualmente
localizar o pedido e marcar ele como \"OBJETO RECEBIDO\",
nisso o sistema criar um registro em atualização e libera o pedido para
a tela de DEFINIR ROTAS',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('prazo_envio', 'date', [
                'comment' => 'Prazo máximo para o cliente deixar o produto
no centro de distribuição.

Só é preenchido quando FILIAL_ID tiver valor',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('previsao_entrega', 'date', [
                'comment' => 'Previsao para o objeto chegar ao destino',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('comprovante', 'string', [
                'comment' => 'Foto do documento assinado pela pessoa que
recebeu o pedido',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('nome_recebedor', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('documento_recebedor', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'codigo',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'codigo_rastreio',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('pessoas')
            ->addColumn('tipo', 'string', [
                'comment' => 'cliente
entregador',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('senha', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('token', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('data_nascimento', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('cpf', 'string', [
                'default' => null,
                'limit' => 14,
                'null' => true,
            ])
            ->addColumn('cnpj', 'string', [
                'default' => null,
                'limit' => 18,
                'null' => true,
            ])
            ->addColumn('telefone', 'string', [
                'default' => null,
                'limit' => 16,
                'null' => true,
            ])
            ->addColumn('celular', 'string', [
                'default' => null,
                'limit' => 16,
                'null' => false,
            ])
            ->addColumn('cep', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('logradouro', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('numero', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('complemento', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('referencia', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('nome_representante', 'string', [
                'comment' => 'Quando for PJ',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('celular_representante', 'string', [
                'comment' => 'Quando for PJ',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('email_representante', 'string', [
                'comment' => 'Quando for PJ',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('status', 'string', [
                'comment' => 'Entregador
Ativo
Inativo

----------
Cliente
Aguardando validar email
Ativo
Inativo',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('quantidade_entregas', 'integer', [
                'comment' => 'Campo para o entregador',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('valor_fixo_pedidos', 'decimal', [
                'comment' => 'Campo para cliente, se o cliente tiver contrato com eles, o cliente
pode ter um valor fixo por pedido',
                'default' => null,
                'null' => true,
                'precision' => 9,
                'scale' => 2,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('rota_pedidos')
            ->addColumn('pedido_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('rota_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('ordem', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('entregue', 'boolean', [
                'comment' => 'Sim ou não',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('rotas')
            ->addColumn('entregador_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data_saida', 'date', [
                'comment' => 'data que o entregador irá
sair para realizar as entregas desta rota',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('users')
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('username', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('token', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('atualizacaoes')
            ->addForeignKey(
                'pedido_id',
                'pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('cidades')
            ->addForeignKey(
                'estado_id',
                'estados',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('entrega_tentativas')
            ->addForeignKey(
                'pedido_id',
                'pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'motivo_id',
                'motivos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'entregador_id',
                'pessoas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('filiais')
            ->addForeignKey(
                'cidade_id',
                'cidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('objetos')
            ->addForeignKey(
                'cidade_id',
                'cidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('pagamentos')
            ->addForeignKey(
                'pedido_id',
                'pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('pedidos')
            ->addForeignKey(
                'objeto_id',
                'objetos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'filial_id',
                'filiais',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'cliente_id',
                'pessoas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'entrega_modalidade_id',
                'entrega_modalidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'entrega_meio_id',
                'entrega_meios',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('pessoas')
            ->addForeignKey(
                'cidade_id',
                'cidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('rota_pedidos')
            ->addForeignKey(
                'rota_id',
                'rotas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'pedido_id',
                'pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('rotas')
            ->addForeignKey(
                'entregador_id',
                'pessoas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $this->table('users')
            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('atualizacaoes')
            ->dropForeignKey(
                'pedido_id'
            )->save();

        $this->table('cidades')
            ->dropForeignKey(
                'estado_id'
            )->save();

        $this->table('entrega_tentativas')
            ->dropForeignKey(
                'pedido_id'
            )
            ->dropForeignKey(
                'motivo_id'
            )
            ->dropForeignKey(
                'entregador_id'
            )->save();

        $this->table('filiais')
            ->dropForeignKey(
                'cidade_id'
            )->save();

        $this->table('objetos')
            ->dropForeignKey(
                'cidade_id'
            )->save();

        $this->table('pagamentos')
            ->dropForeignKey(
                'pedido_id'
            )->save();

        $this->table('pedidos')
            ->dropForeignKey(
                'objeto_id'
            )
            ->dropForeignKey(
                'filial_id'
            )
            ->dropForeignKey(
                'cliente_id'
            )
            ->dropForeignKey(
                'entrega_modalidade_id'
            )
            ->dropForeignKey(
                'entrega_meio_id'
            )->save();

        $this->table('pessoas')
            ->dropForeignKey(
                'cidade_id'
            )->save();

        $this->table('rota_pedidos')
            ->dropForeignKey(
                'rota_id'
            )
            ->dropForeignKey(
                'pedido_id'
            )->save();

        $this->table('rotas')
            ->dropForeignKey(
                'entregador_id'
            )->save();

        $this->table('users')
            ->dropForeignKey(
                'group_id'
            )->save();

        $this->table('atualizacaoes')->drop()->save();
        $this->table('cidades')->drop()->save();
        $this->table('configs')->drop()->save();
        $this->table('entrega_meios')->drop()->save();
        $this->table('entrega_modalidades')->drop()->save();
        $this->table('entrega_tentativas')->drop()->save();
        $this->table('estados')->drop()->save();
        $this->table('filiais')->drop()->save();
        $this->table('groups')->drop()->save();
        $this->table('motivos')->drop()->save();
        $this->table('notificacoes')->drop()->save();
        $this->table('objetos')->drop()->save();
        $this->table('pagamentos')->drop()->save();
        $this->table('pedidos')->drop()->save();
        $this->table('pessoas')->drop()->save();
        $this->table('rota_pedidos')->drop()->save();
        $this->table('rotas')->drop()->save();
        $this->table('users')->drop()->save();
    }
}
