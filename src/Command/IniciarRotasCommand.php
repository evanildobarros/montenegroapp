<?php
declare(strict_types=1);

namespace App\Command;

use App\Model\Entity\Rota;
use App\Model\Table\RotasTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenDate;

/**
 * AtrasarRota sCommandRodar este Command uma vez por dia, no começo do dia
 *
 * @property \App\Model\Table\RotasTable $Rotas
 * @property \App\Model\Table\ConfigsTable $Configs
 */
class IniciarRotasCommand extends Command
{
    protected $modelClass = 'Rotas';

    /**
     * Comando para mudar o status das Rotas em "Aguardando início" para "Em rota"
     * Rodar este Command uma vez por dia, no começo do dia
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $inicio = new \DateTime();
        $io->out('Início IniciarRotasCommand: ' . $inicio->format('d/m/Y H:i:s'));

        $conn = $this->Rotas->getConnection();
        try {
            $this->loadModel('Configs');
            $iniciarAutomatico = (bool)$this->Configs->parametro('rota_iniciar_automatico');
            $io->out("Iniciar rotas automaticamente: {$iniciarAutomatico}");

            $ids = [];
            if ($iniciarAutomatico) {
                $conn->begin();
                $io->out('Buscando rotas do dia ' . $inicio->format('d/m/Y') . ' para inicia-las');

                $this->Rotas
                    ->find()
                    ->where(function (QueryExpression $expression) {
                        $expression
                            ->eq('Rotas.status', RotasTable::AGUARDANDO_INICIO)
                            ->eq('Rotas.data_saida', new FrozenDate());

                        return $expression;
                    })
                    ->each(function (Rota $rota) use ($io, &$ids) {
                        $io->out('Rota: ' . json_encode($rota));
                        $io->out('Trocando status da Rota ID: ' . $rota->id);
                        $io->out("Status antigo: {$rota->status} | Status novo: " . RotasTable::EM_ROTA);

                        $rota->status = RotasTable::EM_ROTA;
                        $this->Rotas->saveOrFail($rota);

                        $io->out('Rota alterada com sucesso! Rota: ' . json_encode($rota));
                        $ids[] = $rota->id;
                    });

                $conn->commit();
            }

            $fim = new \DateTime();
            $intervalo = $fim->getTimestamp() - $inicio->getTimestamp();
            $io->out('Fim IniciarRotasCommand: ' . $fim->format('d/m/Y H:i:s'));
            $io->success(count($ids) . " Rotas iniciadas com sucesso em {$intervalo} segundos.");
        } catch (\Exception $e) {
            $conn->rollback();
            $io->err('Erro ao iniciar rotas: ' . $e->getMessage());
            $this->abort();
        }

        return null;
    }
}
