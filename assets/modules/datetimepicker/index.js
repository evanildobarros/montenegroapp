import 'tempusdominus-bootstrap-4';

$.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
  locale: 'pt-br',
  buttons: {
    showToday: true,
    showClear: true,
    showClose: true,
  },
  ignoreReadonly: true,
  icons: {
    time: 'fas fa-clock',
    date: 'fas fa-calendar',
    up: 'fas fa-arrow-up',
    down: 'fas fa-arrow-down',
    previous: 'fas fa-chevron-left',
    next: 'fas fa-chevron-right',
    today: 'fas fa-calendar-check',
    clear: 'fas fa-trash',
    close: 'fas fa-times',
  },
  keepInvalid: true,
});

function initDateTimePicker() {
  $('[data-datetimepicker]').datetimepicker({
    format: 'DD/MM/YYYY HH:mm:ss',
  });
  $('[data-datepicker]').datetimepicker({
    format: 'DD/MM/YYYY',
  });
  $('[data-timepicker]').datetimepicker({
    format: 'HH:mm:ss',
  });
}

$(() => {
  initDateTimePicker();
});

window.initDateTimePicker = initDateTimePicker;
