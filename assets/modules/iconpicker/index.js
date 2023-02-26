import './iconpicker';
import './fontawesome-iconpicker';

//fontawesome
function fontawesome() {
    $('.icp-auto').iconpicker();

    $('.icp-opts').iconpicker({
        defaultValue: false,
        selectedCustomClass: 'label label-success',
        hideOnSelect: false,
        mustAccept: false,
        placement: 'bottomRight',
        showFooter: false,
        templates: {
            search: '<input type="search" class="form-control iconpicker-search" placeholder="Pesquise aqui..." />',
        }
    }).data('iconpicker');
}

$(() => {
    fontawesome();
});

window.fontawesome = fontawesome;
