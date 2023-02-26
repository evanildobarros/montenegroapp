<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Peso[] $pesos
 * @var \App\Model\Entity\Zona[] $zonas
 */
?>
<div class="table-responsive">
    <table class="table table-sm table-striped table-hover td-center" id="table-pesos">
        <thead>
        <tr>
            <th>Faixa de peso (em gramas)</th>
        </tr>
        </thead>
        <tbody id="pesos"></tbody>
        <tfoot>
        <tr>
            <td colspan="3">
                <button class="btn btn-success" type="button" id="novoPeso">
                    Nova faixa
                </button>
                <button class="btn btn-success" type="button" id="novoQuilo">
                    Novo quilo/tempo adicional
                </button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    $(document).ready(function () {
        let idx = 0;

        function novoPeso(peso) {
            const itens = $('#pesos');

            const tr = $('<tr>').attr({
                'id': 'tr-' + idx,
                'data-id': idx,
                'data-taxa': '0',
                'data-quilo': peso.quilo_adicional,
            });

            const inputId = $('<input>').attr({
                type: 'hidden',
                name: 'pesos[' + idx + '][id]',
                id: 'pesos-' + idx + '-id',
                value: peso.id
            });
            const inputPesoMinimo = $('<input>').attr({
                type: (peso.quilo_adicional ? 'hidden' : 'number'),
                'data-inputmask-alias': 'integer',
                name: 'pesos[' + idx + '][peso_minimo]',
                placeholder: 'Peso mínimo',
                required: peso.quilo_adicional,
                id: 'pesos-' + idx + '-peso-minimo',
                class: 'form-control',
                value: peso.peso_minimo,
            });
            const inputPesoMaximo = $('<input>').attr({
                type: (peso.quilo_adicional ? 'hidden' : 'number'),
                'data-inputmask-alias': 'integer',
                name: 'pesos[' + idx + '][peso_maximo]',
                placeholder: 'Peso máximo',
                required: peso.quilo_adicional,
                id: 'pesos-' + idx + '-peso-maximo',
                class: 'form-control',
                value: peso.peso_maximo,
            });
            const inputQuiloAdicional = $('<input>').attr({
                type: 'hidden',
                name: 'pesos[' + idx + '][quilo_adicional]',
                id: 'pesos-' + idx + '-quilo_adicional',
                value: peso.quilo_adicional,
            });
            const inputRemover = $('<button>').attr({
                type: 'button',
                class: 'btn btn-danger',
                onclick: 'remover(' + idx + ')',
            }).text('Remover');

            const tdPeso = $('<td>')
                .append(inputId)
                .append(inputQuiloAdicional)
                .append(inputPesoMinimo)
                .append(inputPesoMaximo);

            if (peso.quilo_adicional) {
                tdPeso.append('Quilo/Tempo Adicional');
                $('#novoQuilo').attr('disabled', true);
            }

            const tdAcoes = $('<td>').append(inputRemover);

            tr.append(tdPeso);
            novaColuna(peso.taxas, tr);

            tr.append(tdAcoes);

            itens.append(tr);

            idx++;
            initInputMask();
            verificaFaixas();
        }

        function novaTaxa(taxa, tr = null) {
            let idxPeso = 0;
            let idxTaxa = idx;
            if (tr != null && tr.length > 0) {
                idxPeso = tr.data('id');
                idxTaxa = tr.data('taxa');
            }

            let inputId = $('<input>').attr({
                type: 'hidden',
                name: 'pesos[' + idxPeso + '][taxas][' + idxTaxa + '][id]',
                required: true,
                id: 'taxas-' + idxTaxa + '-id',
                class: 'form-control',
                value: taxa.id,
            });
            // let inputZonaId = $('<input>').attr({
            //     type: 'hidden',
            //     name: 'pesos[' + idxPeso + '][taxas][' + idxTaxa + '][zona_id]',
            //     required: true,
            //     id: 'taxas-' + idxTaxa + '-zona-id',
            //     class: 'form-control',
            //     value: taxa.zona_id,
            // });
            let valor = taxa.valor;
            if (valor != null) {
                valor = taxa.valor.replace('.', ',');
            }
            let inputValor = $('<input>').attr({
                type: 'monetary',
                'data-inputmask-alias': 'currency',
                name: 'pesos[' + idxPeso + '][taxas][' + idxTaxa + '][valor]',
                placeholder: 'Valor em R$',
                required: true,
                id: 'taxas-' + idxTaxa + '-valor',
                class: 'form-control',
                value: valor,
            });
            let inputTempo = $('<input>').attr({
                type: 'number',
                'data-inputmask-alias': 'integer',
                name: 'pesos[' + idxPeso + '][taxas][' + idxTaxa + '][tempo_estimado]',
                placeholder: 'Tempo estimado em dias',
                required: true,
                id: 'taxas-' + idxTaxa + '-tempo_estimado',
                class: 'form-control',
                value: taxa.tempo_estimado,
            });

            let tdTaxas = $('<td>')
                .attr('class', 'td-zona-' + taxa.zona_id)
                .append(inputId)
                //.append(inputZonaId)
                .append(inputValor)
                .append(inputTempo);

            // let thZona;
            // if ($('#th-zona-' + taxa.zona_id).length > 0) {
            //     thZona = $('#th-zona-' + taxa.zona_id);
            // } else {
            //     thZona = $('<th>')
            //         .attr('id', 'th-zona-' + taxa.zona_id)
            //         .text($('#zonas-ids option[value="' + taxa.zona_id + '"]').text());
            // }

            idxTaxa++;
            tr.data('taxa', idxTaxa);
            tr.attr('data-taxa', idxTaxa);

            //$('thead').find('tr').append(thZona);
            return tdTaxas;
        }

        function novaColuna(taxas, tr) {
            if (!Array.isArray(taxas)) {
                taxas = [taxas];
            }

            let cont = 1;
            taxas.forEach(function (taxa) {
                if (cont > 1) {
                    return;
                }

                tr.append(novaTaxa(taxa, tr));

                cont++;
            });
        }

        $('#novoPeso').click(function () {
            if ($('#zonas-ids').val().length <= 0) {
                alert('Selecione pelo menos um bairro');
            } else {
                const taxas = [];
                $('#zonas-ids option:selected').each(function (idx, option) {
                    taxas.push({
                        id: null,
                        zona_id: $(option).val(),
                        valor: null,
                        tempo_estimado: null,
                        zona: {
                            id: $(option).val(),
                            nome: $(option).text(),
                        },
                    });
                });

                console.log(taxas);

                novoPeso({
                    id: null,
                    peso_minimo: null,
                    quilo_adicional: false,
                    peso_maximo: null,
                    taxas: taxas,
                });
            }
        });

        $('#novoQuilo').click(function () {
            if ($('#zonas-ids').val().length <= 0) {
                alert('Selecione pelo menos um bairro');
            } else {
                const taxas = [];
                $('#zonas-ids option:selected').each(function (idx, option) {
                    taxas.push({
                        id: null,
                        zona_id: $(option).val(),
                        valor: null,
                        tempo_estimado: null,
                        zona: {
                            id: $(option).val(),
                            nome: $(option).text(),
                        },
                    });
                });

                novoPeso({
                    id: null,
                    peso_minimo: null,
                    quilo_adicional: true,
                    peso_maximo: null,
                    taxas: taxas,
                });

                $(this).attr('disabled', true);
            }
        });

        <?php foreach ($pesos as $peso) { ?>
        novoPeso(<?php echo $peso ?>);
        <?php } ?>

        verificaFaixas();
    });

    function verificaFaixas() {
        if ($("#pesos").find('tr[data-quilo="false"]').length === 0) {
            $('#novoQuilo').attr('disabled', true);
        } else {
            $('#novoQuilo').attr('disabled', false);
        }
    }

    function remover(i) {
        const tr = $(`#tr-${i}`);
        tr.remove();
        verificaFaixas();
    }
</script>
