import $ from 'jquery';
import Inputmask from 'inputmask';

Inputmask.extendAliases({
  datetime: {
    inputFormat: 'dd/mm/yyyy hh:MM:ss',
  },
  date: {
    alias: 'datetime',
    inputFormat: 'dd/mm/yyyy',
  },
  time: {
    alias: 'datetime',
    inputFormat: 'hh:MM',
  },
  currency: {
    prefix: 'R$ ',
    groupSeparator: '.',
    autoGroup: true,
    digits: 2,
    radixPoint: ',',
    digitsOptional: false,
    allowMinus: false,
    removeMaskOnSubmit: true,
    unmaskAsNumber: true,
    numericInput: true,
  },
  numeric: {
    numericInput: true,
  },
  decimal: {
    digits: 2,
    digitsOptional: false,
  },
  integer: {
    digits: 0,
    numericInput: false,
  },
  percentage: {
    digits: 2,
    digitsOptional: true,
    numericInput: false,
    max: 100.00,
    min: 0.00,
    suffix: '',
  },
});

function initInputMask() {
    const input = document.querySelectorAll('input');

    input.forEach((element) => {
        if (!element.inputmask) {
            Inputmask().mask(element);
        }
    });
}

function unmask(selector) {
  selector.get().forEach((element) => {
    element.inputmask.remove();
  });
}

function mask(selector) {
  selector.get().forEach((element) => {
    Inputmask().mask(element);
  });
}

$(() => {
  initInputMask();
});

window.initInputMask = initInputMask;
window.unmask = unmask;
window.mask = mask;
