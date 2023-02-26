function checkbox(input) {
    if (input.is(':checked')) {
        input
            .parent()
            .parent()
            .parent()
            .find('strong')
            .text(input.data('text-checked'))
            .removeClass('text-danger')
            .addClass('text-success');
    } else {
        input
            .parent()
            .parent()
            .parent()
            .find('strong')
            .text(input.data('text-unchecked'))
            .removeClass('text-success')
            .addClass('text-danger');
    }
}
module.exports = checkbox;
window.checkbox = checkbox;
