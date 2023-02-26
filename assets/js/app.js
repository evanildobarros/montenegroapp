import 'bootstrap';
import '@fortawesome/fontawesome-free/js/all';
import 'admin-lte';
import 'overlayscrollbars/js/jquery.overlayScrollbars';
import _ from 'lodash';
import Popper from 'popper.js';
import axios from 'axios';
import Sortable from 'sortablejs';
import './funcoes';
import './../modules/table_buttons';
import './../modules/modal';
import './../modules/mapa';
import './../modules/select2';
import './../modules/inputmask';
import './../modules/iconpicker';
import './../modules/tooltips';

import (/* webpackChunkName: "datetimepicker" */ './../modules/datetimepicker');
import (/* webpackChunkName: "tinymce" */ './../modules/tinymce');
import (/* webpackChunkName: "inputmask" */ './../modules/fileinput');

window._ = _;
window.Popper = Popper;
window.$ = window.jQuery = require('jquery');

/**
 * Set CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = axios;
window.Sortable = Sortable;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';
