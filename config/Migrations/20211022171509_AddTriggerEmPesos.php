<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTriggerEmPesos extends AbstractMigration
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
        $this->query('
            CREATE FUNCTION intervaloJaExiste(inicioIntervalo INT, fimIntervalo INT, tabelaPrecoId INT) RETURNS BOOL
            BEGIN
                DECLARE retorno BOOL;
                SELECT COUNT(*) > 0
                INTO retorno
                FROM pesos
                WHERE (peso_minimo BETWEEN inicioIntervalo AND fimIntervalo
                    OR peso_maximo BETWEEN inicioIntervalo AND fimIntervalo
                    OR inicioIntervalo BETWEEN peso_minimo AND peso_maximo)
                  AND tabelaPrecoId = tabela_preco_id;

                RETURN retorno;
            END;
        ');

        $this->query('
            CREATE TRIGGER tg_validar_intervalo_before_insert
                BEFORE INSERT
                ON pesos
                FOR EACH ROW
                    BEGIN
                        IF (intervaloJaExiste(NEW.peso_minimo, NEW.peso_maximo, NEW.tabela_preco_id)) THEN
                            SIGNAL SQLSTATE \'10000\' SET MESSAGE_TEXT = \'Erro ao inserir intervalo!\';
                        END IF;
            END;
        ');
    }

    public function down()
    {
        $this->query('
            DROP TRIGGER tg_validar_intervalo_before_insert;
            DROP FUNCTION intervaloJaExiste;
        ');
    }
}
