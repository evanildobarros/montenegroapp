import $ from 'jquery';

class Modal {
    constructor(id) {
        this.header = $('<div>').addClass('modal-header');
        this.body = $('<div>').addClass('modal-body');
        this.footer = $('<div>').addClass('modal-footer');

        this.modal = $('<div>').attr({
            class: 'modal fade',
            id: 'modal' + id,
            tabindex: '-1',
            role: 'dialog',
            'aria-labelledby': id + 'label',
            'aria-hidden': 'true'
        });

        const modal = this.modal;

        this.setHeaderText('Selecionar opções', id);
        this.footer
            .append($('<button>').attr({
                type: 'button',
                class: 'btn btn-secondary',
            }).click(function () {
                modal.modal('hide');
            }).text('Fechar'));

        const modalDialog = $('<div>').attr({
            role: 'document',
            class: 'modal-dialog modal-lg'
        });

        const modalContent = $('<div>').attr({
            class: 'modal-content'
        });
        modalContent
            .append(this.header)
            .append(this.body)
            .append(this.footer);

        modalDialog.append(modalContent);

        this.modal.append(modalDialog);
    }

    setHeaderText(text, id) {
        const modal = this.modal;
        const h5Header = $('<h5>')
            .attr({
                class: 'modal-title',
                id: id + 'label'
            })
            .text(text);
        const buttonFecharHeader = $('<button>')
            .attr({
                type: 'button',
                class: 'close',
                'aria-label': 'Close'
            })
            .click(function () {
                modal.modal('hide');
            })
            .append($('<span>').attr('aria-hidden', 'true').html('&times;'));

        this.header
            .empty()
            .append(h5Header)
            .append(buttonFecharHeader);
    }
}

window.Modal = Modal;
