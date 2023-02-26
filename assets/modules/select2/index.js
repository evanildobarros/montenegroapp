import 'select2';
import ptBRSelect2 from 'select2/src/js/select2/i18n/pt-BR';

function initSelect2(selector = 'select:not(.select2ajax, .notSelect2)') {
    $(selector).select2({
        language: ptBRSelect2,
        theme: 'bootstrap4',
    });
}

$(() => {
    initSelect2();
});

window.initSelect2 = initSelect2;
