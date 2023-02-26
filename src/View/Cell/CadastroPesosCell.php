<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\Database\Expression\QueryExpression;
use Cake\View\Cell;

/**
 * CadastroHorariosPrestadores cell
 *
 * @property \App\Model\Table\PesosTable $Pesos
 */
class CadastroPesosCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadModel('Pesos');
    }

    /**
     * Default display method.
     *
     * @param null $tabelaPreco ID da tabela de preço
     * @return void
     */
    public function display($tabelaPreco = null)
    {
        $pesos = [];
        /** @var \App\Model\Entity\TabelaPreco $tabelaPreco */
        if (empty($tabelaPreco)) {
            $pesos = $this->Pesos->newEmptyEntity();
        } else {
            if (empty($tabelaPreco->pesos)) {
                $pesos = $this->Pesos
                    ->find()
                    ->contain([
                        'Taxas' => [
                            'Zonas',
                        ],
                    ])
                    ->where(function (QueryExpression $exp) use ($tabelaPreco) {
                        return $exp->eq('Pesos.tabela_preco_id', $tabelaPreco->id);
                    })
                    ->toArray();
            } else {
                $pesos = $tabelaPreco->pesos;
            }
        }

        $this->set(compact('pesos'));
    }
}
