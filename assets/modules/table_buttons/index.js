function randomString(len) {
  const str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
  let randomStr = '';
  for (let i = 0; i < len; i++) {
    const min = 0;
    const max = str.length - 1;
    randomStr += Math.floor(Math.random() * (max - min + 1) + min);
  }
  return randomStr;
}

function Botao(url, glyphicon, label) {
  this.url = url;
  this.glyphicon = glyphicon;
  this.label = label;
  this.finalUrl = '';

  this.setIdentifier = function (identifier) {
    this.finalUrl = this.url.replace('{identifier}', identifier);
  };

  this.getHtml = function () {
    const i = $('<i>').attr({
      class: this.glyphicon,
    });

    const a = $('<a>').attr({
      href: this.finalUrl,
      title: this.label,
      class: `btn ${this.label == 'Editar' ? 'btn-warning' : 'btn-primary'} btn-sm`,
    }).append(i, ` ${this.label}`);

    return $('<div>').attr({
      class: 'mr-1 buttonsTableButtons',
    }).append(a);
  };
}

function BotaoTarget(url, glyphicon, label) {
  this.url = url;
  this.glyphicon = glyphicon;
  this.label = label;
  this.finalUrl = '';

  this.setIdentifier = function (identifier) {
    this.finalUrl = this.url.replace('{identifier}', identifier);
  };

  this.getHtml = function () {
    const i = $('<i>').attr({
      class: this.glyphicon,
    });

    const a = $('<a>').attr({
      href: this.finalUrl,
      title: this.label,
      target: '_blank',
      class: 'btn btn-default btn-sm',
    }).append(i, ` ${this.label}`);

    return $('<div>').attr({
      class: 'mr-1 buttonsTableButtons',
    }).append(a);
  };
}

function BotaoComponent(url, glyphicon, label, classButton) {
    this.url = url;
    this.glyphicon = glyphicon;
    this.label = label;
    this.finalUrl = '';

    this.setIdentifier = function (identifier) {
        this.finalUrl = this.url.replace('{identifier}', identifier);
    };

    this.getHtml = function () {
        const i = $('<i>').attr({
            class: this.glyphicon,
        });

        const a = $('<button>').attr({
            type: 'button',
            'data-ids': this.finalUrl,
            onclick: 'buttonClick($(this))',
            class: `btn btn-sm ${classButton}`,
        }).append(i, ` ${this.label}`);

        return $('<div>').attr({
            class: 'mr-1 buttonsTableButtons',
        }).append(a);
    };
}

function FormLink(url, glyphicon, label, csrfToken) {
  Botao.call(this, url, glyphicon, label);

  this.getHtml = function () {
    const inputDelete = $('<input>').attr({
      type: 'hidden',
      name: '_method',
      value: 'POST',
    });
    const randNum = randomString(10);
    const formDelete = $('<form>').attr({
      action: this.finalUrl,
      name: `post_${randNum}`,
      id: `post_${randNum}`,
      style: 'display:none;',
      method: 'post',
    }).append(inputDelete);

    if (typeof csrfToken !== 'undefined') {
      const inputCsrf = $('<input>').attr({
        type: 'hidden',
        name: '_csrfToken',
        autocomplete: 'off',
        value: csrfToken,
      });
      formDelete.append(inputCsrf);
    }

    const spanDelete = $('<i>').attr({
      class: 'fa fa-trash',
    });
    const linkDelete = $('<a>').attr({
      href: '#',
      onclick: `if (confirm('Tem certeza que deseja excluir?')) { document.post_${randNum}.submit(); } event.returnValue = false; return false;`,
      title: this.label,
      class: 'btn btn-danger btn-sm',
    }).append(spanDelete, ' Excluir');
    return $('<div>').attr({
      class: 'mr-1 buttonsTableButtons',
    }).append(formDelete).append(linkDelete);
  };
}

(function ($) {
  $.fn.tableButtons = function (options) {
    const settings = $.extend({
      rowsElement: 'tbody tr',
      headerElement: 'thead tr',
      checkboxName: 'identifier',
      highlightClass: 'active',
      buttonsElement: '.btn-toolbar.card-tools',
      individualLinks: {},
      multipleLinks: {},
      joinCharacter: '|',
    }, options);

    const rows = $(this).find(settings.rowsElement);

    const checkAll = $('<input>').attr({
      type: 'checkbox',
    }).on('click', function () {
      const checked = $(this).is(':checked');

      rows.each((idx, element) => {
        $(element).find('td').eq(0).find('input[type=checkbox]')
          .trigger('click');
      });
    });
    const th = $('<th>').attr({
      scope: 'col',
      class: 'text-center',
    }).append(checkAll);
    $(this).find(settings.headerElement).prepend(th);

    rows.each((idx, element) => {
      const hiddenCheckbox = $('<input>').attr({
        type: 'hidden',
        name: settings.checkboxName,
        value: '0',
      });
      const checkbox = $('<input>').attr({
        type: 'checkbox',
        name: settings.checkboxName,
        value: $(element).data('identifier'),
      });
      const td = $('<td>').attr({
        class: 'text-center',
      }).append(hiddenCheckbox).append(checkbox);
      $(element).prepend(td);
    });

    rows.on('click', function (e) {
      if ($(e.target).attr('name') === 'identifier') {
        const row = $(this);

        let checked;
        if (!row.hasClass(settings.highlightClass)) {
          row.addClass(settings.highlightClass);
          checked = true;
        } else {
          row.removeClass(settings.highlightClass);
          checked = false;
        }
        row.find('td').eq(0).find('input[type=checkbox]').prop('checked', checked);

        $(`${settings.buttonsElement} .buttonsTableButtons`).remove();

        const ids = [];
        rows.each((idx, element) => {
          if ($(element).hasClass(settings.highlightClass)) {
            if (typeof $(element).data('identifier') !== 'undefined') {
              ids.push($(element).data('identifier'));
            } else {
              throw 'A tr deve conter o atributo data-identifier!';
            }
          }
        });

        if (ids.length === 1) {
          for (let i = 0; i < settings.individualLinks.length; i++) {
            if (typeof settings.individualLinks[i].setIdentifier !== 'function') {
              throw `O objeto ${settings.individualLinks[i].constructor.name} não implementa o método setIdentifier()`;
            }
            if (typeof settings.individualLinks[i].getHtml !== 'function') {
              throw `O objeto ${settings.individualLinks[i].constructor.name} não implementa o método getHtml()`;
            }

            settings.individualLinks[i].setIdentifier(ids[0]);
            $(settings.buttonsElement).prepend(settings.individualLinks[i].getHtml());
          }
        } else if (ids.length > 1) {
          for (let x = 0; x < settings.multipleLinks.length; x++) {
            if (typeof settings.multipleLinks[x].setIdentifier !== 'function') {
              throw `O objeto ${settings.multipleLinks[x].constructor.name} não implementa o método setIdentifier()`;
            }
            if (typeof settings.multipleLinks[x].getHtml !== 'function') {
              throw `O objeto ${settings.multipleLinks[x].constructor.name} não implementa o método getHtml()`;
            }

            settings.multipleLinks[x].setIdentifier(ids.join(settings.joinCharacter));
            $(settings.buttonsElement).prepend(settings.multipleLinks[x].getHtml());
          }
        }
      }
    });

    return this;
  };
}(jQuery));

window.Botao = Botao;
window.FormLink = FormLink;
window.BotaoTarget = BotaoTarget;
window.BotaoComponent = BotaoComponent;
