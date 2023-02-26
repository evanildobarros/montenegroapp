function initTooltip() {
    $('[data-toggle="tooltip"]').tooltip();
}

$(() => {
    initTooltip();
});

window.initTooltip = initTooltip;
