<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFuncaoTemRota extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function up()
    {
        $this->query("
            CREATE FUNCTION temRotaAtiva(id_pedido INT, tipo_parada TINYTEXT) RETURNS BOOLEAN

            BEGIN
                DECLARE resultado BOOLEAN;

                SELECT CASE WHEN (count(*)) > 0 THEN TRUE ELSE FALSE END
                INTO resultado
                FROM rota_pedidos
                INNER JOIN rotas on rota_pedidos.rota_id = rotas.id
                WHERE rota_pedidos.pedido_id = id_pedido
                AND rotas.status != 'finalizado'
                AND rota_pedidos.tipo = tipo_parada;
                RETURN resultado;
            END;
        ");
    }

    public function down()
    {
        $this->query("DROP FUNCTION temRotaAtiva");
    }
}
