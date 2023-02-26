<?php
declare(strict_types=1);

namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Database\Expression\QueryExpression;
use Correios\Model\Entity\LogBairro;

/**
 * BairrosShell command.
 *
 * @property \Correios\Model\Table\LogLocalidadeTable $LogLocalidade
 * @property \App\Model\Table\ZonasTable $Zonas
 */
class BairrosShell extends Shell
{
    /**
     * Initializes the Shell
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadModel('Correios.LogLocalidade');
        $this->loadModel('Zonas');
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $parser->addOption('estado', [
            'short' => 'e',
            'help' => 'ID IBGE do estado',
            'required' => true,
        ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->out('Início');
        $conn = $this->Zonas->getConnection();
        try {
            $conn->begin();

            /** @var \App\Model\Entity\Estado $estado */
            $estado = $this->Zonas->Cidades->Estados
                ->find()
                ->contain('Cidades')
                ->where(function (QueryExpression $expression) {
                    $expression->eq('Estados.ibge', $this->param('estado'));

                    return $expression;
                })
                ->firstOrFail();

            $this->info("Estado: {$estado->nome}");

            foreach ($estado->cidades as $cidade) {
                /** @var \App\Model\Entity\Cidade $cidade */
                $this->info("Cidade: {$cidade->nome}");

                $b = $this->LogLocalidade->LogBairro
                    ->find()
                    ->contain([
                        'LogFaixaBairro',
                        'LogLocalidade' => [
                            'LogFaixaLocalidade',
                        ],
                    ])
                    ->where(function (QueryExpression $expression) use ($cidade) {
                        $expression->eq('LogLocalidade.mun_nu', $cidade->ibge);

                        return $expression;
                    })
                    ->each(function (LogBairro $logBairro) use ($cidade) {
                        $this->out("Bairro: {$logBairro->bai_no}");

                        $bairro = $this->Zonas->findOrCreate([
                            'nome' => $logBairro->bai_no,
                            'cidade_id' => $cidade->id,
                        ]);
                        $bairro->nome_abreviado = $logBairro->bai_no_abrev;

                        $bairro = $this->Zonas->saveOrFail($bairro);
                        if (!empty($logBairro->log_faixa_bairro)) {
                            foreach ($logBairro->log_faixa_bairro as $logFaixaBairro) {
                                $this->out("Faixa: {$logFaixaBairro->fcb_cep_ini} - {$logFaixaBairro->fcb_cep_fim}");
                                $faixa = $this->Zonas->Faixas->newEmptyEntity();
                                $faixa->zona_id = $bairro->id;
                                $faixa->inicio = $logFaixaBairro->fcb_cep_ini;
                                $faixa->fim = $logFaixaBairro->fcb_cep_fim;

                                $this->Zonas->Faixas->save($faixa);
                            }
                        }
                    });
            }

            $conn->commit();
            $this->success("Finalizado.");
        } catch (\Exception $e) {
            $conn->rollback();
            $this->err($e->getMessage());
        }
    }
}
